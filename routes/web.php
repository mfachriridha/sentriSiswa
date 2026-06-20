<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware(['auth', 'terdaftar'])->group(function () {
    Route::get('/dashboard', function () {
        return match (auth()->user()->peran) {
            'admin' => redirect()->route('admin.dashboard'),
            'wali_kelas' => redirect()->route('wali-kelas.dashboard'),
            'bk' => redirect()->route('bk.dashboard'),
            'kesiswaan' => redirect()->route('kesiswaan.dashboard'),
            'siswa' => redirect()->route('siswa.dashboard'),
            default => redirect()->route('login'),
        };
    })->name('dashboard');

    // Dashboard sementara untuk role lain (akan dipindah ke controller masing-masing)
    Route::middleware('wali_kelas')->get('/wali-kelas/dashboard', fn () => Inertia\Inertia::render('wali-kelas/Dashboard'))->name('wali-kelas.dashboard');
    Route::middleware('bk')->get('/bk/dashboard', fn () => Inertia\Inertia::render('bk/Dashboard'))->name('bk.dashboard');
    Route::middleware('kesiswaan')->get('/kesiswaan/dashboard', fn () => Inertia\Inertia::render('kesiswaan/Dashboard'))->name('kesiswaan.dashboard');
    Route::middleware('siswa')->get('/siswa/dashboard', fn () => Inertia\Inertia::render('siswa/Dashboard'))->name('siswa.dashboard');
});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/otp.php';
