<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Riwayat Pelanggaran (Wali Kelas) — Boundary Value Analysis
|--------------------------------------------------------------------------
|
| Batas yang diuji adalah rentang tanggal penyaringan:
|
| - Pelanggaran yang terjadi tepat pada tanggal mulai dan tanggal selesai ikut
|   tampil, sedangkan yang sehari sebelum atau sesudahnya tidak.
| - Tanggal selesai boleh sama dengan tanggal mulai (riwayat satu hari), tetapi
|   tidak boleh lebih awal.
|
| Rentang yang dipakai adalah 07 Juli 2026 sampai 09 Juli 2026.
|
*/

const RENTANG_RIWAYAT = '?date_from=2026-07-07&date_to=2026-07-09';

// TS.RIP.009 / TC.RIP.009.001 — Positive — batas bawah
test('pelanggaran tepat pada tanggal mulai ikut tampil', function () {
    [, , $siswa] = waliKelasDenganKelas();

    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-07');

    $this->get('/wali-kelas/pelanggaran'.RENTANG_RIWAYAT)
        ->assertSee('Terlambat masuk kelas');
});

// TS.RIP.009 / TC.RIP.009.002 — Negative — sehari di bawah batas bawah
test('pelanggaran sehari sebelum tanggal mulai tidak tampil', function () {
    [, , $siswa] = waliKelasDenganKelas();

    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06');

    $this->get('/wali-kelas/pelanggaran'.RENTANG_RIWAYAT)
        ->assertDontSee('Terlambat masuk kelas')
        ->assertSee('Belum ada riwayat pelanggaran untuk kelas ini.');
});

// TS.RIP.010 / TC.RIP.010.001 — Positive — batas atas
test('pelanggaran tepat pada tanggal selesai ikut tampil', function () {
    [, , $siswa] = waliKelasDenganKelas();

    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-09');

    $this->get('/wali-kelas/pelanggaran'.RENTANG_RIWAYAT)
        ->assertSee('Terlambat masuk kelas');
});

// TS.RIP.010 / TC.RIP.010.002 — Negative — sehari di atas batas atas
test('pelanggaran sehari setelah tanggal selesai tidak tampil', function () {
    [, , $siswa] = waliKelasDenganKelas();

    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-10');

    $this->get('/wali-kelas/pelanggaran'.RENTANG_RIWAYAT)
        ->assertDontSee('Terlambat masuk kelas')
        ->assertSee('Belum ada riwayat pelanggaran untuk kelas ini.');
});

// TS.RIP.011 / TC.RIP.011.001 — Positive — tepat di batas
test('rentang satu hari diterima ketika tanggal selesai sama dengan tanggal mulai', function () {
    [, , $siswa] = waliKelasDenganKelas();

    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-07');

    $this->get('/wali-kelas/pelanggaran?date_from=2026-07-07&date_to=2026-07-07')
        ->assertSee('Terlambat masuk kelas')
        ->assertDontSee('Tanggal selesai harus sama dengan atau setelah tanggal mulai.');
});

// TS.RIP.011 / TC.RIP.011.002 — Negative — sehari di bawah batas
test('rentang riwayat ditolak ketika tanggal selesai sehari lebih awal daripada tanggal mulai', function () {
    waliKelasDenganKelas();

    $this->from('/wali-kelas/pelanggaran')
        ->followingRedirects()
        ->get('/wali-kelas/pelanggaran?date_from=2026-07-07&date_to=2026-07-06')
        ->assertSee('Tanggal selesai harus sama dengan atau setelah tanggal mulai.');
});
