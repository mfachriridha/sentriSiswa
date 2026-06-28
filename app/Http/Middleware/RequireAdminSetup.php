<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAdminSetup
{
    public function handle(Request $request, Closure $next): Response
    {
        // Feature bypassed per user feedback
        return $next($request);
    }
}
