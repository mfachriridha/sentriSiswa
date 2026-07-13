<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Riwayat Absensi (Siswa) — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Pengujian black box: siswa masuk lewat halaman masuk, lalu menelusuri riwayat
| kehadirannya sendiri. Hasilnya diperiksa dari apa yang muncul di layar, bukan
| dari basis data.
|
| Riwayat ditampilkan per bulan. Bulan yang tampil pertama kali adalah bulan
| berjalan, dan siswa bisa berpindah ke bulan lain lewat pilihan bulan.
| Yang diuji di berkas ini adalah pilihan bulannya: bulan yang dikenali dan yang tidak.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

// TS.RAS.002 / TC.RAS.002.001 — Positive
test('siswa berpindah ke bulan lain untuk melihat riwayatnya', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    $siswa = siswaMasuk();

    catatKehadiran($siswa->nisn, '2026-06-01', 'alpha');
    catatKehadiran($siswa->nisn, '2026-07-06', 'hadir');

    $this->get('/siswa/absensi/riwayat?month=2026-06')
        ->assertSee('Juni 2026')
        ->assertSee('Alpha');
});

// TS.RAS.006 / TC.RAS.006.001 — Positive
test('pilihan bulan yang tidak dikenali dikembalikan ke bulan berjalan', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    $siswa = siswaMasuk();

    catatKehadiran($siswa->nisn, '2026-07-06', 'hadir');

    $this->get('/siswa/absensi/riwayat?month=bulan-ngawur')
        ->assertSee('Juli 2026')
        ->assertSee('Hadir');
});
