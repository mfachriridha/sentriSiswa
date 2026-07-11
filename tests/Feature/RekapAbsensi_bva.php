<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Rekap Absensi (Wali Kelas) — Boundary Value Analysis
|--------------------------------------------------------------------------
|
| Batas yang diuji adalah rentang tanggal rekap:
|
| - Kehadiran tepat pada tanggal mulai dan tanggal selesai ikut dihitung,
|   sedangkan sehari sebelum atau sesudahnya tidak.
| - Tanggal selesai boleh sama dengan tanggal mulai (rekap satu hari), tetapi
|   tidak boleh lebih awal.
|
| Rentang yang dipakai adalah 07 Juli 2026 (Selasa) sampai 09 Juli 2026 (Kamis).
| Hari tepat sebelum dan sesudahnya, 06 Juli (Senin) dan 10 Juli (Jumat),
| sama-sama hari absensi, jadi yang menentukan murni batas rentangnya.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

const RENTANG_REKAP = '?mulai=2026-07-07&selesai=2026-07-09';

// TS.REA.011 / TC.REA.011.001 — Positive — batas bawah
test('kehadiran tepat pada tanggal mulai ikut dihitung', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = waliKelasDenganKelas();

    catatKehadiran($siswa->nisn, '2026-07-07', 'alpha');

    // Alpha satu-satunya di dalam rentang, jadi kehadirannya nol persen.
    $this->get('/wali-kelas/absensi'.RENTANG_REKAP)
        ->assertSee('0%');
});

// TS.REA.011 / TC.REA.011.002 — Negative — sehari di bawah batas bawah
test('kehadiran sehari sebelum tanggal mulai tidak ikut dihitung', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = waliKelasDenganKelas();

    catatKehadiran($siswa->nisn, '2026-07-06', 'alpha');  // Sehari sebelum rentang.
    catatKehadiran($siswa->nisn, '2026-07-08', 'hadir');

    // Alpha di luar rentang diabaikan, jadi kehadirannya tetap penuh.
    $this->get('/wali-kelas/absensi'.RENTANG_REKAP)
        ->assertSee('100%');
});

// TS.REA.012 / TC.REA.012.001 — Positive — batas atas
test('kehadiran tepat pada tanggal selesai ikut dihitung', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = waliKelasDenganKelas();

    catatKehadiran($siswa->nisn, '2026-07-09', 'alpha');

    $this->get('/wali-kelas/absensi'.RENTANG_REKAP)
        ->assertSee('0%');
});

// TS.REA.012 / TC.REA.012.002 — Negative — sehari di atas batas atas
test('kehadiran sehari setelah tanggal selesai tidak ikut dihitung', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = waliKelasDenganKelas();

    catatKehadiran($siswa->nisn, '2026-07-08', 'hadir');
    catatKehadiran($siswa->nisn, '2026-07-10', 'alpha');  // Sehari setelah rentang.

    $this->get('/wali-kelas/absensi'.RENTANG_REKAP)
        ->assertSee('100%');
});

// TS.REA.013 / TC.REA.013.001 — Positive — tepat di batas
test('rentang satu hari diterima ketika tanggal selesai sama dengan tanggal mulai', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = waliKelasDenganKelas();

    catatKehadiran($siswa->nisn, '2026-07-07', 'hadir');

    $this->get('/wali-kelas/absensi?mulai=2026-07-07&selesai=2026-07-07')
        ->assertSee('Ahmad Fauzi')
        ->assertSee('100%')
        ->assertDontSee('Tanggal selesai harus sama dengan atau setelah tanggal mulai.');
});

// TS.REA.013 / TC.REA.013.002 — Negative — sehari di bawah batas
test('rentang ditolak ketika tanggal selesai sehari lebih awal daripada tanggal mulai', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    waliKelasDenganKelas();

    $this->from('/wali-kelas/absensi')
        ->followingRedirects()
        ->get('/wali-kelas/absensi?mulai=2026-07-07&selesai=2026-07-06')
        ->assertSee('Tanggal selesai harus sama dengan atau setelah tanggal mulai.');
});
