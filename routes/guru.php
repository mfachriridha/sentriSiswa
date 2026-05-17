<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('guru')->name('guru.')->group(function () {
    Route::view('/dashboard', 'pages.guru.dashboard')->name('dashboard');
    Route::view('/absensi', 'pages.guru.absensi')->name('absensi');
    Route::view('/monitoring', 'pages.guru.monitoring')->name('monitoring');
    Route::view('/pengajuan-poin', 'pages.guru.pengajuan-poin')->name('pengajuan-poin');
    Route::view('/profile', 'pages.guru.profile')->name('profile');
});
