<?php

use Illuminate\Support\Facades\Route;

// Landing Page
Route::get('/', function () {
    return view('landing');
});

// Auth Routes
Route::get('/login', function () {
    return view('auth.login');
});

Route::get('/register', function () {
    return view('auth.register');
});

// Kesiswaan Routes
Route::get('/kesiswaan', function () {
    return view('kesiswaan.dashboard');
});

// Wali Kelas Routes
Route::get('/walikelas', function () {
    return view('walikelas.dashboard');
});

// Guru Mapel Routes
Route::get('/guru-mapel', function () {
    return view('guru-mapel.dashboard');
});

// BK Routes
Route::get('/bk', function () {
    return view('bk.dashboard');
});

// Siswa Routes
Route::get('/siswa', function () {
    return view('siswa.dashboard');
});

// Profile Routes
Route::get('/profile/guru', function () {
    return view('profile.guru');
});

Route::get('/profile/siswa', function () {
    return view('profile.siswa');
});
