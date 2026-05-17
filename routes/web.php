<?php

use App\Enums\UserRole;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;

        return redirect(match ($role) {
            UserRole::Admin->value => '/admin/dashboard',
            UserRole::Guru->value => '/guru/dashboard',
            UserRole::Siswa->value => '/siswa/dashboard',
            default => '/',
        });
    })->name('dashboard');
});

require __DIR__.'/admin.php';
require __DIR__.'/guru.php';
require __DIR__.'/siswa.php';
