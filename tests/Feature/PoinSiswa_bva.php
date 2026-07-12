<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Poin Saya (Siswa) — Boundary Value Analysis
|--------------------------------------------------------------------------
|
| Dua batas yang diuji:
|
| 1. Keterangan poin: di atas 75 berarti Baik, di atas 50 berarti Cukup, sisanya
|    Perhatian. Batasnya diuji tepat pada dan tepat di bawah tiap nilai itu.
| 2. Poin paling rendah adalah 0. Potongan yang melebihi poin tersisa membuat
|    poinnya berhenti di 0, bukan menjadi angka minus.
|
*/

// TS.POS.006 / TC.POS.006.001 — Positive — tepat pada batas Baik
test('poin 76 diberi keterangan Baik', function () {
    $siswa = siswaMasuk();

    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06', 24);

    $this->get('/siswa/poin')
        ->assertSee('76')
        ->assertSee('Baik');
});

// TS.POS.006 / TC.POS.006.002 — Positive — sepoin di bawah batas Baik
test('poin 75 diberi keterangan Cukup', function () {
    $siswa = siswaMasuk();

    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06', 25);

    $this->get('/siswa/poin')
        ->assertSee('75')
        ->assertSee('Cukup');
});

// TS.POS.007 / TC.POS.007.001 — Positive — tepat pada batas Cukup
test('poin 51 diberi keterangan Cukup', function () {
    $siswa = siswaMasuk();

    catatPelanggaran($siswa, 'Berkelahi', 'berat', '2026-07-06', 49);

    $this->get('/siswa/poin')
        ->assertSee('51')
        ->assertSee('Cukup');
});

// TS.POS.007 / TC.POS.007.002 — Positive — sepoin di bawah batas Cukup
test('poin 50 diberi keterangan Perhatian', function () {
    $siswa = siswaMasuk();

    catatPelanggaran($siswa, 'Berkelahi', 'berat', '2026-07-06', 50);

    $this->get('/siswa/poin')
        ->assertSee('50')
        ->assertSee('Perhatian');
});

// TS.POS.008 / TC.POS.008.001 — Positive — tepat pada batas terendah
test('poin berhenti di nol ketika potongannya pas menghabiskan poin', function () {
    $siswa = siswaMasuk();

    catatPelanggaran($siswa, 'Membawa senjata tajam', 'sangat_berat', '2026-07-06', 100);

    $this->get('/siswa/poin')
        ->assertSeeInOrder(['Sisa Poin Disiplin', '0'])
        ->assertSee('Dari 100 poin · 100 poin terpakai')
        ->assertSee('Perhatian');
});

// TS.POS.008 / TC.POS.008.002 — Positive — di bawah batas terendah
test('poin tetap nol dan tidak minus ketika potongannya melebihi poin yang tersisa', function () {
    $siswa = siswaMasuk();

    // Poin awal 100, dipotong 100 lalu dipotong 10 lagi. Poinnya berhenti di nol.
    catatPelanggaran($siswa, 'Membawa senjata tajam', 'sangat_berat', '2026-07-06', 100);
    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-07', 10);

    // Meski potongannya 110, sisa poinnya berhenti di nol, bukan angka minus.
    $this->get('/siswa/poin')
        ->assertSeeInOrder(['Sisa Poin Disiplin', '0'])
        ->assertSee('Dari 100 poin · 110 poin terpakai')
        ->assertSee('Perhatian');
});
