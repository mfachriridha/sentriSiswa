<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\ImporGuruController;
use App\Http\Controllers\Admin\ImporSiswaController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\SiswaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'terdaftar', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Import Siswa
    Route::prefix('siswa')->name('siswa.')->group(function () {
        Route::get('impor', [ImporSiswaController::class, 'create'])->name('impor');
        Route::get('impor/template', [ImporSiswaController::class, 'template'])->name('impor.template');
        Route::post('impor/unggah', [ImporSiswaController::class, 'unggah'])->name('impor.unggah');
        Route::get('impor/pratinjau', [ImporSiswaController::class, 'pratinjau'])->name('impor.pratinjau');
        Route::post('impor', [ImporSiswaController::class, 'simpan'])->name('impor.simpan');
    });

    // Import Guru
    Route::prefix('guru')->name('guru.')->group(function () {
        Route::get('impor', [ImporGuruController::class, 'create'])->name('impor');
        Route::get('impor/template', [ImporGuruController::class, 'template'])->name('impor.template');
        Route::post('impor/unggah', [ImporGuruController::class, 'unggah'])->name('impor.unggah');
        Route::get('impor/pratinjau', [ImporGuruController::class, 'pratinjau'])->name('impor.pratinjau');
        Route::post('impor', [ImporGuruController::class, 'simpan'])->name('impor.simpan');
    });

    // Manajemen Siswa
    Route::resource('siswa', SiswaController::class)->parameters(['siswa' => 'siswa']);
    Route::get('siswa/{siswa}/biodata/edit', [SiswaController::class, 'editBiodata'])->name('siswa.biodata.edit');
    Route::put('siswa/{siswa}/biodata', [SiswaController::class, 'updateBiodata'])->name('siswa.biodata.update');
    Route::post('siswa/{siswa}/foto', [SiswaController::class, 'uploadFoto'])->name('siswa.foto.upload');
    Route::delete('siswa/{siswa}/foto', [SiswaController::class, 'hapusFoto'])->name('siswa.foto.hapus');
    Route::delete('siswa/hapus-semua', [SiswaController::class, 'hapusSemua'])->name('siswa.hapus-semua');

    // Manajemen Guru
    Route::resource('guru', GuruController::class)->parameters(['guru' => 'guru']);
    Route::delete('guru/hapus-semua', [GuruController::class, 'hapusSemua'])->name('guru.hapus-semua');

    // Manajemen Kelas
    Route::resource('kelas', KelasController::class)->parameters(['kelas' => 'kelas']);
    Route::delete('kelas/hapus-semua', [KelasController::class, 'hapusSemua'])->name('kelas.hapus-semua');

    // Pengaturan
    Route::prefix('pengaturan')->name('pengaturan.')->group(function () {
        Route::get('waktu-absen', [PengaturanController::class, 'waktuAbsen'])->name('waktu-absen');
        Route::put('waktu-absen', [PengaturanController::class, 'waktuAbsenSimpan'])->name('waktu-absen.simpan');
        Route::get('lokasi-absen', [PengaturanController::class, 'lokasiAbsen'])->name('lokasi-absen');
        Route::put('lokasi-absen', [PengaturanController::class, 'lokasiAbsenSimpan'])->name('lokasi-absen.simpan');
        Route::put('lokasi-absen/toleransi', [PengaturanController::class, 'lokasiAbsenToleransi'])->name('lokasi-absen.toleransi');
        Route::delete('lokasi-absen', [PengaturanController::class, 'lokasiAbsenHapus'])->name('lokasi-absen.hapus');
    });
});
