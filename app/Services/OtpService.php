<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\EmailOtpToken;
use App\Models\User;
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
    public function generate(User $user, string $type, array $pending = []): EmailOtpToken
    {
        // Invalidate any existing unused OTPs of this type for the user
        EmailOtpToken::where('user_id', $user->id)
            ->where('type', $type)
            ->whereNull('used_at')
            ->delete();

        $rawOtp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $token = EmailOtpToken::create([
            'user_id' => $user->id,
            'otp' => Hash::make($rawOtp),
            'type' => $type,
            'new_email' => $pending['new_email'] ?? null,
            'new_password' => isset($pending['new_password'])
                ? Hash::make($pending['new_password'])
                : null,
            'expires_at' => now()->addMinutes(self::EXPIRY_MINUTES),
        ]);

        // Determine the recipient: for admin_setup the OTP goes to new_email (verifying it's real)
        $recipient = ($type === 'admin_setup' && isset($pending['new_email']))
            ? $pending['new_email']
            : $user->email;

        Mail::to($recipient)->send(new OtpMail($rawOtp, self::EXPIRY_MINUTES));

        return $token;
    }

    /**
     * Verify a raw OTP against the latest valid token for the user and type.
     */
    public function verify(User $user, string $rawOtp, string $type): ?EmailOtpToken
    {
        $token = EmailOtpToken::where('user_id', $user->id)
            ->where('type', $type)
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
    public function applyChange(User $user, EmailOtpToken $token): void
    {
        $token->update(['used_at' => now()]);

        $updates = [];

        if ($token->new_email) {
            $updates['email'] = $token->new_email;
            $updates['email_verified_at'] = now();
        }

        if ($token->new_password) {
            $updates['password'] = $token->new_password;
        }

        if (! empty($updates)) {
            $user->update($updates);
        }
    }
}
