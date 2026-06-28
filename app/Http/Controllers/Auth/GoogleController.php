<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        // Store the mode (login vs register) in session before redirecting
        session(['google_oauth_mode' => $request->input('mode', 'login')]);

        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Autentikasi Google gagal. Silakan coba lagi.']);
        }

        $mode = session('google_oauth_mode', 'login');
        session()->forget('google_oauth_mode');

        if ($mode === 'register') {
            return $this->handleRegister($googleUser);
        }

        return $this->handleLogin($googleUser);
    }

    private function handleRegister(\Laravel\Socialite\Contracts\User $googleUser): RedirectResponse
    {
        $role = session('register_role');
        $userId = session('register_user_id');

        if (! $role || ! $userId) {
            return redirect()->route('register')
                ->withErrors(['identity' => 'Sesi pendaftaran kedaluwarsa. Silakan ulangi verifikasi identitas.']);
        }

        // Check email not taken by another user
        $existing = User::where('email', $googleUser->getEmail())
            ->where('id', '!=', $userId)
            ->first();

        if ($existing) {
            return redirect()->route('register.step2')
                ->withErrors(['email' => 'Email Google ini sudah digunakan oleh akun lain.']);
        }

        $user = User::find($userId);

        if (! $user || $user->isRegistered()) {
            return redirect()->route('register')
                ->withErrors(['identity' => 'Akun sudah terdaftar atau tidak valid.']);
        }

        $user->update([
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'status' => 'registered',
            'email_verified_at' => now(),
        ]);

        session()->forget(['register_role', 'register_user_id', 'register_identity', 'register_name']);

        Auth::login($user);

        if ($role === 'teacher') {
            // Teacher needs to provide WhatsApp number
            session(['google_pending_whatsapp' => true]);

            return redirect()->route('google.whatsapp');
        }

        // Student goes straight to dashboard
        return redirect()->route($user->dashboardRouteName())
            ->with('success', 'Pendaftaran berhasil! Selamat datang di Sentri Siswa.');
    }

    private function handleLogin(\Laravel\Socialite\Contracts\User $googleUser): RedirectResponse
    {
        // Find by google_id first, then by email
        $user = User::where('google_id', $googleUser->getId())->first()
            ?? User::where('email', $googleUser->getEmail())->first();

        if (! $user) {
            return redirect()->route('register')
                ->with('error', 'Akun Google ini belum terdaftar di Sentri Siswa. Silakan daftar terlebih dahulu.');
        }

        if (! $user->isRegistered()) {
            return redirect()->route('register')
                ->with('error', 'Selesaikan pendaftaran akun Anda terlebih dahulu.');
        }

        // Link google_id if not linked yet (user registered via email, now logging in with Google)
        if (! $user->google_id) {
            $user->update(['google_id' => $googleUser->getId()]);
        }

        Auth::login($user);

        return redirect()->route($user->dashboardRouteName());
    }
}
