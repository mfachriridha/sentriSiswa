<?php

use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\BiodataSiswaController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\ImporGuruController;
use App\Http\Controllers\Admin\ImporSiswaController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\PengaturanController;
use App\Http\Controllers\Admin\ProfilController as AdminProfilController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Bk\DashboardController as BkDashboardController;
use App\Http\Controllers\Bk\LaporanController as BkLaporanController;
use App\Http\Controllers\Bk\MonitoringController as BkMonitoringController;
use App\Http\Controllers\Bk\PengajuanPelanggaranController;
use App\Http\Controllers\Bk\ProfilController as BkProfilController;
use App\Http\Controllers\Kesiswaan\DashboardController as KesiswaanDashboardController;
use App\Http\Controllers\Kesiswaan\JenisPelanggaranController;
use App\Http\Controllers\Kesiswaan\LaporanController as KesiswaanLaporanController;
use App\Http\Controllers\Kesiswaan\MonitoringController as KesiswaanMonitoringController;
use App\Http\Controllers\Kesiswaan\PelanggaranSiswaController;
use App\Http\Controllers\Kesiswaan\ProfilController as KesiswaanProfilController;
use App\Http\Controllers\Kesiswaan\TataTertibController;
use App\Http\Controllers\Siswa\AbsensiController as SiswaAbsensiController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\ProfilController as SiswaProfilController;
use App\Http\Controllers\Siswa\SchoolRuleController as SiswaSchoolRuleController;
use App\Http\Controllers\WaliKelas\AbsensiController as WaliKelasAbsensiController;
use App\Http\Controllers\WaliKelas\DashboardController as WaliKelasDashboardController;
use App\Http\Controllers\WaliKelas\KelasSayaController;
use App\Http\Controllers\WaliKelas\ProfilController as WaliKelasProfilController;
use App\Http\Controllers\WaliKelas\RiwayatPelanggaranController;
use App\Http\Controllers\Webhook\WhatsAppWebhookController;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return view('welcome', [
            'studentCount' => StudentProfile::count(),
            'teacherCount' => User::whereIn('role', ['wali_kelas', 'bk', 'kesiswaan'])->count(),
        ]);
    }

    return redirect()->route(auth()->user()->dashboardRouteName());
})->name('home');

Route::view('/privacy-policy', 'legal.privacy-policy')->name('privacy-policy');
Route::view('/terms-of-service', 'legal.terms-of-service')->name('terms-of-service');
Route::get('/whatsapp/webhook', [WhatsAppWebhookController::class, 'verify'])->name('whatsapp.webhook.verify');
Route::post('/whatsapp/webhook', [WhatsAppWebhookController::class, 'handle'])->name('whatsapp.webhook.handle');

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
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profil', [AdminProfilController::class, 'show'])->name('profil');
    Route::get('/profil/edit', [AdminProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [AdminProfilController::class, 'update'])->name('profil.update');
    Route::get('/guru/impor', [ImporGuruController::class, 'create'])->name('guru.impor');
    Route::get('/guru/impor/template', [ImporGuruController::class, 'template'])->name('guru.impor.template');
    Route::post('/guru/impor/unggah', [ImporGuruController::class, 'upload'])->name('guru.impor.unggah');
    Route::get('/guru/impor/pratinjau', [ImporGuruController::class, 'preview'])->name('guru.impor.pratinjau');
    Route::post('/guru/impor', [ImporGuruController::class, 'store'])->name('guru.impor.store');
    Route::delete('/guru/hapus-semua', [GuruController::class, 'deleteAll'])->name('guru.hapus-semua');
    Route::resource('guru', GuruController::class)->parameters(['guru' => 'teacher']);
    Route::delete('/siswa/hapus-semua', [SiswaController::class, 'deleteAll'])->name('siswa.hapus-semua');
    Route::get('/siswa/impor', [ImporSiswaController::class, 'create'])->name('siswa.impor');
    Route::get('/siswa/impor/template', [ImporSiswaController::class, 'template'])->name('siswa.impor.template');
    Route::post('/siswa/impor/unggah', [ImporSiswaController::class, 'upload'])->name('siswa.impor.unggah');
    Route::get('/siswa/impor/pratinjau', [ImporSiswaController::class, 'preview'])->name('siswa.impor.pratinjau');
    Route::post('/siswa/impor', [ImporSiswaController::class, 'store'])->name('siswa.impor.store');
    Route::get('/siswa/{student}/biodata/edit', [BiodataSiswaController::class, 'edit'])->name('siswa.biodata.edit');
    Route::put('/siswa/{student}/biodata', [BiodataSiswaController::class, 'update'])->name('siswa.biodata.update');
    Route::post('/siswa/{student}/foto', [BiodataSiswaController::class, 'uploadPhoto'])->name('siswa.foto');
    Route::delete('/siswa/{student}/foto', [BiodataSiswaController::class, 'deletePhoto'])->name('siswa.foto.hapus');
    Route::resource('siswa', SiswaController::class)->parameters(['siswa' => 'student']);
    Route::delete('/kelas/hapus-semua', [KelasController::class, 'deleteAll'])->name('kelas.hapus-semua');
    Route::resource('kelas', KelasController::class)->parameters(['kelas' => 'class']);
    Route::get('/settings/attendance-time', [PengaturanController::class, 'attendanceTime'])->name('settings.attendance-time.index');
    Route::put('/settings/attendance-time', [PengaturanController::class, 'attendanceTimeUpdate'])->name('settings.attendance-time.update');
    Route::get('/settings/attendance-location', [PengaturanController::class, 'attendanceLocation'])->name('settings.attendance-location.index');
    Route::put('/settings/attendance-location', [PengaturanController::class, 'attendanceLocationUpdate'])->name('settings.attendance-location.update');
    Route::put('/settings/attendance-location/tolerance', [PengaturanController::class, 'attendanceLocationTolerance'])->name('settings.attendance-location.tolerance');
    Route::delete('/settings/attendance-location', [PengaturanController::class, 'attendanceLocationDelete'])->name('settings.attendance-location.destroy');
    Route::get('/settings/whatsapp', [PengaturanController::class, 'whatsapp'])->name('settings.whatsapp.index');
    Route::put('/settings/whatsapp', [PengaturanController::class, 'whatsappUpdate'])->name('settings.whatsapp.update');
    Route::post('/settings/whatsapp/test', [PengaturanController::class, 'whatsappTest'])->name('settings.whatsapp.test');
});

Route::middleware(['auth', 'registered', 'wali-kelas'])->prefix('wali-kelas')->name('wali-kelas.')->group(function () {
    Route::get('/dashboard', [WaliKelasDashboardController::class, 'index'])->name('dashboard');
    Route::get('/kelas-saya', [KelasSayaController::class, 'index'])->name('kelas-saya');
    Route::get('/kelas-saya/{studentProfile}', [KelasSayaController::class, 'show'])->name('kelas-saya.show');
    Route::put('/kelas-saya/{studentProfile}/absensi', [KelasSayaController::class, 'updateAttendance'])->name('kelas-saya.absensi.update');
    Route::get('/absensi', [WaliKelasAbsensiController::class, 'index'])->name('absensi.index');
    Route::get('/absensi/export-excel', [WaliKelasAbsensiController::class, 'exportExcel'])->name('absensi.export-excel');
    Route::get('/absensi/export-pdf', [WaliKelasAbsensiController::class, 'exportPdf'])->name('absensi.export-pdf');
    Route::get('/pelanggaran', [RiwayatPelanggaranController::class, 'index'])->name('pelanggaran');
    Route::get('/profil', [WaliKelasProfilController::class, 'show'])->name('profil');
    Route::get('/profil/edit', [WaliKelasProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [WaliKelasProfilController::class, 'update'])->name('profil.update');
    Route::post('/profil/photo', [WaliKelasProfilController::class, 'uploadPhoto'])->name('profil.photo');
    Route::delete('/profil/photo', [WaliKelasProfilController::class, 'deletePhoto'])->name('profil.photo.delete');
});

Route::middleware(['auth', 'registered', 'bk'])->prefix('bk')->name('bk.')->group(function () {
    Route::get('/dashboard', [BkDashboardController::class, 'index'])->name('dashboard');
    Route::get('/monitoring', [BkMonitoringController::class, 'index'])->name('monitoring.index');
    Route::get('/monitoring/{monitoring}', [BkMonitoringController::class, 'show'])->name('monitoring.show');
    Route::get('/pelanggaran', [PengajuanPelanggaranController::class, 'index'])->name('pelanggaran.index');
    Route::get('/pelanggaran/buat', [PengajuanPelanggaranController::class, 'create'])->name('pelanggaran.create');
    Route::post('/pelanggaran', [PengajuanPelanggaranController::class, 'store'])->name('pelanggaran.store');
    Route::get('/laporan', [BkLaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export-excel', [BkLaporanController::class, 'exportExcel'])->name('laporan.export-excel');
    Route::get('/laporan/export-pdf', [BkLaporanController::class, 'exportPdf'])->name('laporan.export-pdf');
    Route::get('/profil', [BkProfilController::class, 'show'])->name('profil');
    Route::get('/profil/edit', [BkProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [BkProfilController::class, 'update'])->name('profil.update');
    Route::post('/profil/photo', [BkProfilController::class, 'uploadPhoto'])->name('profil.photo');
    Route::delete('/profil/photo', [BkProfilController::class, 'deletePhoto'])->name('profil.photo.delete');
});

Route::middleware(['auth', 'registered', 'kesiswaan'])->prefix('kesiswaan')->name('kesiswaan.')->group(function () {
    Route::get('/dashboard', [KesiswaanDashboardController::class, 'index'])->name('dashboard');
    Route::get('/monitoring', [KesiswaanMonitoringController::class, 'index'])->name('monitoring.index');
    Route::get('/monitoring/{monitoring}', [KesiswaanMonitoringController::class, 'show'])->name('monitoring.show');
    Route::put('/pelanggaran-siswa/{studentViolation}/approve', [PelanggaranSiswaController::class, 'approve'])->name('pelanggaran-siswa.approve');
    Route::put('/pelanggaran-siswa/{studentViolation}/reject', [PelanggaranSiswaController::class, 'reject'])->name('pelanggaran-siswa.reject');
    Route::resource('pelanggaran-siswa', PelanggaranSiswaController::class)
        ->parameters(['pelanggaran-siswa' => 'studentViolation']);
    Route::resource('jenis-pelanggaran', JenisPelanggaranController::class)
        ->parameters(['jenis-pelanggaran' => 'violationType']);
    Route::get('/pengajuan-pelanggaran', [PelanggaranSiswaController::class, 'index'])->defaults('status', 'pending')->name('pengajuan-pelanggaran.index');
    Route::get('/laporan', [KesiswaanLaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export-excel', [KesiswaanLaporanController::class, 'exportExcel'])->name('laporan.export-excel');
    Route::get('/laporan/export-pdf', [KesiswaanLaporanController::class, 'exportPdf'])->name('laporan.export-pdf');
    Route::get('/tata-tertib', [TataTertibController::class, 'index'])->name('tata-tertib.index');
    Route::post('/tata-tertib', [TataTertibController::class, 'store'])->name('tata-tertib.store');
    Route::put('/tata-tertib/{schoolRule}/publish', [TataTertibController::class, 'publish'])->name('tata-tertib.publish');
    Route::put('/tata-tertib/{schoolRule}/unpublish', [TataTertibController::class, 'unpublish'])->name('tata-tertib.unpublish');
    Route::delete('/tata-tertib/{schoolRule}', [TataTertibController::class, 'destroy'])->name('tata-tertib.destroy');
    Route::get('/profil', [KesiswaanProfilController::class, 'show'])->name('profil');
    Route::get('/profil/edit', [KesiswaanProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [KesiswaanProfilController::class, 'update'])->name('profil.update');
    Route::post('/profil/photo', [KesiswaanProfilController::class, 'uploadPhoto'])->name('profil.photo');
    Route::delete('/profil/photo', [KesiswaanProfilController::class, 'deletePhoto'])->name('profil.photo.delete');
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
