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
        $mode = session('google_oauth_mode', 'login');

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception) {
            session()->forget(['google_oauth_mode', 'google_link_user_id']);

            if ($mode === 'link') {
                return redirect()->route('login')
                    ->withErrors(['email' => 'Autentikasi Google gagal. Masuk dulu, lalu coba hubungkan lagi dari halaman profil.']);
            }

            return redirect()->route('login')
                ->withErrors(['email' => 'Autentikasi Google gagal. Silakan coba lagi.']);
        }

        session()->forget('google_oauth_mode');

        if ($mode === 'link') {
            $user = Auth::user() ?? Pengguna::find(session('google_link_user_id'));
            session()->forget('google_link_user_id');

            if (! $user) {
                return redirect()->route('login')
                    ->withErrors(['email' => 'Sesi tautan Google kedaluwarsa. Masuk dulu, lalu coba lagi.']);
            }

            return $this->handleLink($googleUser, $user);
        }

        session()->forget('google_link_user_id');

        if ($mode === 'register') {
            return $this->handleRegister($googleUser);
        }

        return $this->handleLogin($googleUser);
    }

    public function linkRedirect(): RedirectResponse
    {
        /** @var \App\Models\Pengguna $user */
        $user = Auth::user();
        session([
            'google_oauth_mode' => 'link',
            'google_link_user_id' => $user->id,
        ]);

        return Socialite::driver('google')->redirect();
    }

    private function handleLink(\Laravel\Socialite\Contracts\User $googleUser, \App\Models\Pengguna $user): RedirectResponse
    {
        $taken = Pengguna::where('id_google', $googleUser->getId())
            ->where('id', '!=', $user->id)
            ->exists();

        if ($taken) {
            return redirect()->route($user->profilRouteName('edit'))
                ->withErrors(['google' => 'Akun Google ini sudah terhubung ke akun lain.']);
        }

        if (strcasecmp($googleUser->getEmail(), $user->email) !== 0) {
            return redirect()->route($user->profilRouteName('edit'))
                ->withErrors(['google' => 'Email akun Google ('.$googleUser->getEmail().') tidak sama dengan email akun Anda. Gunakan akun Google dengan email yang sama untuk menghubungkan.']);
        }

        $user->update(['id_google' => $googleUser->getId()]);

        return redirect()->route($user->profilRouteName())
            ->with('success', 'Akun berhasil dihubungkan ke Google.');
    }

    public function unlink(): RedirectResponse
    {
        /** @var Pengguna $user */
        $user = Auth::user();
        $user->update(['id_google' => null]);

        return redirect()->route($user->profilRouteName('edit'))
            ->with('success', 'Akun Google berhasil diputuskan.');
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
        session()->regenerate();

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
        session()->regenerate();

        return redirect()->route($user->dashboardRouteName());
    }
}
