<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->role !== 'admin') {
            auth()->logout();

            return redirect()->route('login')->withErrors([
                'email' => 'Anda tidak memiliki akses admin.',
            ]);
        }

        return $next($request);
    }
}
