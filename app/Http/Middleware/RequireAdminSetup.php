<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireAdminSetup
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->needsAdminSetup()) {
            // Allow access to the setup routes themselves to avoid infinite redirect
            if ($request->routeIs('admin.setup', 'admin.setup.store', 'admin.setup.verify', 'admin.setup.verify.store', 'admin.setup.resend', 'logout')) {
                return $next($request);
            }

            return redirect()->route('admin.setup');
        }

        return $next($request);
    }
}
