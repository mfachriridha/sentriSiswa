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

// TS.PLS.014 / TC.PLS.014.001 — Negative — alur pengecualian: jenis pelanggaran belum ada
test('mencatat pelanggaran ketika jenis pelanggarannya belum ada sama sekali', function () {
    kelasBerisiSiswa();
    kesiswaanMasuk();

    // Keadaan yang dialami setiap sekolah yang baru memasang aplikasinya. Tanpa
    // penjagaan ini, formulirnya tetap tampil utuh - tetapi kolom jenis pelanggarannya
    // kosong dan wajib diisi, jadi formulir itu mustahil diselesaikan. Penggunanya
    // terjebak tanpa tahu bahwa yang kurang adalah data yang harus ia buat sendiri.
    // Judul halamannya tetap "Catat Pelanggaran Siswa" - itu nama menunya. Yang
    // membuktikan formulirnya benar-benar tidak ditampilkan adalah hilangnya tombol
    // Simpan dan kolom isiannya.
    $this->get('/kesiswaan/pelanggaran-siswa/create')
        ->assertSee('Belum Ada Jenis Pelanggaran')
        ->assertSee('Tambah Jenis Pelanggaran')
        ->assertDontSee('Simpan')
        ->assertDontSee('Cari nama, NIS, atau NISN siswa...');
});
