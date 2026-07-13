<?php

use App\Models\JenisPelanggaran;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Jenis Pelanggaran (Kesiswaan) — Use Case Testing
|--------------------------------------------------------------------------
|
| Alur pemakaian tanpa isian: kesiswaan menghapus jenis pelanggaran yang belum
| pernah dipakai, dan membuka daftarnya ketika masih kosong.
|
*/

// TS.JEP.006 / TC.JEP.006.001 — Positive
test('kesiswaan menghapus jenis pelanggaran yang belum pernah dipakai', function () {
    kesiswaanMasuk();
    $jenis = JenisPelanggaran::create(dataJenisPelanggaran());

    $this->followingRedirects()
        ->delete("/kesiswaan/jenis-pelanggaran/{$jenis->id}")
        ->assertSee('Jenis pelanggaran berhasil dihapus.')
        ->assertSee('Belum ada data jenis pelanggaran.');
});

// TS.JEP.011 / TC.JEP.011.001 — Positive — alur alternatif: daftar masih kosong
test('daftar jenis pelanggaran yang masih kosong menampilkan keterangannya', function () {
    kesiswaanMasuk();

    $this->get('/kesiswaan/jenis-pelanggaran')
        ->assertSee('Belum ada data jenis pelanggaran.');
});
