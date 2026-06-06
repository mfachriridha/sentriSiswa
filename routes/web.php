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
use App\Http\Controllers\Guru\AttendanceRecapController;
use App\Http\Controllers\Guru\BkMonitoringController;
use App\Http\Controllers\Guru\BkViolationSubmissionController;
use App\Http\Controllers\Guru\ClassRosterController;
use App\Http\Controllers\Guru\DashboardController as GuruDashboardController;
use App\Http\Controllers\Guru\MonitoringController;
use App\Http\Controllers\Guru\ProfilController as GuruProfilController;
use App\Http\Controllers\Guru\SchoolRuleController as GuruSchoolRuleController;
use App\Http\Controllers\Guru\StudentViolationController;
use App\Http\Controllers\Guru\ViolationHistoryController;
use App\Http\Controllers\Guru\ViolationReportController;
use App\Http\Controllers\Guru\ViolationTypeController;
use App\Http\Controllers\Siswa\AbsensiController as SiswaAbsensiController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\ProfilController as SiswaProfilController;
use App\Http\Controllers\Siswa\SchoolRuleController as SiswaSchoolRuleController;
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
    Route::get('/guru/impor', [TeacherImportController::class, 'create'])->name('guru.impor');
    Route::get('/guru/impor/template', [TeacherImportController::class, 'template'])->name('guru.impor.template');
    Route::post('/guru/impor/unggah', [TeacherImportController::class, 'upload'])->name('guru.impor.unggah');
    Route::get('/guru/impor/pratinjau', [TeacherImportController::class, 'preview'])->name('guru.impor.pratinjau');
    Route::post('/guru/impor', [TeacherImportController::class, 'store'])->name('guru.impor.store');
    Route::delete('/guru/hapus-semua', [TeacherController::class, 'deleteAll'])->name('guru.hapus-semua');
    Route::resource('guru', TeacherController::class)->parameters(['guru' => 'teacher']);
    Route::delete('/siswa/hapus-semua', [StudentController::class, 'deleteAll'])->name('siswa.hapus-semua');
    Route::get('/siswa/impor', [StudentImportController::class, 'create'])->name('siswa.impor');
    Route::get('/siswa/impor/template', [StudentImportController::class, 'template'])->name('siswa.impor.template');
    Route::post('/siswa/impor/unggah', [StudentImportController::class, 'upload'])->name('siswa.impor.unggah');
    Route::get('/siswa/impor/pratinjau', [StudentImportController::class, 'preview'])->name('siswa.impor.pratinjau');
    Route::post('/siswa/impor', [StudentImportController::class, 'store'])->name('siswa.impor.store');
    Route::get('/siswa/{student}/biodata/edit', [StudentBiodataController::class, 'edit'])->name('siswa.biodata.edit');
    Route::put('/siswa/{student}/biodata', [StudentBiodataController::class, 'update'])->name('siswa.biodata.update');
    Route::post('/siswa/{student}/foto', [StudentBiodataController::class, 'uploadPhoto'])->name('siswa.foto');
    Route::delete('/siswa/{student}/foto', [StudentBiodataController::class, 'deletePhoto'])->name('siswa.foto.hapus');
    Route::resource('siswa', StudentController::class)->parameters(['siswa' => 'student']);
    Route::delete('/kelas/hapus-semua', [ClassController::class, 'deleteAll'])->name('kelas.hapus-semua');
    Route::resource('kelas', ClassController::class)->parameters(['kelas' => 'class']);
    Route::get('/settings/attendance-time', [SettingController::class, 'attendanceTime'])->name('settings.attendance-time.index');
    Route::put('/settings/attendance-time', [SettingController::class, 'attendanceTimeUpdate'])->name('settings.attendance-time.update');
    Route::get('/settings/attendance-location', [SettingController::class, 'attendanceLocation'])->name('settings.attendance-location.index');
    Route::put('/settings/attendance-location', [SettingController::class, 'attendanceLocationUpdate'])->name('settings.attendance-location.update');
    Route::put('/settings/attendance-location/tolerance', [SettingController::class, 'attendanceLocationTolerance'])->name('settings.attendance-location.tolerance');
    Route::delete('/settings/attendance-location', [SettingController::class, 'attendanceLocationDelete'])->name('settings.attendance-location.destroy');
    Route::get('/settings/whatsapp', [SettingController::class, 'whatsapp'])->name('settings.whatsapp.index');
    Route::put('/settings/whatsapp', [SettingController::class, 'whatsappUpdate'])->name('settings.whatsapp.update');
    Route::post('/settings/whatsapp/test', [SettingController::class, 'whatsappTest'])->name('settings.whatsapp.test');
});

Route::middleware(['auth', 'registered', 'guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [GuruDashboardController::class, 'index'])->name('dashboard');
    Route::middleware('bk')->prefix('bk')->name('bk.')->group(function () {
        Route::get('/monitoring', [BkMonitoringController::class, 'index'])->name('monitoring.index');
        Route::get('/monitoring/{monitoring}', [BkMonitoringController::class, 'show'])->name('monitoring.show');
        Route::get('/pelanggaran', [BkViolationSubmissionController::class, 'index'])->name('pelanggaran.index');
        Route::get('/pelanggaran/buat', [BkViolationSubmissionController::class, 'create'])->name('pelanggaran.create');
        Route::post('/pelanggaran', [BkViolationSubmissionController::class, 'store'])->name('pelanggaran.store');
        Route::get('/laporan', [ViolationReportController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export-excel', [ViolationReportController::class, 'exportExcel'])->name('laporan.export-excel');
        Route::get('/laporan/export-pdf', [ViolationReportController::class, 'exportPdf'])->name('laporan.export-pdf');
    });
    Route::middleware('kesiswaan')->group(function () {
        Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
        Route::get('/monitoring/{monitoring}', [MonitoringController::class, 'show'])->name('monitoring.show');
        Route::put('/pelanggaran-siswa/{studentViolation}/approve', [StudentViolationController::class, 'approve'])->name('pelanggaran-siswa.approve');
        Route::put('/pelanggaran-siswa/{studentViolation}/reject', [StudentViolationController::class, 'reject'])->name('pelanggaran-siswa.reject');
        Route::resource('pelanggaran-siswa', StudentViolationController::class)
            ->parameters(['pelanggaran-siswa' => 'studentViolation']);
        Route::resource('jenis-pelanggaran', ViolationTypeController::class)
            ->parameters(['jenis-pelanggaran' => 'violationType']);
        Route::prefix('kesiswaan')->name('kesiswaan.')->group(function () {
            Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
            Route::get('/monitoring/{monitoring}', [MonitoringController::class, 'show'])->name('monitoring.show');
            Route::get('/pengajuan-pelanggaran', [StudentViolationController::class, 'index'])->defaults('status', 'pending')->name('pengajuan-pelanggaran.index');
            Route::get('/laporan', [ViolationReportController::class, 'index'])->name('laporan.index');
            Route::get('/laporan/export-excel', [ViolationReportController::class, 'exportExcel'])->name('laporan.export-excel');
            Route::get('/laporan/export-pdf', [ViolationReportController::class, 'exportPdf'])->name('laporan.export-pdf');
            Route::get('/tata-tertib', [GuruSchoolRuleController::class, 'index'])->name('tata-tertib.index');
            Route::post('/tata-tertib', [GuruSchoolRuleController::class, 'store'])->name('tata-tertib.store');
            Route::put('/tata-tertib/{schoolRule}/publish', [GuruSchoolRuleController::class, 'publish'])->name('tata-tertib.publish');
            Route::put('/tata-tertib/{schoolRule}/unpublish', [GuruSchoolRuleController::class, 'unpublish'])->name('tata-tertib.unpublish');
            Route::delete('/tata-tertib/{schoolRule}', [GuruSchoolRuleController::class, 'destroy'])->name('tata-tertib.destroy');
        });
    });
    Route::middleware('wali-kelas')->group(function () {
        Route::get('/kelas-saya', [ClassRosterController::class, 'index'])->name('kelas-saya');
        Route::put('/kelas-saya/{studentProfile}/absensi', [ClassRosterController::class, 'updateAttendance'])->name('kelas-saya.absensi.update');
        Route::get('/absensi', [AttendanceRecapController::class, 'index'])->name('absensi.index');
        Route::get('/absensi/export-excel', [AttendanceRecapController::class, 'exportExcel'])->name('absensi.export-excel');
        Route::get('/absensi/export-pdf', [AttendanceRecapController::class, 'exportPdf'])->name('absensi.export-pdf');
        Route::get('/pelanggaran', [ViolationHistoryController::class, 'index'])->name('pelanggaran');
    });
    Route::get('/profil', [GuruProfilController::class, 'show'])->name('profil');
    Route::get('/profil/edit', [GuruProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [GuruProfilController::class, 'update'])->name('profil.update');
    Route::post('/profil/photo', [GuruProfilController::class, 'uploadPhoto'])->name('profil.photo');
    Route::delete('/profil/photo', [GuruProfilController::class, 'deletePhoto'])->name('profil.photo.delete');
});

Route::middleware(['auth', 'registered', 'siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');
    Route::get('/poin', [SiswaProfilController::class, 'poin'])->name('poin');
    Route::get('/tata-tertib', [SiswaSchoolRuleController::class, 'index'])->name('tata-tertib.index');
    Route::get('/absensi', [SiswaAbsensiController::class, 'index'])->name('absensi');
    Route::post('/absensi/cek-lokasi', [SiswaAbsensiController::class, 'checkLocation'])->name('absensi.check-location');
    Route::post('/absensi', [SiswaAbsensiController::class, 'store'])->name('absensi.store');
    Route::get('/absensi/riwayat', [SiswaAbsensiController::class, 'riwayat'])->name('absensi.riwayat');
    Route::get('/profil', [SiswaProfilController::class, 'show'])->name('profil');
    Route::get('/profil/edit', [SiswaProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [SiswaProfilController::class, 'update'])->name('profil.update');
    Route::post('/profil/photo', [SiswaProfilController::class, 'uploadPhoto'])->name('profil.photo');
    Route::delete('/profil/photo', [SiswaProfilController::class, 'deletePhoto'])->name('profil.photo.delete');
});
