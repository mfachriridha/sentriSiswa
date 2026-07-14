<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Tata Tertib (Kesiswaan) — Use Case Testing
|--------------------------------------------------------------------------
|
| Alur pemakaian tanpa isian: kesiswaan menghapus berkas tata tertib yang tidak
| dipakai lagi, dan membuka daftarnya ketika belum ada berkas sama sekali.
|
*/

beforeEach(function () {
    Storage::fake('public');
});

// TS.TTK.007 / TC.TTK.007.001 — Positive
test('kesiswaan menghapus tata tertib', function () {
    kesiswaanMasuk();
    $tataTertib = tataTertibTersimpan('Tata Tertib Sekolah 2026');

    $this->followingRedirects()
        ->delete("/kesiswaan/tata-tertib/{$tataTertib->id}")
        ->assertSee('Tata tertib berhasil dihapus.')
        ->assertSee('Belum ada file tata tertib.');
});

// TS.TTK.008 / TC.TTK.008.001 — Positive — alur alternatif: daftar masih kosong
test('daftar tata tertib yang masih kosong menampilkan keterangannya', function () {
    kesiswaanMasuk();

    $this->get('/kesiswaan/tata-tertib')
        ->assertSee('Belum ada file tata tertib.');
});

// TS.TTK.014 / TC.TTK.014.001 — Positive
test('berkas tata tertib yang dibuka siswa bernama sesuai judulnya', function () {
    [, , $siswa] = kelasBerisiSiswa();

    kesiswaanMasuk();
    $this->post('/kesiswaan/tata-tertib', [
        'judul' => 'Tata Tertib Sekolah 2026',
        'file_pdf' => berkasTataTertib(),
        'dipublikasikan' => 1,
    ]);
    $this->post('/logout');

    masukSebagai($siswa->pengguna);

    // Siswa membukanya lewat peramban ponsel, dan yang tertulis di sana adalah nama
    // berkasnya. Kalau ia berupa deretan huruf acak, tidak ada petunjuk sama sekali
    // bahwa itu berkas yang benar.
    $this->get('/siswa/tata-tertib')
        ->assertSee('Tata Tertib Sekolah 2026')
        ->assertSee('tata-tertib-sekolah-2026', escape: false);
});
