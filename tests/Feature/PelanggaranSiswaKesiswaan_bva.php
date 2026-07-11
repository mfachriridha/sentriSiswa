<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Pelanggaran Siswa (Kesiswaan) — Boundary Value Analysis
|--------------------------------------------------------------------------
|
| Dua batas yang diuji:
|
| 1. Tanggal kejadian paling akhir adalah hari ini. Kemarin dan hari ini
|    diterima, besok ditolak.
| 2. Sisa poin siswa dimulai dari 100 dan tidak boleh turun di bawah 0.
|    Pengurangan yang pas menghabiskan poin membuat sisanya nol, dan
|    pengurangan berikutnya tetap menyisakan nol, bukan angka minus.
|
| Hari ini pada pengujian ini adalah 10 Juli 2026.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

// TS.PLS.012 / TC.PLS.012.001 — Positive — tepat pada batas
test('tanggal kejadian hari ini diterima', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = kelasBerisiSiswa();
    kesiswaanMasuk();
    $jenis = jenisPelanggaranTersedia();

    $this->followingRedirects()
        ->post('/kesiswaan/pelanggaran-siswa', dataPelanggaranSiswa($siswa, $jenis, [
            'tanggal_pelanggaran' => '2026-07-10',
        ]))
        ->assertSee('Pelanggaran siswa berhasil dicatat.');
});

// TS.PLS.012 / TC.PLS.012.002 — Positive — sehari di bawah batas
test('tanggal kejadian kemarin diterima', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = kelasBerisiSiswa();
    kesiswaanMasuk();
    $jenis = jenisPelanggaranTersedia();

    $this->followingRedirects()
        ->post('/kesiswaan/pelanggaran-siswa', dataPelanggaranSiswa($siswa, $jenis, [
            'tanggal_pelanggaran' => '2026-07-09',
        ]))
        ->assertSee('Pelanggaran siswa berhasil dicatat.');
});

// TS.PLS.012 / TC.PLS.012.003 — Negative — sehari di atas batas
test('tanggal kejadian besok ditolak', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = kelasBerisiSiswa();
    kesiswaanMasuk();
    $jenis = jenisPelanggaranTersedia();

    $this->from('/kesiswaan/pelanggaran-siswa/create')
        ->followingRedirects()
        ->post('/kesiswaan/pelanggaran-siswa', dataPelanggaranSiswa($siswa, $jenis, [
            'tanggal_pelanggaran' => '2026-07-11',
        ]))
        ->assertSee('Tanggal pelanggaran tidak boleh melebihi hari ini.');
});

// TS.PLS.013 / TC.PLS.013.001 — Positive — tepat di batas bawah sisa poin
test('sisa poin menjadi nol ketika pengurangannya pas menghabiskan poin', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = kelasBerisiSiswa();

    // Poin awal 100, dipotong 100 sekaligus, sisanya pas nol.
    $pelanggaran = catatPelanggaran($siswa, 'Membawa senjata tajam', 'sangat_berat', '2026-07-06', 100);

    kesiswaanMasuk();

    $this->get("/kesiswaan/pelanggaran-siswa/{$pelanggaran->id}")
        ->assertSee('Sisa Poin: 0');
});

// TS.PLS.013 / TC.PLS.013.002 — Positive — di bawah batas bawah sisa poin
test('sisa poin tetap nol dan tidak minus ketika pengurangannya melebihi poin yang tersisa', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = kelasBerisiSiswa();

    // Poin awal 100, dipotong 100 lalu dipotong 10 lagi. Sisanya berhenti di nol.
    catatPelanggaran($siswa, 'Membawa senjata tajam', 'sangat_berat', '2026-07-06', 100);
    $pelanggaranKedua = catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-07', 10);

    kesiswaanMasuk();

    $this->get("/kesiswaan/pelanggaran-siswa/{$pelanggaranKedua->id}")
        ->assertSee('Sisa Poin: 0')
        ->assertDontSee('Sisa Poin: -10');
});
