<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('landing'))->name('landing');

Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::view('/register', 'auth.register')->name('register');
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
});

Route::prefix('siswa')->name('siswa.')->middleware('auth.session')->group(function () {
    Route::view('/', 'siswa.dashboard')->name('dashboard');
    Route::view('/kehadiran', 'siswa.kehadiran')->name('kehadiran');
    Route::view('/riwayat', 'siswa.riwayat')->name('riwayat');
    Route::view('/poin', 'siswa.poin')->name('poin');
    Route::view('/biodata', 'siswa.biodata')->name('biodata');
    Route::view('/pengaturan', 'siswa.pengaturan')->name('pengaturan');
});

Route::prefix('admin')->name('admin.')->middleware('auth.session')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::put('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');

    // Guru
    Route::get('/guru', fn () => view('admin.guru'))->name('guru');
    Route::get('/guru/tambah', fn () => view('admin.guru-form'))->name('guru.tambah');
    Route::get('/guru/edit', fn () => view('admin.guru-form'))->name('guru.edit');

    // Kelas
    Route::get('/kelas', [AdminController::class, 'kelas'])->name('kelas');
    Route::post('/kelas', [AdminController::class, 'storeKelas'])->name('kelas.store');
    Route::put('/kelas/{kelas}', [AdminController::class, 'updateKelas'])->name('kelas.update');
    Route::delete('/kelas/{kelas}', [AdminController::class, 'destroyKelas'])->name('kelas.destroy');

    // Siswa
    Route::get('/siswa', [AdminController::class, 'siswa'])->name('siswa');
    Route::post('/siswa', [AdminController::class, 'storeSiswa'])->name('siswa.store');
    Route::get('/siswa/tambah', fn () => view('admin.siswa-form'))->name('siswa.tambah');
    Route::get('/siswa/{student}/edit', [AdminController::class, 'editSiswa'])->name('siswa.edit');
    Route::put('/siswa/{student}', [AdminController::class, 'updateSiswa'])->name('siswa.update');
    Route::delete('/siswa/{student}', [AdminController::class, 'destroySiswa'])->name('siswa.destroy');
    // CSV
    Route::post('/siswa/import', [AdminController::class, 'previewCsv'])->name('siswa.import');
    Route::get('/siswa/preview', [AdminController::class, 'preview'])->name('siswa.preview');
    Route::post('/siswa/import/confirm', [AdminController::class, 'importCsv'])->name('siswa.import.confirm');

    // Poin Pelanggaran
    Route::get('/poin', [AdminController::class, 'poin'])->name('poin');
    Route::post('/poin', [AdminController::class, 'storePoin'])->name('poin.store');

    // Tata Tertib
    Route::get('/tata-tertib', [AdminController::class, 'tataTertib'])->name('tata-tertib');
    Route::post('/tata-tertib', [AdminController::class, 'uploadTataTertib'])->name('tata-tertib.upload');

    // Laporan
    Route::view('/laporan', 'admin.laporan')->name('laporan');
});

Route::prefix('profile')->name('profile.')->group(function () {
    Route::view('/', 'profile.index')->name('index');
    Route::view('/edit', 'profile.edit')->name('edit');
});