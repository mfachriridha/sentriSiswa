<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('/dashboard', 'pages.admin.dashboard')->name('dashboard');
    Route::view('/kelas', 'pages.admin.kelas')->name('kelas');
    Route::view('/siswa', 'pages.admin.siswa')->name('siswa');
    Route::view('/guru', 'pages.admin.guru')->name('guru');
    Route::view('/import', 'pages.admin.import')->name('import');
    Route::view('/poin-pelanggaran', 'pages.admin.poin-pelanggaran')->name('poin-pelanggaran');
    Route::view('/catat-pelanggaran', 'pages.admin.catat-pelanggaran')->name('catat-pelanggaran');
    Route::view('/pengajuan-poin', 'pages.admin.pengajuan-poin')->name('pengajuan-poin');
    Route::view('/tata-tertib', 'pages.admin.tata-tertib')->name('tata-tertib');
    Route::view('/absensi', 'pages.admin.absensi')->name('absensi');
    Route::view('/konfigurasi', 'pages.admin.konfigurasi')->name('konfigurasi');
});
