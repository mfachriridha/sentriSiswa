<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::prefix('auth')->name('auth.')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::view('/register', 'auth.register')->name('register');
    
    Route::post('/login', function () {
        $role = request()->input('role');
        $username = request()->input('username');
        
        Session::put('user_role', $role);
        Session::put('user_name', $username);
        
        return redirect()->route($role . '.dashboard');
    })->name('login.post');
    
    Route::get('/logout', function () {
        Session::flush();
        return redirect()->route('landing');
    })->name('logout');
});

Route::prefix('kesiswaan')->name('kesiswaan.')->group(function () {
    Route::view('/', 'kesiswaan.dashboard')->name('dashboard');
    Route::view('/master-data', 'kesiswaan.master-data')->name('master-data');
    Route::view('/manajemen-role', 'kesiswaan.manajemen-role')->name('manajemen-role');
    Route::view('/poin-pelanggaran', 'kesiswaan.poin-pelanggaran')->name('poin-pelanggaran');
});

Route::prefix('walikelas')->name('walikelas.')->group(function () {
    Route::view('/', 'walikelas.dashboard')->name('dashboard');
    Route::view('/monitoring', 'walikelas.monitoring')->name('monitoring');
});

Route::prefix('guru-mapel')->name('guru-mapel.')->group(function () {
    Route::view('/', 'guru-mapel.dashboard')->name('dashboard');
});

Route::prefix('bk')->name('bk.')->group(function () {
    Route::view('/', 'bk.dashboard')->name('dashboard');
    Route::view('/monitoring', 'bk.monitoring')->name('monitoring');
    Route::view('/rekap-presensi', 'bk.rekap-presensi')->name('rekap-presensi');
});

Route::prefix('siswa')->name('siswa.')->group(function () {
    Route::view('/', 'siswa.dashboard')->name('dashboard');
});

Route::prefix('profile')->name('profile.')->group(function () {
    Route::view('/', 'profile.index')->name('index');
    Route::view('/edit', 'profile.edit')->name('edit');
    Route::view('/guru', 'profile.guru')->name('guru');
    Route::view('/siswa', 'profile.siswa')->name('siswa');
});