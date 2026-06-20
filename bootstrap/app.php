<?php

use App\Http\Middleware\EnsurePeranAdmin;
use App\Http\Middleware\EnsurePeranBk;
use App\Http\Middleware\EnsurePeranKesiswaan;
use App\Http\Middleware\EnsurePeranSiswa;
use App\Http\Middleware\EnsurePeranWaliKelas;
use App\Http\Middleware\EnsureSudahDaftar;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->encryptCookies(except: ['sidebar_state']);

        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->alias([
            'admin' => EnsurePeranAdmin::class,
            'wali_kelas' => EnsurePeranWaliKelas::class,
            'bk' => EnsurePeranBk::class,
            'kesiswaan' => EnsurePeranKesiswaan::class,
            'siswa' => EnsurePeranSiswa::class,
            'terdaftar' => EnsureSudahDaftar::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
