<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StudentBiodataController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\StudentImportController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\TeacherImportController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/', fn () => redirect()->route('admin.dashboard'));
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
});
