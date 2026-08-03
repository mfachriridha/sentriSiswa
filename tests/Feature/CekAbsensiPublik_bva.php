<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Cek Absensi Anak (Orang Tua) — Boundary Value Analysis
|--------------------------------------------------------------------------
|
| Batas yang diuji adalah masa berlaku link. Link absensi dikirim tiap pagi dan
| hanya berlaku sampai akhir hari itu juga. Dibuka semenit sebelum tengah malam
| masih bisa; lewat tengah malam link itu mati dan orang tua harus menunggu link
| baru esok paginya.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

// TS.CAP.010 / TC.CAP.010.001 — Positive — semenit sebelum masa berlakunya habis
test('link masih bisa dibuka semenit sebelum hari itu berakhir', function () {
    Carbon::setTestNow('2026-07-06 08:00:00');
    [, $kelas, $siswa] = kelasBerisiSiswa();
    catatKehadiranBerselfie($siswa->nisn, '2026-07-06');
    $link = linkAbsensiOrangTua($kelas->id, '2026-07-06');

    Carbon::setTestNow('2026-07-06 23:59:00');

    $this->get($link)->assertSee('Cek Presensi Anak');

    $this->followingRedirects()
        ->post($link.'/cek', ['nisn' => $siswa->nisn])
        ->assertSee('Ahmad Fauzi')
        ->assertSee('Hadir');
});

// TS.CAP.010 / TC.CAP.010.002 — Negative — semenit setelah masa berlakunya habis
test('link tidak bisa dibuka lagi setelah harinya berganti', function () {
    Carbon::setTestNow('2026-07-06 08:00:00');
    [, $kelas, $siswa] = kelasBerisiSiswa();
    catatKehadiranBerselfie($siswa->nisn, '2026-07-06');
    $link = linkAbsensiOrangTua($kelas->id, '2026-07-06');

    Carbon::setTestNow('2026-07-07 00:01:00');

    $this->get($link)
        ->assertSee('Link Kedaluwarsa')
        ->assertDontSee('Cek Presensi Anak');

    $this->followingRedirects()
        ->post($link.'/cek', ['nisn' => $siswa->nisn])
        ->assertSee('Link Kedaluwarsa')
        ->assertDontSee('Sisa Poin');
});
