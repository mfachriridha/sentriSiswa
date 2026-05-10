<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showSiswaLogin()
    {
        return view('auth.login');
    }

    public function showAdminLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request, string $expectedRole)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials['role'] = $expectedRole;

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();
            Session::put('user_role', $user->role);
            Session::put('user_name', $user->name);
            Session::put('user_email', $user->email);

            return redirect()->intended(
                $user->role === 'admin'
                    ? route('admin.dashboard')
                    : route('siswa.dashboard')
            );
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
        Session::flush();

        return redirect()->route('landing');
    }
}