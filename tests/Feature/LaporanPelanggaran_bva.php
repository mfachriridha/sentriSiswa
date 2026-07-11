<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Laporan Kesiswaan — Boundary Value Analysis
|--------------------------------------------------------------------------
|
| Batas yang diuji adalah rentang tanggal laporan:
|
| - Pelanggaran yang terjadi tepat pada tanggal mulai dan tanggal selesai ikut
|   masuk laporan, sedangkan yang sehari sebelum atau sesudahnya tidak.
| - Tanggal selesai boleh sama dengan tanggal mulai (laporan satu hari), tetapi
|   tidak boleh lebih awal.
|
| Rentang yang dipakai adalah 07 Juli 2026 sampai 09 Juli 2026.
|
*/

const RENTANG_LAPORAN = '?mulai=2026-07-07&selesai=2026-07-09';

// TS.LAP.011 / TC.LAP.011.001 — Positive — batas bawah
test('pelanggaran tepat pada tanggal mulai ikut masuk laporan', function () {
    [, , $siswa] = kelasBerisiSiswa();
    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-07', 10);

    kesiswaanMasuk();

    $this->get('/kesiswaan/laporan'.RENTANG_LAPORAN)
        ->assertSee('Terlambat masuk kelas');
});

// TS.LAP.011 / TC.LAP.011.002 — Negative — sehari di bawah batas bawah
test('pelanggaran sehari sebelum tanggal mulai tidak masuk laporan', function () {
    [, , $siswa] = kelasBerisiSiswa();
    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06', 10);

    kesiswaanMasuk();

    $this->get('/kesiswaan/laporan'.RENTANG_LAPORAN)
        ->assertDontSee('Terlambat masuk kelas')
        ->assertSee('Tidak ada data laporan.');
});

// TS.LAP.012 / TC.LAP.012.001 — Positive — batas atas
test('pelanggaran tepat pada tanggal selesai ikut masuk laporan', function () {
    [, , $siswa] = kelasBerisiSiswa();
    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-09', 10);

    kesiswaanMasuk();

    $this->get('/kesiswaan/laporan'.RENTANG_LAPORAN)
        ->assertSee('Terlambat masuk kelas');
});

// TS.LAP.012 / TC.LAP.012.002 — Negative — sehari di atas batas atas
test('pelanggaran sehari setelah tanggal selesai tidak masuk laporan', function () {
    [, , $siswa] = kelasBerisiSiswa();
    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-10', 10);

    kesiswaanMasuk();

    $this->get('/kesiswaan/laporan'.RENTANG_LAPORAN)
        ->assertDontSee('Terlambat masuk kelas')
        ->assertSee('Tidak ada data laporan.');
});

// TS.LAP.013 / TC.LAP.013.001 — Positive — tepat di batas
test('rentang satu hari diterima ketika tanggal selesai sama dengan tanggal mulai', function () {
    [, , $siswa] = kelasBerisiSiswa();
    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-07', 10);

    kesiswaanMasuk();

    $this->get('/kesiswaan/laporan?mulai=2026-07-07&selesai=2026-07-07')
        ->assertSee('Terlambat masuk kelas')
        ->assertDontSee('Tanggal selesai harus sama dengan atau setelah tanggal mulai.');
});

// TS.LAP.013 / TC.LAP.013.002 — Negative — sehari di bawah batas
test('rentang laporan ditolak ketika tanggal selesai sehari lebih awal daripada tanggal mulai', function () {
    kelasBerisiSiswa();
    kesiswaanMasuk();

    $this->from('/kesiswaan/laporan')
        ->followingRedirects()
        ->get('/kesiswaan/laporan?mulai=2026-07-07&selesai=2026-07-06')
        ->assertSee('Tanggal selesai harus sama dengan atau setelah tanggal mulai.');
});
