<?php

use App\Http\Controllers\Auth\VerifikasiOtpController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('otp')->name('otp.')->group(function () {
    Route::post('/kirim', [VerifikasiOtpController::class, 'kirim'])->name('kirim');
    Route::get('/verifikasi', [VerifikasiOtpController::class, 'verifikasi'])->name('verifikasi');
    Route::post('/verifikasi', [VerifikasiOtpController::class, 'prosesVerifikasi'])->name('verifikasi.proses');
    Route::get('/ganti-email', [VerifikasiOtpController::class, 'formGantiEmail'])->name('ganti-email');
    Route::put('/ganti-email', [VerifikasiOtpController::class, 'simpanEmailBaru'])->name('ganti-email.simpan');
    Route::get('/ganti-password', [VerifikasiOtpController::class, 'formGantiPassword'])->name('ganti-password');
    Route::put('/ganti-password', [VerifikasiOtpController::class, 'simpanPasswordBaru'])->name('ganti-password.simpan');
});
