<?php

use App\Models\Pengaturan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Absensi (Siswa) — Boundary Value Analysis
|--------------------------------------------------------------------------
|
| Tiga batas yang diuji, seluruhnya pada hari Senin 06 Juli 2026:
|
| 1. Jam absen dibuka pukul 06:30. Semenit sebelumnya masih ditolak.
| 2. Jam absen ditutup pukul 07:00. Semenit sesudahnya sudah ditolak.
| 3. Batas keterlambatan pukul 06:45. Absen sampai menit itu tercatat Hadir,
|    semenit sesudahnya tercatat Terlambat.
|
| Ditambah batas ukuran selfie, yaitu paling besar 300 KB.
|
*/

beforeEach(function () {
    Storage::fake('public');
    Pengaturan::set('attendance_start_time', '06:30');
    Pengaturan::set('attendance_end_time', '07:00');
    Pengaturan::set('attendance_late_tolerance_minutes', 15); // Terlambat setelah 06:45.
});

afterEach(function () {
    Carbon::setTestNow();
});

// TS.ABS.012 / TC.ABS.012.001 — Negative — semenit sebelum jam absen dibuka
test('absen pukul 06:29 ditolak', function () {
    Carbon::setTestNow('2026-07-06 06:29:00');
    siswaMasuk();

    $this->followingRedirects()
        ->post('/siswa/absensi', ['selfie' => selfieAbsensi()])
        ->assertSee('Waktu absen sudah lewat atau belum dimulai.');
});

// TS.ABS.012 / TC.ABS.012.002 — Positive — tepat saat jam absen dibuka
test('absen pukul 06:30 diterima dan tercatat hadir', function () {
    Carbon::setTestNow('2026-07-06 06:30:00');
    siswaMasuk();

    $this->followingRedirects()
        ->post('/siswa/absensi', ['selfie' => selfieAbsensi()])
        ->assertSee('Absen berhasil: Hadir.');
});

// TS.ABS.013 / TC.ABS.013.001 — Positive — tepat pada batas keterlambatan
test('absen pukul 06:45 masih tercatat hadir', function () {
    Carbon::setTestNow('2026-07-06 06:45:00');
    siswaMasuk();

    $this->followingRedirects()
        ->post('/siswa/absensi', ['selfie' => selfieAbsensi()])
        ->assertSee('Absen berhasil: Hadir.');
});

// TS.ABS.013 / TC.ABS.013.002 — Positive — semenit setelah batas keterlambatan
test('absen pukul 06:46 tercatat terlambat', function () {
    Carbon::setTestNow('2026-07-06 06:46:00');
    siswaMasuk();

    $this->followingRedirects()
        ->post('/siswa/absensi', ['selfie' => selfieAbsensi()])
        ->assertSee('Absen tercatat: Terlambat.');
});

// TS.ABS.014 / TC.ABS.014.001 — Positive — tepat saat jam absen ditutup
test('absen pukul 07:00 masih diterima', function () {
    Carbon::setTestNow('2026-07-06 07:00:00');
    siswaMasuk();

    $this->followingRedirects()
        ->post('/siswa/absensi', ['selfie' => selfieAbsensi()])
        ->assertSee('Absen tercatat: Terlambat.');
});

// TS.ABS.014 / TC.ABS.014.002 — Negative — semenit setelah jam absen ditutup
test('absen pukul 07:01 ditolak', function () {
    Carbon::setTestNow('2026-07-06 07:01:00');
    siswaMasuk();

    $this->followingRedirects()
        ->post('/siswa/absensi', ['selfie' => selfieAbsensi()])
        ->assertSee('Waktu absen sudah lewat atau belum dimulai.');
});

// TS.ABS.015 / TC.ABS.015.001 — Positive — tepat pada batas ukuran selfie
test('selfie berukuran tepat 300 KB diterima', function () {
    Carbon::setTestNow('2026-07-06 06:35:00');
    siswaMasuk();

    $this->followingRedirects()
        ->post('/siswa/absensi', ['selfie' => selfieAbsensi(300)])
        ->assertSee('Absen berhasil: Hadir.');
});

// TS.ABS.015 / TC.ABS.015.002 — Negative — di atas batas ukuran selfie
test('selfie berukuran lebih dari 300 KB ditolak', function () {
    Carbon::setTestNow('2026-07-06 06:35:00');
    siswaMasuk();

    $this->from('/siswa/absensi')
        ->followingRedirects()
        ->post('/siswa/absensi', ['selfie' => selfieAbsensi(301)])
        ->assertSee('Ukuran foto maksimal 300 KB.');
});
