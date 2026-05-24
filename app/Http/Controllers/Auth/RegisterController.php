<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\VerifyIdentityRequest;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function verify(VerifyIdentityRequest $request): RedirectResponse|View
    {
        $role = $request->role;
        $identity = $request->identity;

        if ($role === 'teacher') {
            $profile = TeacherProfile::where('nip', $identity)->first();
            if (! $profile) {
                return back()->withErrors(['identity' => 'NIP tidak ditemukan.'])->onlyInput('identity', 'role');
            }

            if ($profile->user && $profile->user->isRegistered()) {
                return back()->withErrors(['identity' => 'NIP sudah terdaftar. Silakan masuk.'])->onlyInput('identity', 'role');
            }
        } else {
            $profile = StudentProfile::where('nisn', $identity)
                ->orWhere('nis', $identity)
                ->first();

            if (! $profile) {
                return back()->withErrors(['identity' => 'NISN/NIS tidak ditemukan.'])->onlyInput('identity', 'role');
            }

            if ($profile->user && $profile->user->isRegistered()) {
                return back()->withErrors(['identity' => 'NISN/NIS sudah terdaftar. Silakan masuk.'])->onlyInput('identity', 'role');
            }
        }

        session([
            'register_role' => $role,
            'register_user_id' => $profile->user_id,
            'register_identity' => $identity,
        ]);

        return view('auth.register-step2', compact('role', 'identity'));
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $role = session('register_role');
        $userId = session('register_user_id');

        if (! $role || ! $userId) {
            return redirect()->route('register')->withErrors(['identity' => 'Sesi verifikasi kedaluwarsa. Silakan ulangi.']);
        }

        $user = User::find($userId);

        if (! $user || $user->isRegistered()) {
            return redirect()->route('register')->withErrors(['identity' => 'Akun sudah terdaftar atau tidak valid.']);
        }

        $user->update([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'registered',
        ]);

        if ($role === 'teacher') {
            $user->teacherProfile?->update([
                'phone' => $request->phone,
            ]);
        }

        session()->forget(['register_role', 'register_user_id', 'register_identity']);

        Auth::login($user);

        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'teacher' => redirect()->route('guru.dashboard'),
            'student' => redirect()->route('siswa.dashboard'),
            default => redirect()->route('login'),
        };
    }
}
