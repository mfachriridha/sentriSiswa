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
