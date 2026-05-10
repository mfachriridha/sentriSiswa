<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('landing');

// Auth routes
Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/login', [LoginController::class, 'showSiswaLogin'])->name('login');
    Route::view('/register', 'auth.register')->name('register');
    Route::post('/login', function () {
        return app(LoginController::class)->login(request(), 'siswa');
    })->name('login.post');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout.get');
});

// Siswa
Route::prefix('siswa')->name('siswa.')->group(function () {
    Route::view('/', 'siswa.dashboard')->name('dashboard');
    Route::view('/kehadiran', 'siswa.kehadiran')->name('kehadiran');
    Route::view('/riwayat', 'siswa.riwayat')->name('riwayat');
    Route::view('/poin', 'siswa.poin')->name('poin');
    Route::view('/biodata', 'siswa.biodata')->name('biodata');
    Route::view('/pengaturan', 'siswa.pengaturan')->name('pengaturan');
});

// Admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [LoginController::class, 'showAdminLogin'])->name('login');
    Route::post('/login', function () {
        return app(LoginController::class)->login(request(), 'admin');
    })->name('login.post');

    Route::view('/', 'admin.dashboard')->name('dashboard');
    Route::view('/guru', 'admin.guru')->name('guru');
    Route::view('/guru/tambah', 'admin.guru-form')->name('guru.tambah');
    Route::view('/guru/edit', 'admin.guru-form')->name('guru.edit');
    Route::view('/siswa', 'admin.siswa')->name('siswa');
    Route::view('/siswa/tambah', 'admin.siswa-form')->name('siswa.tambah');
    Route::view('/siswa/edit', 'admin.siswa-form')->name('siswa.edit');
    Route::view('/kelas', 'admin.kelas')->name('kelas');
    Route::view('/tata-tertib', 'admin.tata-tertib')->name('tata-tertib');
    Route::view('/poin', 'admin.poin-pelanggaran')->name('poin');
    Route::view('/laporan', 'admin.laporan')->name('laporan');
    Route::view('/log', 'admin.log')->name('log');
});

// Profile
Route::prefix('profile')->name('profile.')->group(function () {
    Route::view('/', 'profile.index')->name('index');
    Route::view('/edit', 'profile.edit')->name('edit');
    Route::view('/guru', 'profile.guru')->name('guru');
    Route::view('/siswa', 'profile.siswa')->name('siswa');
});