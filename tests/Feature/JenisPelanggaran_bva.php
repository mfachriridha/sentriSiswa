<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Jenis Pelanggaran (Kesiswaan) — Boundary Value Analysis
|--------------------------------------------------------------------------
|
| Batas yang diuji adalah poin pelanggaran. Ada dua lapis batas:
|
| 1. Batas menyeluruh, berlaku untuk kategori apa pun: 5 sampai 100 poin.
| 2. Batas per kategori, yang lebih sempit:
|
|      Sanksi Ringan          5 - 25
|      Sanksi Sedang         26 - 50
|      Sanksi Berat          51 - 75
|      Sanksi Sangat Berat   76 - 100
|
| Tiap batas diuji tepat di bawah, tepat pada, dan tepat di atas nilainya.
|
*/

// TS.JEP.012 / TC.JEP.012.001 — Negative — sepoin di bawah batas terendah
test('poin 4 ditolak karena di bawah batas terendah', function () {
    kesiswaanMasuk();

    $this->from('/kesiswaan/jenis-pelanggaran/create')
        ->followingRedirects()
        ->post('/kesiswaan/jenis-pelanggaran', dataJenisPelanggaran([
            'kategori' => 'ringan',
            'pengurangan_poin' => 4,
        ]))
        ->assertSee('Poin pelanggaran minimal 5.');
});

// TS.JEP.012 / TC.JEP.012.002 — Positive — tepat di batas terendah
test('poin 5 diterima untuk kategori sanksi ringan', function () {
    kesiswaanMasuk();

    $this->followingRedirects()
        ->post('/kesiswaan/jenis-pelanggaran', dataJenisPelanggaran([
            'kategori' => 'ringan',
            'pengurangan_poin' => 5,
        ]))
        ->assertSee('Jenis pelanggaran berhasil ditambahkan.');
});

// TS.JEP.013 / TC.JEP.013.001 — Positive — tepat di batas atas kategori ringan
test('poin 25 diterima untuk kategori sanksi ringan', function () {
    kesiswaanMasuk();

    $this->followingRedirects()
        ->post('/kesiswaan/jenis-pelanggaran', dataJenisPelanggaran([
            'kategori' => 'ringan',
            'pengurangan_poin' => 25,
        ]))
        ->assertSee('Jenis pelanggaran berhasil ditambahkan.');
});

// TS.JEP.013 / TC.JEP.013.002 — Negative — sepoin di atas batas atas kategori ringan
test('poin 26 ditolak untuk kategori sanksi ringan', function () {
    kesiswaanMasuk();

    $this->from('/kesiswaan/jenis-pelanggaran/create')
        ->followingRedirects()
        ->post('/kesiswaan/jenis-pelanggaran', dataJenisPelanggaran([
            'kategori' => 'ringan',
            'pengurangan_poin' => 26,
        ]))
        ->assertSee('Poin untuk kategori ini harus berada di antara 5 sampai 25.');
});

// TS.JEP.014 / TC.JEP.014.001 — Positive — tepat di batas bawah kategori sedang
test('poin 26 diterima untuk kategori sanksi sedang', function () {
    kesiswaanMasuk();

    $this->followingRedirects()
        ->post('/kesiswaan/jenis-pelanggaran', dataJenisPelanggaran([
            'kategori' => 'sedang',
            'pengurangan_poin' => 26,
        ]))
        ->assertSee('Jenis pelanggaran berhasil ditambahkan.');
});

// TS.JEP.014 / TC.JEP.014.002 — Negative — sepoin di bawah batas bawah kategori sedang
test('poin 25 ditolak untuk kategori sanksi sedang', function () {
    kesiswaanMasuk();

    $this->from('/kesiswaan/jenis-pelanggaran/create')
        ->followingRedirects()
        ->post('/kesiswaan/jenis-pelanggaran', dataJenisPelanggaran([
            'kategori' => 'sedang',
            'pengurangan_poin' => 25,
        ]))
        ->assertSee('Poin untuk kategori ini harus berada di antara 26 sampai 50.');
});

// TS.JEP.015 / TC.JEP.015.001 — Positive — tepat di batas tertinggi
test('poin 100 diterima untuk kategori sanksi sangat berat', function () {
    kesiswaanMasuk();

    $this->followingRedirects()
        ->post('/kesiswaan/jenis-pelanggaran', dataJenisPelanggaran([
            'kategori' => 'sangat_berat',
            'pengurangan_poin' => 100,
        ]))
        ->assertSee('Jenis pelanggaran berhasil ditambahkan.');
});

// TS.JEP.015 / TC.JEP.015.002 — Negative — sepoin di atas batas tertinggi
test('poin 101 ditolak karena melebihi batas tertinggi', function () {
    kesiswaanMasuk();

    $this->from('/kesiswaan/jenis-pelanggaran/create')
        ->followingRedirects()
        ->post('/kesiswaan/jenis-pelanggaran', dataJenisPelanggaran([
            'kategori' => 'sangat_berat',
            'pengurangan_poin' => 101,
        ]))
        ->assertSee('Poin pelanggaran maksimal 100.');
});
