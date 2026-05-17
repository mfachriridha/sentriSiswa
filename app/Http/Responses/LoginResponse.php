<?php

namespace App\Http\Responses;

use App\Enums\UserRole;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        $role = $request->user()->role;

        return redirect(match ($role) {
            UserRole::Admin->value => route('admin.dashboard'),
            UserRole::Guru->value => route('guru.dashboard'),
            UserRole::Siswa->value => route('siswa.dashboard'),
            default => route('home'),
        });
    }
}
