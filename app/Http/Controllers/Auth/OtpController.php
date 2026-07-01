<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OtpController extends Controller
{
    public function __construct(private readonly OtpService $otpService) {}

    public function show(): View|RedirectResponse
    {
        if (! session()->has('otp_type') || ! session()->has('otp_pending')) {
            return redirect()->route(Auth::user()->profilRouteName('edit'));
        }

        $pending = session('otp_pending', []);
        $emailToShow = $pending['new_email'] ?? Auth::user()->email ?? '';
        $maskedEmail = $emailToShow ? $this->maskEmail($emailToShow) : '***@***';

        return view('auth.verify-otp', compact('maskedEmail'));
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

        // Throttle: 1 resend per minute
        $cacheKey = "otp_resend_{$user->id}";
        if (cache()->has($cacheKey)) {
            return back()->withErrors(['otp' => 'Harap tunggu sebentar sebelum mengirim ulang kode.']);
        }

        cache()->put($cacheKey, true, 60);

        $this->otpService->generate($user, $type, $pending);

        return back()->with('success', 'Kode OTP baru telah dikirim.');
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = explode('@', $email);
        $masked = substr($local, 0, 2).str_repeat('*', max(0, strlen($local) - 2));

        return $masked.'@'.$domain;
    }
}
