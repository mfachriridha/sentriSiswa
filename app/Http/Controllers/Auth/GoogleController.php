<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
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

        if ($mode === 'link' && Auth::check()) {
            return $this->handleLink($googleUser);
        }

        if ($mode === 'register') {
            return $this->handleRegister($googleUser);
        }

        return $this->handleLogin($googleUser);
    }

    public function linkRedirect(): RedirectResponse
    {
        session(['google_oauth_mode' => 'link']);

        return Socialite::driver('google')->redirect();
    }

    private function handleLink(\Laravel\Socialite\Contracts\User $googleUser): RedirectResponse
    {
        $user = Auth::user();

        $taken = Pengguna::where('id_google', $googleUser->getId())
            ->where('id', '!=', $user->id)
            ->exists();

        if ($taken) {
            return redirect()->route($user->profilRouteName('edit'))
                ->withErrors(['google' => 'Akun Google ini sudah terhubung ke akun lain.']);
        }

        $user->update(['id_google' => $googleUser->getId()]);

        return redirect()->route($user->profilRouteName())
            ->with('success', 'Akun berhasil dihubungkan ke Google.');
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
        $existing = Pengguna::where('email', $googleUser->getEmail())
            ->where('id', '!=', $userId)
            ->first();

        if ($existing) {
            return redirect()->route('register.step2')
                ->withErrors(['email' => 'Email Google ini sudah digunakan oleh akun lain.']);
        }

        $user = Pengguna::find($userId);

        if (! $user || $user->isRegistered()) {
            return redirect()->route('register')
                ->withErrors(['identity' => 'Akun sudah terdaftar atau tidak valid.']);
        }

        $user->update([
            'email' => $googleUser->getEmail(),
            'id_google' => $googleUser->getId(),
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
        // Find by id_google first, then by email
        $user = Pengguna::where('id_google', $googleUser->getId())->first()
            ?? Pengguna::where('email', $googleUser->getEmail())->first();

        if (! $user) {
            return redirect()->route('register')
                ->with('error', 'Akun Google ini belum terdaftar di Sentri Siswa. Silakan daftar terlebih dahulu.');
        }

        if (! $user->isRegistered()) {
            return redirect()->route('register')
                ->with('error', 'Selesaikan pendaftaran akun Anda terlebih dahulu.');
        }

        // Link id_google if not linked yet (user registered via email, now logging in with Google)
        if (! $user->id_google) {
            $user->update(['id_google' => $googleUser->getId()]);
        }

        Auth::login($user);

        return redirect()->route($user->dashboardRouteName());
    }
}
