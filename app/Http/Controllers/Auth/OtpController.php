<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OtpController extends Controller
{
    /** Jeda minimum antar kirim ulang OTP. */
    public const RESEND_COOLDOWN_SECONDS = 60;

    public function __construct(private readonly OtpService $otpService) {}

    public function show(): View|RedirectResponse
    {
        if (! session()->has('otp_type') || ! session()->has('otp_pending')) {
            return redirect()->route(Auth::user()->profilRouteName('edit'));
        }

        $pending = session('otp_pending', []);
        $emailToShow = $pending['new_email'] ?? Auth::user()->email ?? '';
        $maskedEmail = $emailToShow ? $this->maskEmail($emailToShow) : '***@***';
        $resendAvailableIn = $this->resendAvailableIn(Auth::user());

        return view('auth.verify-otp', compact('maskedEmail', 'resendAvailableIn'));
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ], [
            'otp.required' => 'Masukkan kode OTP terlebih dahulu.',
            'otp.digits' => 'Kode OTP harus 6 digit angka.',
        ]);

        $user = Auth::user();
        $type = session('otp_type');

        if (! $type) {
            return redirect()->route($user->profilRouteName('edit'))
                ->withErrors(['otp' => 'Sesi OTP tidak valid. Silakan coba lagi.']);
        }

        $token = $this->otpService->verify($user, $request->otp, $type);

        if (! $token) {
            return back()->withErrors(['otp' => 'Kode OTP salah atau sudah kedaluwarsa.']);
        }

        $pending = session('otp_pending', []);

        if ($type === 'password_change' && empty($pending['new_password'])) {
            $token->update(['digunakan_pada' => now()]);
            session()->forget(['otp_type', 'otp_pending']);
            session(['password_change_verified' => true]);

            return redirect()->route($user->profilRouteName('set-sandi-baru'))
                ->with('success', 'OTP berhasil diverifikasi. Masukkan kata sandi baru Anda.');
        }

        $this->otpService->applyChange($user, $token);

        session()->forget(['otp_type', 'otp_pending']);

        return redirect()->route($user->profilRouteName())
            ->with('success', 'Perubahan berhasil disimpan.');
    }

    public function resend(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $type = session('otp_type');
        $pending = session('otp_pending', []);

        if (! $type) {
            return redirect()->route($user->profilRouteName('edit'))
                ->withErrors(['otp' => 'Sesi OTP tidak valid. Silakan coba lagi.']);
        }

        $remaining = $this->resendAvailableIn($user);

        if ($remaining > 0) {
            return back()->withErrors([
                'otp' => "Tunggu {$remaining} detik lagi sebelum mengirim ulang kode.",
            ]);
        }

        // Simpan waktu kapan boleh kirim ulang lagi (bukan sekadar penanda), biar
        // sisa detiknya bisa dihitung ulang walau halaman di-refresh.
        cache()->put(
            $this->resendCacheKey($user),
            now()->addSeconds(self::RESEND_COOLDOWN_SECONDS)->timestamp,
            self::RESEND_COOLDOWN_SECONDS,
        );

        $this->otpService->generate($user, $type, $pending);

        return back()->with('success', 'Kode OTP baru telah dikirim.');
    }

    /** Sisa detik sebelum boleh kirim ulang; 0 kalau sudah boleh. */
    private function resendAvailableIn(Pengguna $user): int
    {
        $availableAt = cache()->get($this->resendCacheKey($user));

        if (! is_numeric($availableAt)) {
            return 0;
        }

        return max(0, (int) $availableAt - now()->timestamp);
    }

    private function resendCacheKey(Pengguna $user): string
    {
        return "otp_resend_{$user->id}";
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = explode('@', $email);
        $masked = substr($local, 0, 2).str_repeat('*', max(0, strlen($local) - 2));

        return $masked.'@'.$domain;
    }
}
