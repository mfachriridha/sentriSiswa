<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::view('/dashboard', 'pages.siswa.dashboard')->name('dashboard');
    Route::view('/riwayat', 'pages.siswa.riwayat')->name('riwayat');
    Route::view('/pelanggaran', 'pages.siswa.pelanggaran')->name('pelanggaran');
    Route::view('/tata-tertib', 'pages.siswa.tata-tertib')->name('tata-tertib');
    Route::view('/profile', 'pages.siswa.profile')->name('profile');
});
