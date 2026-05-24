<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsRegistered
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && ! $request->user()->isRegistered()) {
            auth()->logout();

            return redirect()->route('login')->with('error', 'Akun belum terdaftar. Silakan daftar terlebih dahulu.');
        }

        return $next($request);
    }
}
