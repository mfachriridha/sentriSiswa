<?php

use App\Models\Kelas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Cek Absensi Anak (Orang Tua) — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Tiap pagi sekolah mengirim laporan absensi ke WhatsApp wali kelas, berikut satu
| link yang diteruskan ke orang tua. Orang tua membuka link itu, memasukkan NISN
| anaknya, lalu melihat absensi anaknya hari itu: statusnya, jam absennya, foto
| selfie saat absen, dan sisa poin anaknya.
|
| Link itu hanya membuka satu kelas, yaitu kelas yang laporannya dikirim. NISN
| anak dari kelas lain ditolak, begitu juga NISN yang tidak dikenal.
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

// TS.CAP.006 / TC.CAP.006.001 — Negative
test('nisn yang tidak dikenal ditolak beserta alasannya', function () {
    Carbon::setTestNow('2026-07-06 08:00:00');
    [, $kelas] = kelasBerisiSiswa();

    $link = linkAbsensiOrangTua($kelas->id, '2026-07-06');

    $this->from($link)
        ->followingRedirects()
        ->post($link.'/cek', ['nisn' => '9999999999'])
        ->assertSee('Data siswa tidak ditemukan. Periksa kembali NISN-nya.')
        ->assertDontSee('Sisa Poin');
});

// TS.CAP.007 / TC.CAP.007.001 — Negative
test('nisn anak dari kelas lain tidak bisa dilihat lewat link kelas ini', function () {
    Carbon::setTestNow('2026-07-06 08:00:00');
    [, $kelas] = kelasBerisiSiswa();

    $kelasLain = Kelas::create(['nama' => '10 IPA 2', 'tingkat' => '10']);
    $siswaLain = siswaLainDiKelas($kelasLain->id, 'Budi Santoso', '1234567891', '10002');
    catatKehadiranBerselfie($siswaLain->nisn, '2026-07-06');

    $link = linkAbsensiOrangTua($kelas->id, '2026-07-06');

    $this->from($link)
        ->followingRedirects()
        ->post($link.'/cek', ['nisn' => $siswaLain->nisn])
        ->assertSee('Data siswa tidak ditemukan. Periksa kembali NISN-nya.')
        ->assertDontSee('Budi Santoso');
});

// TS.CAP.008 / TC.CAP.008.001 — Negative
test('nisn yang dikosongkan ditolak', function () {
    Carbon::setTestNow('2026-07-06 08:00:00');
    [, $kelas] = kelasBerisiSiswa();

    $link = linkAbsensiOrangTua($kelas->id, '2026-07-06');

    $this->from($link)
        ->followingRedirects()
        ->post($link.'/cek', ['nisn' => ''])
        ->assertSee('Cek Absensi Anak')
        ->assertDontSee('Sisa Poin');
});

// TS.CAP.009 / TC.CAP.009.001 — Negative
test('link yang tidak dikenal tidak membuka absensi siapa pun', function () {
    Carbon::setTestNow('2026-07-06 08:00:00');
    kelasBerisiSiswa();

    $this->get('/absensi/publik/link-yang-tidak-pernah-dikirim')
        ->assertSee('Link Kedaluwarsa')
        ->assertDontSee('Cek Absensi Anak');
});
