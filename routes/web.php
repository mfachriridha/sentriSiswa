<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BKController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\WaliKelasController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('landing'))->name('landing');

Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/register', [RegisterController::class, 'showForm'])->name('register');
    Route::post('/register/validate-nip', [RegisterController::class, 'validateNip'])->name('register.validate-nip');
    Route::post('/register/validate-nis', [RegisterController::class, 'validateNis'])->name('register.validate-nis');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
});

Route::prefix('siswa')->name('siswa.')->middleware(['auth', 'role:siswa'])->group(function () {
    Route::view('/', 'siswa.dashboard')->name('dashboard');
    Route::get('/kehadiran', [SiswaController::class, 'kehadiran'])->name('kehadiran');
    Route::post('/kehadiran', [SiswaController::class, 'storeAttendance'])->name('kehadiran.store');
    Route::get('/riwayat', [SiswaController::class, 'riwayat'])->name('riwayat');
    Route::get('/poin', [SiswaController::class, 'poin'])->name('poin');
    Route::get('/pengaturan', [SiswaController::class, 'pengaturan'])->name('pengaturan');
    Route::put('/pengaturan', [SiswaController::class, 'updatePengaturan'])->name('pengaturan.update');
    Route::view('/biodata', 'siswa.biodata')->name('biodata');
});

Route::prefix('wali-kelas')->name('wali-kelas.')->middleware(['auth', 'role:wali_kelas'])->group(function () {
    Route::get('/', [WaliKelasController::class, 'dashboard'])->name('dashboard');
    Route::get('/laporan', [WaliKelasController::class, 'laporan'])->name('laporan');
    Route::post('/confirm/{attendance}', [WaliKelasController::class, 'confirm'])->name('confirm');
    Route::post('/update-status/{attendance}', [WaliKelasController::class, 'updateStatus'])->name('update-status');
});

Route::prefix('bk')->name('bk.')->middleware(['auth', 'role:bk'])->group(function () {
    Route::get('/', [BKController::class, 'dashboard'])->name('dashboard');
    Route::get('/laporan', [BKController::class, 'laporan'])->name('laporan');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::put('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');

    // Guru
    Route::get('/guru', [AdminController::class, 'guru'])->name('guru');
    Route::post('/guru', [AdminController::class, 'storeGuru'])->name('guru.store');
    Route::get('/guru/{user}/edit', [AdminController::class, 'editGuru'])->name('guru.edit');
    Route::put('/guru/{user}', [AdminController::class, 'updateGuru'])->name('guru.update');
    Route::delete('/guru', [AdminController::class, 'destroyAllGuru'])->name('guru.destroy-all');
    Route::delete('/guru/{user}', [AdminController::class, 'destroyGuru'])->name('guru.destroy');
    Route::post('/guru/import', [AdminController::class, 'previewGuruCsv'])->name('guru.import');
    Route::get('/guru/preview', [AdminController::class, 'previewGuru'])->name('guru.preview');
    Route::post('/guru/import/confirm', [AdminController::class, 'importGuruCsv'])->name('guru.import.confirm');
    Route::get('/guru/template', [AdminController::class, 'downloadGuruTemplate'])->name('guru.template');

    // Kelas
    Route::get('/kelas', [AdminController::class, 'kelas'])->name('kelas');
    Route::post('/kelas', [AdminController::class, 'storeClass'])->name('kelas.store');
    Route::put('/kelas/{schoolClass}', [AdminController::class, 'updateClass'])->name('kelas.update');
    Route::delete('/kelas', [AdminController::class, 'destroyAllKelas'])->name('kelas.destroy-all');
    Route::delete('/kelas/{schoolClass}', [AdminController::class, 'destroyClass'])->name('kelas.destroy');
    Route::get('/kelas/{schoolClass}/students', [AdminController::class, 'classStudents'])->name('kelas.students');
    Route::post('/kelas/{schoolClass}/assign', [AdminController::class, 'assignStudentToClass'])->name('kelas.assign');
    Route::post('/kelas/{schoolClass}/remove', [AdminController::class, 'removeStudentFromClass'])->name('kelas.remove');
    Route::get('/siswa/search', [AdminController::class, 'searchStudents'])->name('siswa.search');
    Route::get('/siswa/{student}/detail', [AdminController::class, 'studentDetail'])->name('siswa.detail');

    // Siswa
    Route::get('/siswa', [AdminController::class, 'siswa'])->name('siswa');
    Route::post('/siswa', [AdminController::class, 'storeStudent'])->name('siswa.store');
    Route::get('/siswa/tambah', fn () => view('admin.siswa-form'))->name('siswa.tambah');
    Route::get('/siswa/{student}/edit', [AdminController::class, 'editSiswa'])->name('siswa.edit');
    Route::put('/siswa/{student}', [AdminController::class, 'updateStudent'])->name('siswa.update');
    Route::delete('/siswa', [AdminController::class, 'destroyAllSiswa'])->name('siswa.destroy-all');
    Route::delete('/siswa/{student}', [AdminController::class, 'destroyStudent'])->name('siswa.destroy');
    // CSV
    Route::post('/siswa/import', [AdminController::class, 'previewCsv'])->name('siswa.import');
    Route::get('/siswa/preview', [AdminController::class, 'preview'])->name('siswa.preview');
    Route::post('/siswa/import/confirm', [AdminController::class, 'importCsv'])->name('siswa.import.confirm');
    Route::get('/siswa/template', [AdminController::class, 'downloadSiswaTemplate'])->name('siswa.template');

    // Poin Pelanggaran
    Route::get('/poin', [AdminController::class, 'poin'])->name('poin');
    Route::post('/poin', [AdminController::class, 'storeViolation'])->name('poin.store');
    Route::post('/poin/violation', [AdminController::class, 'storeViolationType'])->name('poin.violation.store');
    Route::put('/poin/violation/{violation}', [AdminController::class, 'updateViolationType'])->name('poin.violation.update');
    Route::delete('/poin/violation/{violation}', [AdminController::class, 'destroyViolationType'])->name('poin.violation.destroy');

    // Tata Tertib
    Route::get('/tata-tertib', [AdminController::class, 'tataTertib'])->name('tata-tertib');
    Route::post('/tata-tertib', [AdminController::class, 'uploadTataTertib'])->name('tata-tertib.upload');
    Route::delete('/tata-tertib', [AdminController::class, 'deleteTataTertib'])->name('tata-tertib.delete');

    // Integrasi
    Route::view('/integrasi', 'admin.integrasi')->name('integrasi');

    // Pengaturan
    Route::get('/pengaturan', [AdminController::class, 'settings'])->name('settings');
    Route::put('/pengaturan', [AdminController::class, 'updateSettings'])->name('settings.update');
});

Route::prefix('profile')->name('profile.')->group(function () {
    Route::view('/', 'profile.index')->name('index');
    Route::view('/edit', 'profile.edit')->name('edit');
});
