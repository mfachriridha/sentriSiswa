<?php

use App\Http\Middleware\CheckRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => CheckRole::class,
        ]);

        $middleware->redirectGuestsTo(fn () => route('auth.login'));

        $middleware->validateCsrfTokens(except: [
            'auth/register/validate-nip',
            'auth/register/validate-nis',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
