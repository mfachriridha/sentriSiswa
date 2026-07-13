<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Lokasi Absen (Admin) — Use Case Testing
|--------------------------------------------------------------------------
|
| Pengujian black box: admin masuk lewat halaman masuk, lalu mengatur area
| absensi seperti pengguna biasa. Hasilnya diperiksa dari apa yang muncul di
| layar, bukan dari basis data.
|
| Area absensi digambar di Google My Maps lalu diunduh sebagai berkas KML.
| Admin mengunggah berkas itu, dan boleh menambahkan toleransi jarak agar
| siswa yang berada sedikit di luar garis area tetap bisa absen.
| Alur pemakaian tanpa isian: admin menghapus area absensi yang tidak dipakai lagi.
|
*/

// TS.LKA.007 / TC.LKA.007.001 — Positive
test('admin berhasil menghapus area absensi yang sudah dipasang', function () {
    adminLokasiAbsen();

    $this->followingRedirects()
        ->put('/admin/pengaturan/lokasi-absen', ['kml_file' => berkasAreaSekolah()])
        ->assertSee('Area absensi berhasil diimpor.');

    $this->followingRedirects()
        ->delete('/admin/pengaturan/lokasi-absen')
        ->assertSee('Lokasi absen berhasil dihapus.');
});
