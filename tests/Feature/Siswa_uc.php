<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Data Siswa (Admin) — Use Case Testing
|--------------------------------------------------------------------------
|
| Pengujian black box: admin masuk lewat halaman masuk, lalu mengelola data
| siswa seperti pengguna biasa. Hasilnya diperiksa dari apa yang muncul di
| layar, bukan dari basis data.
| Alur pemakaian tanpa isian: admin menghapus data seorang siswa dari sekolah.
|
*/

// TS.SIS.009 / TC.SIS.009.001 — Positive
test('admin berhasil menghapus data siswa', function () {
    adminDataSiswa();
    $siswa = siswaTercatat('Siswa Dihapus', '1234567899', '10009');

    $this->followingRedirects()
        ->delete("/admin/siswa/{$siswa->pengguna_id}")
        ->assertSee('Siswa berhasil dihapus.')
        ->assertDontSee('Siswa Dihapus');
});
