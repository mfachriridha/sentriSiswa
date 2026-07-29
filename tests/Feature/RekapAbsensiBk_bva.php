<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Rekap Absensi (BK) — Boundary Value Analysis
|--------------------------------------------------------------------------
|
| Batas yang diuji adalah rentang tanggal rekap:
|
| - Kehadiran tepat pada tanggal mulai dan tanggal selesai ikut dihitung,
|   sedangkan sehari sebelum atau sesudahnya tidak.
| - Tanggal selesai boleh sama dengan tanggal mulai, menghasilkan rekap satu hari.
|
| Rentang yang dipakai adalah 07 Juli 2026 (Selasa) sampai 09 Juli 2026 (Kamis).
| Hari tepat sebelum dan sesudahnya, 06 Juli dan 10 Juli, sama-sama hari absensi,
| jadi yang menentukan murni batas rentangnya.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

const RENTANG_REKAP_BK = '?mulai=2026-07-07&selesai=2026-07-09';

// TS.RAB.010 / TC.RAB.010.001 — Positive — batas bawah
test('kehadiran tepat pada tanggal mulai ikut dihitung', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = kelasBerisiSiswa();

    catatKehadiran($siswa->nisn, '2026-07-07', 'alpha');

    bkMasuk('10');

    $respons = $this->get('/bk/laporan'.RENTANG_REKAP_BK);

    expect(rekapBarisSiswa($respons, 'Ahmad Fauzi'))
        ->toBe(['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 1]);
});

// TS.RAB.010 / TC.RAB.010.002 — Negative — sehari di bawah batas bawah
test('kehadiran sehari sebelum tanggal mulai tidak ikut dihitung', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = kelasBerisiSiswa();

    catatKehadiran($siswa->nisn, '2026-07-06', 'alpha');
    catatKehadiran($siswa->nisn, '2026-07-08', 'hadir');

    bkMasuk('10');

    $respons = $this->get('/bk/laporan'.RENTANG_REKAP_BK);

    expect(rekapBarisSiswa($respons, 'Ahmad Fauzi'))
        ->toBe(['hadir' => 1, 'izin' => 0, 'sakit' => 0, 'alpha' => 0]);
});

// TS.RAB.011 / TC.RAB.011.001 — Positive — batas atas
test('kehadiran tepat pada tanggal selesai ikut dihitung', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = kelasBerisiSiswa();

    catatKehadiran($siswa->nisn, '2026-07-09', 'alpha');

    bkMasuk('10');

    $respons = $this->get('/bk/laporan'.RENTANG_REKAP_BK);

    expect(rekapBarisSiswa($respons, 'Ahmad Fauzi'))
        ->toBe(['hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 1]);
});

// TS.RAB.011 / TC.RAB.011.002 — Negative — sehari di atas batas atas
test('kehadiran sehari setelah tanggal selesai tidak ikut dihitung', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = kelasBerisiSiswa();

    catatKehadiran($siswa->nisn, '2026-07-08', 'hadir');
    catatKehadiran($siswa->nisn, '2026-07-10', 'alpha');

    bkMasuk('10');

    $respons = $this->get('/bk/laporan'.RENTANG_REKAP_BK);

    expect(rekapBarisSiswa($respons, 'Ahmad Fauzi'))
        ->toBe(['hadir' => 1, 'izin' => 0, 'sakit' => 0, 'alpha' => 0]);
});

// TS.RAB.012 / TC.RAB.012.001 — Positive — tepat di batas
test('rentang satu hari diterima ketika tanggal selesai sama dengan tanggal mulai', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = kelasBerisiSiswa();

    catatKehadiran($siswa->nisn, '2026-07-07', 'hadir');

    bkMasuk('10');

    $respons = $this->get('/bk/laporan?mulai=2026-07-07&selesai=2026-07-07')
        ->assertSee('Ahmad Fauzi');

    expect(rekapBarisSiswa($respons, 'Ahmad Fauzi'))
        ->toBe(['hadir' => 1, 'izin' => 0, 'sakit' => 0, 'alpha' => 0]);
});
