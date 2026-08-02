<?php

use App\Models\Kelas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Cek Absensi Anak (Orang Tua) — Use Case Testing
|--------------------------------------------------------------------------
|
| Tiap pagi sekolah mengirim laporan absensi ke WhatsApp wali kelas, berikut satu
| link yang diteruskan ke orang tua. Orang tua membuka link itu, memasukkan NISN
| anaknya, lalu melihat absensi anaknya hari itu: statusnya, jam absennya, foto
| selfie saat absen, dan sisa poin anaknya.
|
| Link itu hanya membuka satu kelas, yaitu kelas yang laporannya dikirim. NISN
| anak dari kelas lain ditolak, begitu juga NISN yang tidak dikenal.
| Alur pemakaian: orang tua membuka link dari WhatsApp, lalu membaca absensi anaknya
| hari itu - hadir dengan foto selfie-nya, izin, alpha, atau belum absen - beserta
| sisa poin anaknya.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

// TS.CAP.001 / TC.CAP.001.001 — Positive
test('orang tua melihat absensi anaknya lengkap dengan jam dan foto selfienya', function () {
    Carbon::setTestNow('2026-07-06 08:00:00');
    [, $kelas, $siswa] = kelasBerisiSiswa();
    catatKehadiranBerselfie($siswa->nisn, '2026-07-06', '06:45');

    $link = linkAbsensiOrangTua($kelas->id, '2026-07-06');

    $this->followingRedirects()
        ->post($link.'/cek', ['nisn' => $siswa->nisn])
        ->assertSee('Ahmad Fauzi')
        ->assertSee('10 IPA 1')
        ->assertSee('Hadir')
        ->assertSee('06:45')
        ->assertSee('Foto diambil saat absen')
        ->assertSee('Sisa Poin');
});

test('orang tua bisa melihat absensi anaknya menggunakan NIS', function () {
    Carbon::setTestNow('2026-07-06 08:00:00');
    [, $kelas, $siswa] = kelasBerisiSiswa();
    catatKehadiranBerselfie($siswa->nisn, '2026-07-06', '06:45');

    $link = linkAbsensiOrangTua($kelas->id, '2026-07-06');

    $this->followingRedirects()
        ->post($link.'/cek', ['nisn' => $siswa->nis])
        ->assertSee('Ahmad Fauzi')
        ->assertSee('10 IPA 1')
        ->assertSee('Hadir');
});

// TS.CAP.002 / TC.CAP.002.001 — Positive
test('sisa poin anak yang pernah melanggar ikut tampil apa adanya', function () {
    Carbon::setTestNow('2026-07-06 08:00:00');
    [, $kelas, $siswa] = kelasBerisiSiswa();
    catatKehadiranBerselfie($siswa->nisn, '2026-07-06');
    catatPelanggaran($siswa, 'Tidak memakai atribut', 'ringan', '2026-07-02', 15);

    $link = linkAbsensiOrangTua($kelas->id, '2026-07-06');

    $this->followingRedirects()
        ->post($link.'/cek', ['nisn' => $siswa->nisn])
        ->assertSee('Sisa Poin')
        ->assertSee('85');
});

// TS.CAP.003 / TC.CAP.003.001 — Positive
test('anak yang izin ditampilkan tanpa foto, berikut keterangannya', function () {
    Carbon::setTestNow('2026-07-06 08:00:00');
    [, $kelas, $siswa] = kelasBerisiSiswa();
    catatKehadiran($siswa->nisn, '2026-07-06', 'izin');

    $link = linkAbsensiOrangTua($kelas->id, '2026-07-06');

    $this->followingRedirects()
        ->post($link.'/cek', ['nisn' => $siswa->nisn])
        ->assertSee('Izin')
        ->assertSee('Tidak ada foto absensi karena anak Anda izin hari ini.')
        ->assertDontSee('Foto diambil saat absen');
});

// TS.CAP.004 / TC.CAP.004.001 — Positive
test('anak yang belum absen ditampilkan sebagai belum absen', function () {
    Carbon::setTestNow('2026-07-06 08:00:00');
    [, $kelas, $siswa] = kelasBerisiSiswa();

    $link = linkAbsensiOrangTua($kelas->id, '2026-07-06');

    $this->followingRedirects()
        ->post($link.'/cek', ['nisn' => $siswa->nisn])
        ->assertSee('Belum Absen')
        ->assertSee('Anak Anda belum melakukan absensi hari ini.');
});

// TS.CAP.005 / TC.CAP.005.001 — Positive
test('anak yang tidak hadir tanpa keterangan ditampilkan sebagai tidak hadir', function () {
    Carbon::setTestNow('2026-07-06 08:00:00');
    [, $kelas, $siswa] = kelasBerisiSiswa();
    catatKehadiran($siswa->nisn, '2026-07-06', 'alpha');

    $link = linkAbsensiOrangTua($kelas->id, '2026-07-06');

    $this->followingRedirects()
        ->post($link.'/cek', ['nisn' => $siswa->nisn])
        ->assertSee('Tidak Hadir')
        ->assertSee('Anak Anda tidak hadir tanpa keterangan hari ini.');
});
