<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsCounselor;
use App\Http\Middleware\EnsureUserIsGuru;
use App\Http\Middleware\EnsureUserIsHomeroom;
use App\Http\Middleware\EnsureUserIsRegistered;
use App\Http\Middleware\EnsureUserIsSiswa;
use App\Http\Middleware\EnsureUserIsStudentAffairs;
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
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'registered' => EnsureUserIsRegistered::class,
            'guru' => EnsureUserIsGuru::class,
            'bk' => EnsureUserIsCounselor::class,
            'kesiswaan' => EnsureUserIsStudentAffairs::class,
            'wali-kelas' => EnsureUserIsHomeroom::class,
            'siswa' => EnsureUserIsSiswa::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
