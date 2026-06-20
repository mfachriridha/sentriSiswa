<?php

use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/daftar', [RegisterController::class, 'create'])->name('daftar');
    Route::post('/daftar/verifikasi', [RegisterController::class, 'verify'])->name('daftar.verifikasi');
    Route::get('/daftar/lengkapi', [RegisterController::class, 'showForm'])->name('daftar.lengkapi');
    Route::post('/daftar', [RegisterController::class, 'store'])->name('daftar.simpan');
});
