<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if (! $user->is_active && $user->role !== 'admin') {
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => 'Akun belum diaktivasi. Silakan lengkapi registrasi terlebih dahulu.',
                ]);
            }

            return redirect()->intended(match ($user->role) {
                'admin' => route('admin.dashboard'),
                'wali_kelas' => route('wali-kelas.dashboard'),
                'bk' => route('bk.dashboard'),
                default => route('siswa.dashboard'),
            });
        }

        throw ValidationException::withMessages([
            'email' => 'Email atau kata sandi salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing');
    }
}
