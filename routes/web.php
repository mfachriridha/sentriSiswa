<?php

use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StudentBiodataController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\StudentImportController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\TeacherImportController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\Siswa\AbsensiController as SiswaAbsensiController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\ProfilController as SiswaProfilController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'create'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'store']);
    Route::get('/daftar', [RegisterController::class, 'create'])->name('register');
    Route::post('/daftar/verifikasi', [RegisterController::class, 'verify'])->name('register.verify');
    Route::get('/daftar/lengkapi', [RegisterController::class, 'showForm'])->name('register.step2');
    Route::post('/daftar/lengkapi', [RegisterController::class, 'store'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AdminLoginController::class, 'destroy'])->name('logout');
    Route::get('/', function () {
        return match (auth()->user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'teacher' => redirect()->route('guru.dashboard'),
            'student' => redirect()->route('siswa.dashboard'),
            default => redirect()->route('login'),
        };
    });
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/teachers/import', [TeacherImportController::class, 'create'])->name('teachers.import');
    Route::get('/teachers/import/template', [TeacherImportController::class, 'template'])->name('teachers.import.template');
    Route::post('/teachers/import/upload', [TeacherImportController::class, 'upload'])->name('teachers.import.upload');
    Route::get('/teachers/import/preview', [TeacherImportController::class, 'preview'])->name('teachers.import.preview');
    Route::post('/teachers/import', [TeacherImportController::class, 'store'])->name('teachers.import.store');
    Route::delete('/teachers/delete-all', [TeacherController::class, 'deleteAll'])->name('teachers.delete-all');
    Route::resource('teachers', TeacherController::class);
    Route::delete('/students/delete-all', [StudentController::class, 'deleteAll'])->name('students.delete-all');
    Route::get('/students/import', [StudentImportController::class, 'create'])->name('students.import');
    Route::get('/students/import/template', [StudentImportController::class, 'template'])->name('students.import.template');
    Route::post('/students/import/upload', [StudentImportController::class, 'upload'])->name('students.import.upload');
    Route::get('/students/import/preview', [StudentImportController::class, 'preview'])->name('students.import.preview');
    Route::post('/students/import', [StudentImportController::class, 'store'])->name('students.import.store');
    Route::get('/students/{student}/biodata/edit', [StudentBiodataController::class, 'edit'])->name('students.biodata.edit');
    Route::put('/students/{student}/biodata', [StudentBiodataController::class, 'update'])->name('students.biodata.update');
    Route::post('/students/{student}/photo', [StudentBiodataController::class, 'uploadPhoto'])->name('students.photo');
    Route::delete('/students/{student}/photo', [StudentBiodataController::class, 'deletePhoto'])->name('students.photo.delete');
    Route::resource('students', StudentController::class);
    Route::delete('/classes/delete-all', [ClassController::class, 'deleteAll'])->name('classes.delete-all');
    Route::resource('classes', ClassController::class);
    Route::get('/settings/attendance-time', [SettingController::class, 'attendanceTime'])->name('settings.attendance-time.index');
    Route::put('/settings/attendance-time', [SettingController::class, 'attendanceTimeUpdate'])->name('settings.attendance-time.update');
    Route::get('/settings/whatsapp', [SettingController::class, 'whatsapp'])->name('settings.whatsapp.index');
    Route::put('/settings/whatsapp', [SettingController::class, 'whatsappUpdate'])->name('settings.whatsapp.update');
});

Route::middleware(['auth', 'registered', 'guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'registered', 'siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');
    Route::get('/absensi', [SiswaAbsensiController::class, 'index'])->name('absensi');
    Route::post('/absensi', [SiswaAbsensiController::class, 'store'])->name('absensi.store');
    Route::get('/absensi/riwayat', [SiswaAbsensiController::class, 'riwayat'])->name('absensi.riwayat');
    Route::get('/profil', [SiswaProfilController::class, 'show'])->name('profil');
    Route::get('/profil/edit', [SiswaProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [SiswaProfilController::class, 'update'])->name('profil.update');
    Route::post('/profil/photo', [SiswaProfilController::class, 'uploadPhoto'])->name('profil.photo');
    Route::delete('/profil/photo', [SiswaProfilController::class, 'deletePhoto'])->name('profil.photo.delete');
});
