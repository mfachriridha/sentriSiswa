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
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\GoogleWhatsappController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Bk\DashboardController as BkDashboardController;
use App\Http\Controllers\Bk\AbsensiRecapController as BkAbsensiRecapController;
use App\Http\Controllers\Bk\MonitoringController as BkMonitoringController;
use App\Http\Controllers\Bk\ProfilController as BkProfilController;
use App\Http\Controllers\Kesiswaan\DashboardController as KesiswaanDashboardController;
use App\Http\Controllers\Kesiswaan\JenisPelanggaranController;
use App\Http\Controllers\Kesiswaan\LaporanController as KesiswaanLaporanController;
use App\Http\Controllers\Kesiswaan\MonitoringController as KesiswaanMonitoringController;
use App\Http\Controllers\Kesiswaan\PelanggaranSiswaController;
use App\Http\Controllers\Kesiswaan\PengajuanPoinController as KesiswaanPengajuanPoinController;
use App\Http\Controllers\Kesiswaan\ProfilController as KesiswaanProfilController;
use App\Http\Controllers\Kesiswaan\TataTertibController;
use App\Http\Controllers\Siswa\AbsensiController as SiswaAbsensiController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\Siswa\ProfilController as SiswaProfilController;
use App\Http\Controllers\Siswa\SchoolRuleController as SiswaSchoolRuleController;
use App\Http\Controllers\WaliKelas\AbsensiController as WaliKelasAbsensiController;
use App\Http\Controllers\WaliKelas\DashboardController as WaliKelasDashboardController;
use App\Http\Controllers\WaliKelas\KelasSayaController;
use App\Http\Controllers\WaliKelas\PengajuanPoinController as WaliKelasPengajuanPoinController;
use App\Http\Controllers\WaliKelas\ProfilController as WaliKelasProfilController;
use App\Http\Controllers\WaliKelas\RiwayatPelanggaranController;
use App\Http\Controllers\AbsensiPublikController;
use App\Http\Controllers\Admin\RiwayatPesanController;
use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return view('welcome', [
            'studentCount' => ProfilSiswa::count(),
            'teacherCount' => Pengguna::whereIn('peran', ['wali_kelas', 'bk', 'kesiswaan'])->count(),
            'classCount' => Kelas::count(),
        ]);
    }

    return redirect()->route(auth()->user()->dashboardRouteName());
})->name('home');

Route::view('/privacy-policy', 'legal.privacy-policy')->name('privacy-policy');
Route::view('/terms-of-service', 'legal.terms-of-service')->name('terms-of-service');

// Akses publik absensi (link dari WhatsApp, expires end of day)
Route::get('/absensi/publik/{token}', [AbsensiPublikController::class, 'show'])->name('absensi.publik');
Route::post('/absensi/publik/{token}/cek', [AbsensiPublikController::class, 'cek'])->name('absensi.publik.cek');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'create'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'store']);
    Route::get('/daftar', [RegisterController::class, 'create'])->name('register');
    Route::post('/daftar/verifikasi', [RegisterController::class, 'verify'])->name('register.verify');
    Route::get('/daftar/lengkapi', [RegisterController::class, 'showForm'])->name('register.step2');
    Route::post('/daftar/lengkapi', [RegisterController::class, 'store'])->name('register.store');

    // Lupa & reset kata sandi
    Route::get('/lupa-sandi', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('/lupa-sandi', [ForgotPasswordController::class, 'store'])->name('password.email');
    Route::get('/reset-sandi/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-sandi', [ResetPasswordController::class, 'store'])->name('password.update');

    // Google OAuth (redirect only — callback is outside guest group to support link flow)
    Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
});

// Google OAuth callback — no auth/guest restriction, handles login/register/link modes
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AdminLoginController::class, 'destroy'])->name('logout');

    // OTP verifikasi (shared semua role)
    Route::get('/otp/verifikasi', [OtpController::class, 'show'])->name('otp.show');
    Route::post('/otp/verifikasi', [OtpController::class, 'verify'])->name('otp.verify');
    Route::post('/otp/kirim-ulang', [OtpController::class, 'resend'])->name('otp.resend');

    // Google OAuth — link existing account
    Route::get('/auth/google/link', [GoogleController::class, 'linkRedirect'])->name('google.link');
    Route::post('/auth/google/unlink', [GoogleController::class, 'unlink'])->name('google.unlink');

    // Google WhatsApp step (guru setelah Google OAuth)
    Route::get('/auth/google/whatsapp', [GoogleWhatsappController::class, 'create'])->name('google.whatsapp');
    Route::post('/auth/google/whatsapp', [GoogleWhatsappController::class, 'store'])->name('google.whatsapp.store');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profil', [AdminProfilController::class, 'show'])->name('profil');
    Route::get('/profil/edit', [AdminProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [AdminProfilController::class, 'update'])->name('profil.update');
    Route::get('/profil/ganti-sandi', [AdminProfilController::class, 'gantiSandiForm'])->name('profil.ganti-sandi');
    Route::post('/profil/ganti-sandi', [AdminProfilController::class, 'gantiSandi']);
    Route::get('/profil/set-sandi-baru', [AdminProfilController::class, 'setSandiBaruForm'])->name('profil.set-sandi-baru');
    Route::post('/profil/set-sandi-baru', [AdminProfilController::class, 'setSandiBaru']);
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
    Route::get('/settings/whatsapp/riwayat', [RiwayatPesanController::class, 'index'])->name('settings.whatsapp.riwayat');
    Route::get('/settings/whatsapp/riwayat/{pesanWhatsapp}', [RiwayatPesanController::class, 'show'])->name('settings.whatsapp.riwayat.show');
    Route::post('/settings/whatsapp/riwayat/{pesanWhatsapp}/kirim-ulang', [RiwayatPesanController::class, 'resend'])->name('settings.whatsapp.riwayat.resend');
});

Route::middleware(['auth', 'registered', 'wali-kelas'])->prefix('wali-kelas')->name('wali-kelas.')->group(function () {
    Route::get('/dashboard', [WaliKelasDashboardController::class, 'index'])->name('dashboard');
    Route::get('/kelas-saya', [KelasSayaController::class, 'index'])->name('kelas-saya');
    Route::get('/kelas-saya/status-absensi', [KelasSayaController::class, 'statusAbsensi'])->name('kelas-saya.status-absensi');
    Route::get('/kelas-saya/{studentProfile}', [KelasSayaController::class, 'show'])->name('kelas-saya.show');
    Route::put('/kelas-saya/{studentProfile}/absensi', [KelasSayaController::class, 'updateAttendance'])->name('kelas-saya.absensi.update');
    Route::get('/absensi', [WaliKelasAbsensiController::class, 'index'])->name('absensi.index');
    Route::get('/absensi/export-excel', [WaliKelasAbsensiController::class, 'exportExcel'])->name('absensi.export-excel');
    Route::get('/absensi/export-pdf', [WaliKelasAbsensiController::class, 'exportPdf'])->name('absensi.export-pdf');
    Route::get('/pelanggaran', [RiwayatPelanggaranController::class, 'index'])->name('pelanggaran');
    Route::get('/pengajuan-poin', [WaliKelasPengajuanPoinController::class, 'index'])->name('pengajuan-poin.index');
    Route::get('/pengajuan-poin/buat', [WaliKelasPengajuanPoinController::class, 'create'])->name('pengajuan-poin.create');
    Route::post('/pengajuan-poin', [WaliKelasPengajuanPoinController::class, 'store'])->name('pengajuan-poin.store');
    Route::get('/profil', [WaliKelasProfilController::class, 'show'])->name('profil');
    Route::get('/profil/edit', [WaliKelasProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [WaliKelasProfilController::class, 'update'])->name('profil.update');
    Route::post('/profil/photo', [WaliKelasProfilController::class, 'uploadPhoto'])->name('profil.photo');
    Route::delete('/profil/photo', [WaliKelasProfilController::class, 'deletePhoto'])->name('profil.photo.delete');
    Route::get('/profil/ganti-sandi', [WaliKelasProfilController::class, 'gantiSandiForm'])->name('profil.ganti-sandi');
    Route::post('/profil/ganti-sandi', [WaliKelasProfilController::class, 'gantiSandi']);
    Route::get('/profil/set-sandi-baru', [WaliKelasProfilController::class, 'setSandiBaruForm'])->name('profil.set-sandi-baru');
    Route::post('/profil/set-sandi-baru', [WaliKelasProfilController::class, 'setSandiBaru']);
});

Route::middleware(['auth', 'registered', 'bk'])->prefix('bk')->name('bk.')->group(function () {
    Route::get('/dashboard', [BkDashboardController::class, 'index'])->name('dashboard');
    Route::get('/monitoring', [BkMonitoringController::class, 'index'])->name('monitoring.index');
    Route::get('/monitoring/{monitoring}', [BkMonitoringController::class, 'show'])->name('monitoring.show');
    Route::get('/laporan', [BkAbsensiRecapController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export-excel', [BkAbsensiRecapController::class, 'exportExcel'])->name('laporan.export-excel');
    Route::get('/laporan/export-pdf', [BkAbsensiRecapController::class, 'exportPdf'])->name('laporan.export-pdf');
    Route::get('/profil', [BkProfilController::class, 'show'])->name('profil');
    Route::get('/profil/edit', [BkProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [BkProfilController::class, 'update'])->name('profil.update');
    Route::post('/profil/photo', [BkProfilController::class, 'uploadPhoto'])->name('profil.photo');
    Route::delete('/profil/photo', [BkProfilController::class, 'deletePhoto'])->name('profil.photo.delete');
    Route::get('/profil/ganti-sandi', [BkProfilController::class, 'gantiSandiForm'])->name('profil.ganti-sandi');
    Route::post('/profil/ganti-sandi', [BkProfilController::class, 'gantiSandi']);
    Route::get('/profil/set-sandi-baru', [BkProfilController::class, 'setSandiBaruForm'])->name('profil.set-sandi-baru');
    Route::post('/profil/set-sandi-baru', [BkProfilController::class, 'setSandiBaru']);
});

Route::middleware(['auth', 'registered', 'kesiswaan'])->prefix('kesiswaan')->name('kesiswaan.')->group(function () {
    Route::get('/dashboard', [KesiswaanDashboardController::class, 'index'])->name('dashboard');
    Route::get('/monitoring', [KesiswaanMonitoringController::class, 'index'])->name('monitoring.index');
    Route::get('/monitoring/{monitoring}', [KesiswaanMonitoringController::class, 'show'])->name('monitoring.show');
    Route::resource('pelanggaran-siswa', PelanggaranSiswaController::class)
        ->parameters(['pelanggaran-siswa' => 'studentViolation']);
    Route::resource('jenis-pelanggaran', JenisPelanggaranController::class)
        ->parameters(['jenis-pelanggaran' => 'violationType']);
    Route::get('/pengajuan-poin', [KesiswaanPengajuanPoinController::class, 'persetujuan'])->name('pengajuan-poin.persetujuan');
    Route::get('/pengajuan-poin/pending-count', [KesiswaanPengajuanPoinController::class, 'pendingCount'])->name('pengajuan-poin.pending-count');
    Route::put('/pengajuan-poin/{pengajuanPoin}/approve', [KesiswaanPengajuanPoinController::class, 'approve'])->name('pengajuan-poin.approve');
    Route::put('/pengajuan-poin/{pengajuanPoin}/reject', [KesiswaanPengajuanPoinController::class, 'reject'])->name('pengajuan-poin.reject');
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
    Route::get('/profil/ganti-sandi', [KesiswaanProfilController::class, 'gantiSandiForm'])->name('profil.ganti-sandi');
    Route::post('/profil/ganti-sandi', [KesiswaanProfilController::class, 'gantiSandi']);
    Route::get('/profil/set-sandi-baru', [KesiswaanProfilController::class, 'setSandiBaruForm'])->name('profil.set-sandi-baru');
    Route::post('/profil/set-sandi-baru', [KesiswaanProfilController::class, 'setSandiBaru']);
});

Route::middleware(['auth', 'registered', 'siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');
    Route::get('/poin', [SiswaProfilController::class, 'poin'])->name('poin');
    Route::get('/tata-tertib', [SiswaSchoolRuleController::class, 'index'])->name('tata-tertib.index');
    Route::get('/absensi', [SiswaAbsensiController::class, 'index'])->name('absensi');
    Route::get('/absensi/status', [SiswaAbsensiController::class, 'statusHariIni'])->name('absensi.status');
    Route::post('/absensi/cek-lokasi', [SiswaAbsensiController::class, 'checkLocation'])->name('absensi.check-location');
    Route::post('/absensi', [SiswaAbsensiController::class, 'store'])->name('absensi.store');
    Route::get('/absensi/riwayat', [SiswaAbsensiController::class, 'riwayat'])->name('absensi.riwayat');
    Route::get('/profil', [SiswaProfilController::class, 'show'])->name('profil');
    Route::get('/profil/edit', [SiswaProfilController::class, 'edit'])->name('profil.edit');
    Route::put('/profil', [SiswaProfilController::class, 'update'])->name('profil.update');
    Route::post('/profil/photo', [SiswaProfilController::class, 'uploadPhoto'])->name('profil.photo');
    Route::delete('/profil/photo', [SiswaProfilController::class, 'deletePhoto'])->name('profil.photo.delete');
    Route::get('/profil/ganti-sandi', [SiswaProfilController::class, 'gantiSandiForm'])->name('profil.ganti-sandi');
    Route::post('/profil/ganti-sandi', [SiswaProfilController::class, 'gantiSandi']);
    Route::get('/profil/set-sandi-baru', [SiswaProfilController::class, 'setSandiBaruForm'])->name('profil.set-sandi-baru');
    Route::post('/profil/set-sandi-baru', [SiswaProfilController::class, 'setSandiBaru']);
});
