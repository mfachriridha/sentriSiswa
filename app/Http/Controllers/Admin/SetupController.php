<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SetupController extends Controller
{
    public function __construct(private readonly OtpService $otpService) {}

    public function create(): View|RedirectResponse
    {
        if (! Auth::user()->needsAdminSetup()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.setup');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.unique' => 'Email ini sudah digunakan. Gunakan email lain.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $admin = Auth::user();

        // Store pending changes in session
        session([
            'otp_type' => 'admin_setup',
            'otp_pending' => [
                'new_email' => $request->email,
                'new_password' => $request->password,
            ],
        ]);

        // OTP sent to the NEW email to verify it's real and accessible
        $this->otpService->generate($admin, 'admin_setup', [
            'new_email' => $request->email,
            'new_password' => $request->password,
        ]);

        return redirect()->route('admin.setup.verify')
            ->with('success', 'Kode verifikasi telah dikirim ke '.$request->email);
    }

    public function verifyOtp(): View|RedirectResponse
    {
        if (! Auth::user()->needsAdminSetup()) {
            return redirect()->route('admin.dashboard');
        }

        if (! session()->has('otp_type')) {
            return redirect()->route('admin.setup');
        }

        $maskedEmail = $this->maskEmail(session('otp_pending.new_email', ''));

        return view('admin.setup-verify-otp', compact('maskedEmail'));
    }

    public function confirmOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ], [
            'otp.required' => 'Masukkan kode OTP terlebih dahulu.',
            'otp.digits' => 'Kode OTP harus 6 digit angka.',
        ]);

        $admin = Auth::user();
        $token = $this->otpService->verify($admin, $request->otp, 'admin_setup');

        if (! $token) {
            return back()->withErrors(['otp' => 'Kode OTP salah atau sudah kedaluwarsa.']);
        }

        $this->otpService->applyChange($admin, $token);
        session()->forget(['otp_type', 'otp_pending']);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Akun berhasil diperbarui. Selamat datang, Admin!');
    }

    public function resendOtp(): RedirectResponse
    {
        $admin = Auth::user();
        $pending = session('otp_pending', []);

        if (! isset($pending['new_email'])) {
            return redirect()->route('admin.setup');
        }

        $cacheKey = "otp_resend_{$admin->id}";
        if (cache()->has($cacheKey)) {
            return back()->withErrors(['otp' => 'Harap tunggu sebentar sebelum mengirim ulang kode.']);
        }

        cache()->put($cacheKey, true, 60);

        $this->otpService->generate($admin, 'admin_setup', $pending);

        return back()->with('success', 'Kode OTP baru telah dikirim ke '.$pending['new_email']);
    }

    private function maskEmail(string $email): string
    {
        if (! str_contains($email, '@')) {
            return $email;
        }

        [$local, $domain] = explode('@', $email);
        $masked = substr($local, 0, 2).str_repeat('*', max(0, strlen($local) - 2));

        return $masked.'@'.$domain;
    }
}
