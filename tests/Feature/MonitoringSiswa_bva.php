<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Monitoring Siswa (Kesiswaan) — Boundary Value Analysis
|--------------------------------------------------------------------------
|
| Batas yang diuji adalah ambang peringatan alpha. Siswa baru ditandai perlu
| ditindaklanjuti setelah alpha-nya mencapai 3 kali pada semester berjalan.
| Alpha ke-2 belum memunculkan peringatan, alpha ke-3 memunculkannya.
|
| Semester berjalan pada pengujian ini adalah Juli sampai Desember 2026.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

// TS.MOS.010 / TC.MOS.010.001 — Negative — sekali di bawah ambang
test('siswa dengan alpha dua kali belum ditandai perlu ditindaklanjuti', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = kelasBerisiSiswa();

    catatKehadiran($siswa->nisn, '2026-07-06', 'alpha');
    catatKehadiran($siswa->nisn, '2026-07-07', 'alpha');

    kesiswaanMasuk();

    $this->get('/kesiswaan/monitoring')
        ->assertSee('Ahmad Fauzi')
        ->assertDontSee('Peringatan alpha');
});

// TS.MOS.010 / TC.MOS.010.002 — Positive — tepat pada ambang
test('siswa dengan alpha tiga kali ditandai perlu ditindaklanjuti', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = kelasBerisiSiswa();

    catatKehadiran($siswa->nisn, '2026-07-06', 'alpha');
    catatKehadiran($siswa->nisn, '2026-07-07', 'alpha');
    catatKehadiran($siswa->nisn, '2026-07-08', 'alpha');

    kesiswaanMasuk();

    $this->get('/kesiswaan/monitoring')
        ->assertSee('Ahmad Fauzi')
        ->assertSee('Peringatan alpha 3x');
});

// TS.MOS.011 / TC.MOS.011.001 — Negative — alpha di semester sebelumnya
test('alpha dari semester sebelumnya tidak ikut dihitung ke ambang peringatan', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = kelasBerisiSiswa();

    // Tiga alpha, tetapi seluruhnya jatuh di semester Januari sampai Juni.
    catatKehadiran($siswa->nisn, '2026-06-01', 'alpha');
    catatKehadiran($siswa->nisn, '2026-06-02', 'alpha');
    catatKehadiran($siswa->nisn, '2026-06-03', 'alpha');

    kesiswaanMasuk();

    $this->get('/kesiswaan/monitoring')
        ->assertSee('Ahmad Fauzi')
        ->assertDontSee('Peringatan alpha');
});
