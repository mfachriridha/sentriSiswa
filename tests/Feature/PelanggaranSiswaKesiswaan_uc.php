<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Pelanggaran Siswa (Kesiswaan) — Use Case Testing
|--------------------------------------------------------------------------
|
| Alur pemakaian tanpa isian: kesiswaan membuka rincian sebuah catatan pelanggaran,
| dan membuka daftarnya ketika belum ada catatan sama sekali.
|
*/

// TS.PLS.009 / TC.PLS.009.001 — Positive
test('kesiswaan melihat rincian sebuah catatan pelanggaran', function () {
    [, , $siswa] = kelasBerisiSiswa();
    $pelanggaran = catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06');

    kesiswaanMasuk();

    $this->get("/kesiswaan/pelanggaran-siswa/{$pelanggaran->id}")
        ->assertSee('Ahmad Fauzi')
        ->assertSee('Terlambat masuk kelas')
        ->assertSee('10 IPA 1');
});

// TS.PLS.011 / TC.PLS.011.001 — Positive — alur alternatif: belum ada catatan
test('daftar pelanggaran siswa yang masih kosong menampilkan keterangannya', function () {
    kesiswaanMasuk();

    $this->get('/kesiswaan/pelanggaran-siswa')
        ->assertSee('Belum ada catatan pelanggaran siswa.');
});
