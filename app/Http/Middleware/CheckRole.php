<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (! Auth::check()) {
            return redirect()->route('auth.login');
        }

        if (! in_array(Auth::user()->role, $roles)) {
            return redirect()->route('landing')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        return $next($request);
    }
}
