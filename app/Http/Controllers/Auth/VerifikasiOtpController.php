<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\GantiEmailRequest;
use App\Http\Requests\Auth\GantiPasswordRequest;
use App\Http\Requests\Auth\KirimOtpRequest;
use App\Http\Requests\Auth\VerifikasiOtpRequest;
use App\Mail\KodeOtpMail;
use App\Services\OtpGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class VerifikasiOtpController extends Controller
{
    public function __construct(
        private readonly OtpGenerator $otpGenerator
    ) {}

    public function kirim(KirimOtpRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $tujuan = $request->tujuan;

        if ($this->otpGenerator->tooManyRequests($user->id)) {
            return back()->withErrors(['otp' => 'Terlalu banyak permintaan. Silakan coba lagi nanti.']);
        }

        $otp = $this->otpGenerator->generate($user->id);

        $tujuanLabel = $tujuan === 'ganti_email' ? 'pergantian email' : 'pergantian kata sandi';

        Mail::to($user->email)->send(new KodeOtpMail($otp, $tujuanLabel));

        $this->otpGenerator->hitThrottle($user->id);

        session(['otp_tujuan' => $tujuan]);

        return redirect()->route('otp.verifikasi');
    }

    public function verifikasi(): Response|RedirectResponse
    {
        $user = Auth::user();
        $tujuan = session('otp_tujuan');

        if (! $tujuan) {
            return redirect()->route('dashboard');
        }

        if ($this->otpGenerator->isLockedOut($user->id)) {
            return back()->withErrors(['otp' => 'Terlalu banyak percobaan. Silakan minta kode baru.']);
        }

        return Inertia::render('auth/VerifikasiOtp', [
            'tujuan' => $tujuan,
            'sisaPercobaan' => $this->otpGenerator->remainingAttempts($user->id),
        ]);
    }

    public function prosesVerifikasi(VerifikasiOtpRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $tujuan = session('otp_tujuan');

        if (! $tujuan) {
            return redirect()->route('dashboard');
        }

        if (! $this->otpGenerator->verify($user->id, $request->otp)) {
            if ($this->otpGenerator->isLockedOut($user->id)) {
                return back()->withErrors(['otp' => 'Terlalu banyak percobaan. Silakan minta kode baru.']);
            }

            return back()->withErrors(['otp' => 'Kode OTP tidak valid.'])->withInput();
        }

        session(['otp_verified' => true]);

        $route = $tujuan === 'ganti_email' ? 'otp.ganti-email' : 'otp.ganti-password';

        return redirect()->route($route);
    }

    public function formGantiEmail(): Response|RedirectResponse
    {
        if (! $this->isOtpVerified()) {
            return redirect()->route('otp.verifikasi');
        }

        return Inertia::render('auth/GantiEmail');
    }

    public function simpanEmailBaru(GantiEmailRequest $request): RedirectResponse
    {
        if (! $this->isOtpVerified()) {
            return redirect()->route('otp.verifikasi');
        }

        $user = Auth::user();
        $user->update([
            'email' => $request->email,
            'email_verified_at' => null,
        ]);

        $this->otpGenerator->clear($user->id);
        session()->forget(['otp_tujuan', 'otp_verified']);

        return redirect()->route('dashboard')->with('success', 'Email berhasil diubah.');
    }

    public function formGantiPassword(): Response|RedirectResponse
    {
        if (! $this->isOtpVerified()) {
            return redirect()->route('otp.verifikasi');
        }

        return Inertia::render('auth/GantiPassword');
    }

    public function simpanPasswordBaru(GantiPasswordRequest $request): RedirectResponse
    {
        if (! $this->isOtpVerified()) {
            return redirect()->route('otp.verifikasi');
        }

        $user = Auth::user();
        $user->update([
            'password' => bcrypt($request->password),
        ]);

        $this->otpGenerator->clear($user->id);
        session()->forget(['otp_tujuan', 'otp_verified']);

        return redirect()->route('dashboard')->with('success', 'Kata sandi berhasil diubah.');
    }

    private function isOtpVerified(): bool
    {
        return session('otp_verified') === true;
    }
}
