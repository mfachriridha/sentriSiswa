<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\Pengguna;
use App\Models\TokenOtp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    private const EXPIRY_MINUTES = 10;

    /**
     * Generate an OTP, store it, and send to the given email address.
     *
     * @param  array{new_email?: string, new_password?: string}  $pending
     */
    public function generate(Pengguna $user, string $type, array $pending = []): TokenOtp
    {
        TokenOtp::where('pengguna_id', $user->id)
            ->where('tipe', $type)
            ->whereNull('digunakan_pada')
            ->delete();

        $rawOtp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $token = TokenOtp::create([
            'pengguna_id' => $user->id,
            'otp' => Hash::make($rawOtp),
            'tipe' => $type,
            'email_baru' => $pending['new_email'] ?? null,
            'sandi_baru' => isset($pending['new_password'])
                ? Hash::make($pending['new_password'])
                : null,
            'kadaluwarsa_pada' => now()->addMinutes(self::EXPIRY_MINUTES),
        ]);

        $recipientEmail = $pending['new_email'] ?? $user->email;
        Mail::to($recipientEmail)->send(new OtpMail($rawOtp, self::EXPIRY_MINUTES));

        return $token;
    }

    /**
     * Verify a raw OTP against the latest valid token for the user and type.
     */
    public function verify(Pengguna $user, string $rawOtp, string $type): ?TokenOtp
    {
        $token = TokenOtp::where('pengguna_id', $user->id)
            ->where('tipe', $type)
            ->valid()
            ->latest()
            ->first();

        if (! $token || ! Hash::check($rawOtp, $token->otp)) {
            return null;
        }

        return $token;
    }

    /**
     * Mark a token as used and apply the pending credential change to the user.
     */
    public function applyChange(Pengguna $user, TokenOtp $token): void
    {
        $token->update(['digunakan_pada' => now()]);

        if ($token->email_baru) {
            $user->email = $token->email_baru;
            $user->email_verified_at = now();
        }

        if ($token->sandi_baru) {
            $user->password = $token->sandi_baru;
        }

        $user->save();
    }
}
