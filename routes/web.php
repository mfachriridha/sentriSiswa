<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('landing');

// Auth routes — satu login untuk semua role
Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::view('/register', 'auth.register')->name('register');
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
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
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/guru', function () { return view('admin.guru'); })->name('guru');
    Route::get('/guru/tambah', function () { return view('admin.guru-form'); })->name('guru.tambah');
    Route::get('/guru/edit', function () { return view('admin.guru-form'); })->name('guru.edit');
    Route::get('/siswa', [AdminController::class, 'siswa'])->name('siswa');
    Route::post('/siswa/import', [AdminController::class, 'importCsv'])->name('siswa.import');
    Route::get('/siswa/tambah', function () { return view('admin.siswa-form'); })->name('siswa.tambah');
    Route::get('/siswa/edit', function () { return view('admin.siswa-form'); })->name('siswa.edit');
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