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
| Yang diuji di berkas ini adalah isian NISN-nya: dikenali, tidak dikenali, milik kelas
| lain, atau dikosongkan; berikut link yang tidak dikenali.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

// TS.CAP.006 / TC.CAP.006.001 — Negative
test('nisn yang tidak dikenal ditolak beserta alasannya', function () {
    Carbon::setTestNow('2026-07-06 08:00:00');
    [, $kelas] = kelasBerisiSiswa();

    $link = linkAbsensiOrangTua($kelas->id, '2026-07-06');

    $this->from($link)
        ->followingRedirects()
        ->post($link.'/cek', ['nisn' => '9999999999'])
        ->assertSee('Data siswa tidak ditemukan. Periksa kembali NISN atau NIS-nya.')
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
        ->assertSee('Data siswa tidak ditemukan. Periksa kembali NISN atau NIS-nya.')
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
