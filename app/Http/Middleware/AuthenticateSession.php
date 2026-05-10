<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateSession
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() && !session()->has('user_role')) {
            return redirect()->route('auth.login');
        }

        return $next($request);
    }
}