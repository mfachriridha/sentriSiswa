<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfGoogleWhatsappPending
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && session('google_pending_whatsapp')) {
            if (! $request->routeIs('google.whatsapp', 'google.whatsapp.store', 'logout')) {
                return redirect()->route('google.whatsapp');
            }
        }

        return $next($request);
    }
}
