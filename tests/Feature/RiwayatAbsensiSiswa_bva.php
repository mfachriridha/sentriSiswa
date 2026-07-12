<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Riwayat Absensi (Siswa) — Boundary Value Analysis
|--------------------------------------------------------------------------
|
| Batas yang diuji adalah tepi bulan yang dipilih. Kehadiran pada tanggal pertama
| dan tanggal terakhir bulan itu ikut tampil, sedangkan sehari sebelum atau
| sesudahnya sudah masuk bulan lain dan tidak tampil.
|
| Bulan yang dipakai adalah Juli 2026, yang berjalan dari tanggal 1 sampai 31.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

// TS.RAS.007 / TC.RAS.007.001 — Positive — batas bawah bulan
test('kehadiran pada tanggal pertama bulan itu ikut tampil', function () {
    Carbon::setTestNow('2026-08-10 08:00:00');
    $siswa = siswaMasuk();

    catatKehadiran($siswa->nisn, '2026-07-01', 'alpha');

    $this->get('/siswa/absensi/riwayat?month=2026-07')
        ->assertSee('Juli 2026')
        ->assertSee('Alpha');
});

// TS.RAS.007 / TC.RAS.007.002 — Negative — sehari di bawah batas bawah
test('kehadiran sehari sebelum bulan itu tidak tampil', function () {
    Carbon::setTestNow('2026-08-10 08:00:00');
    $siswa = siswaMasuk();

    catatKehadiran($siswa->nisn, '2026-06-30', 'alpha');

    $this->get('/siswa/absensi/riwayat?month=2026-07')
        ->assertSee('Belum ada catatan absensi pada bulan ini.');
});

// TS.RAS.008 / TC.RAS.008.001 — Positive — batas atas bulan
test('kehadiran pada tanggal terakhir bulan itu ikut tampil', function () {
    Carbon::setTestNow('2026-08-10 08:00:00');
    $siswa = siswaMasuk();

    catatKehadiran($siswa->nisn, '2026-07-31', 'alpha');

    $this->get('/siswa/absensi/riwayat?month=2026-07')
        ->assertSee('Juli 2026')
        ->assertSee('Alpha');
});

// TS.RAS.008 / TC.RAS.008.002 — Negative — sehari di atas batas atas
test('kehadiran sehari setelah bulan itu tidak tampil', function () {
    Carbon::setTestNow('2026-08-10 08:00:00');
    $siswa = siswaMasuk();

    catatKehadiran($siswa->nisn, '2026-08-01', 'alpha');

    $this->get('/siswa/absensi/riwayat?month=2026-07')
        ->assertSee('Belum ada catatan absensi pada bulan ini.');
});
