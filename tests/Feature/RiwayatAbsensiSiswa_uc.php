<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Riwayat Absensi (Siswa) — Use Case Testing
|--------------------------------------------------------------------------
|
| Pengujian black box: siswa masuk lewat halaman masuk, lalu menelusuri riwayat
| kehadirannya sendiri. Hasilnya diperiksa dari apa yang muncul di layar, bukan
| dari basis data.
|
| Riwayat ditampilkan per bulan. Bulan yang tampil pertama kali adalah bulan
| berjalan, dan siswa bisa berpindah ke bulan lain lewat pilihan bulan.
| Alur pemakaian tanpa isian: siswa membaca riwayat kehadirannya sendiri. Termasuk
| alur alternatifnya (bulan yang belum punya catatan) dan pengecualiannya (kehadiran
| siswa lain tidak boleh terbaca).
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

// TS.RAS.001 / TC.RAS.001.001 — Positive
test('siswa melihat riwayat kehadirannya di bulan berjalan', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    $siswa = siswaMasuk();

    catatKehadiran($siswa->nisn, '2026-07-06', 'hadir');
    catatKehadiran($siswa->nisn, '2026-07-07', 'sakit');

    $this->get('/siswa/absensi/riwayat')
        ->assertSee('Juli 2026')
        ->assertSee('Hadir')
        ->assertSee('Sakit');
});

// TS.RAS.003 / TC.RAS.003.001 — Negative
test('riwayat bulan berjalan tidak memuat kehadiran dari bulan lain', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    $siswa = siswaMasuk();

    catatKehadiran($siswa->nisn, '2026-06-01', 'alpha');

    // Kehadiran bulan Juni tidak ikut tampil di riwayat bulan Juli.
    $this->get('/siswa/absensi/riwayat')
        ->assertSee('Juli 2026')
        ->assertSee('Belum ada catatan absensi pada bulan ini.');
});

// TS.RAS.004 / TC.RAS.004.001 — Positive
test('bulan yang belum punya catatan menampilkan keterangannya', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    siswaMasuk();

    $this->get('/siswa/absensi/riwayat')
        ->assertSee('Belum ada catatan absensi pada bulan ini.');
});

// TS.RAS.005 / TC.RAS.005.001 — Negative
test('siswa tidak melihat kehadiran siswa lain di riwayatnya', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    $siswa = siswaMasuk();

    $siswaLain = siswaLainDiKelas($siswa->kelas_id, 'Siti Aminah', '1234567892', '10003');
    catatKehadiran($siswaLain->nisn, '2026-07-06', 'alpha');

    $this->get('/siswa/absensi/riwayat')
        ->assertSee('Belum ada catatan absensi pada bulan ini.');
});
