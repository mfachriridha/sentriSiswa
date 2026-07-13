<?php

use App\Models\Kelas;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Data Kelas (Admin) — Use Case Testing
|--------------------------------------------------------------------------
|
| Pengujian black box: admin masuk lewat halaman masuk, lalu mengelola kelas
| seperti pengguna biasa. Hasilnya diperiksa dari apa yang muncul di layar,
| bukan dari basis data.
|
| Saat menambah kelas, admin mengisi tingkat dan nama kelas secara terpisah;
| keduanya digabung menjadi nama lengkap, misalnya tingkat 10 dan nama "IPA 1"
| menjadi "10 IPA 1". Admin juga bisa langsung memilihkan wali kelas dan
| memasukkan beberapa siswa sekaligus.
| Alur pemakaian tanpa isian: admin menghapus sebuah kelas.
|
*/

// TS.KEL.009 / TC.KEL.009.001 — Positive
test('admin berhasil menghapus kelas', function () {
    adminDataKelas();
    $kelas = Kelas::create(['nama' => '10 IPA 8', 'tingkat' => '10']);

    $this->followingRedirects()
        ->delete("/admin/kelas/{$kelas->id}")
        ->assertSee('Kelas berhasil dihapus.')
        ->assertDontSee('10 IPA 8');
});
