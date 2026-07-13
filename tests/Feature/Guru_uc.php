<?php

use App\Models\Kelas;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Data Guru (Admin) — Use Case Testing
|--------------------------------------------------------------------------
|
| Pengujian black box: admin masuk lewat halaman masuk, lalu mengelola data
| guru seperti pengguna biasa. Hasilnya diperiksa dari apa yang muncul di
| layar, bukan dari basis data.
|
| Guru punya tiga peran: wali kelas, BK, dan kesiswaan. Guru BK wajib memilih
| tingkat yang dipegangnya; wali kelas boleh langsung dipilihkan kelasnya.
| Alur pemakaian tanpa isian: admin menghapus data seorang guru dari sekolah.
|
*/

// TS.GUR.010 / TC.GUR.010.001 — Positive
test('admin berhasil menghapus data guru', function () {
    adminDataGuru();
    $guru = guruTercatat('Guru Dihapus', '198501012020121011');

    $this->followingRedirects()
        ->delete("/admin/guru/{$guru->pengguna_id}")
        ->assertSee('Guru berhasil dihapus.')
        ->assertDontSee('Guru Dihapus');
});
