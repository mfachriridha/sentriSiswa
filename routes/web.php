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
        $email = request()->input('email');
        $password = request()->input('password');
        
        if ($email === 'siswa@sentrisiswa.sch.id' && $password === 'siswa123') {
            Session::put('user_role', 'siswa');
            Session::put('user_name', 'Ahmad Fauzi');
            Session::put('user_email', 'siswa@sentrisiswa.sch.id');
            return redirect()->route('siswa.dashboard');
        }
        
        return redirect()->route('auth.login')->with('error', 'Email atau kata sandi salah.');
    })->name('login.post');
    
    Route::get('/logout', function () {
        Session::flush();
        return redirect()->route('landing');
    })->name('logout');
});

Route::prefix('siswa')->name('siswa.')->group(function () {
    Route::view('/', 'siswa.dashboard')->name('dashboard');
    Route::view('/kehadiran', 'siswa.kehadiran')->name('kehadiran');
    Route::view('/riwayat', 'siswa.riwayat')->name('riwayat');
    Route::view('/poin', 'siswa.poin')->name('poin');
    Route::view('/biodata', 'siswa.biodata')->name('biodata');
    Route::view('/pengaturan', 'siswa.pengaturan')->name('pengaturan');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::view('/login', 'admin.login')->name('login');
    
    Route::post('/login', function () {
        $email = request()->input('email');
        $password = request()->input('password');
        
        if ($email === 'admin@sentrisiswa.sch.id' && $password === 'admin123') {
            Session::put('user_role', 'admin');
            Session::put('user_name', 'Admin Kesiswaan');
            Session::put('user_email', 'admin@sentrisiswa.sch.id');
            return redirect()->route('admin.dashboard');
        }
        
        return redirect()->route('admin.login')->with('error', 'Email atau kata sandi salah.');
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

Route::prefix('profile')->name('profile.')->group(function () {
    Route::view('/', 'profile.index')->name('index');
    Route::view('/edit', 'profile.edit')->name('edit');
    Route::view('/guru', 'profile.guru')->name('guru');
    Route::view('/siswa', 'profile.siswa')->name('siswa');
});