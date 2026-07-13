<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Monitoring Siswa (Kesiswaan) — Use Case Testing
|--------------------------------------------------------------------------
|
| Pengujian black box: kesiswaan masuk lewat halaman masuk, lalu memantau seluruh
| siswa sekolah. Hasilnya diperiksa dari apa yang muncul di layar, bukan dari
| basis data.
|
| Monitoring menampilkan kehadiran hari ini, persentase kehadiran, dan sisa poin
| tiap siswa. Siswa yang alpha-nya sudah mencapai ambang batas ditandai supaya
| bisa ditindaklanjuti. Hanya siswa yang sudah mendaftarkan akunnya yang tampil.
| Alur pemakaian tanpa isian: kesiswaan memantau seluruh siswa, membaca sisa poin
| seorang siswa, dan menelusuri riwayat kehadirannya.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

// TS.MOS.001 / TC.MOS.001.001 — Positive
test('kesiswaan memantau seluruh siswa beserta kehadiran hari ini', function () {
    Carbon::setTestNow('2026-07-06 08:00:00'); // Senin, hari absensi.
    [, , $siswa] = kelasBerisiSiswa();
    catatKehadiran($siswa->nisn, '2026-07-06', 'hadir');

    kesiswaanMasuk();

    $this->get('/kesiswaan/monitoring')
        ->assertSee('Monitoring Siswa')
        ->assertSee('Ahmad Fauzi')
        ->assertSee('10 IPA 1')
        ->assertSee('Hadir');
});

// TS.MOS.002 / TC.MOS.002.001 — Positive
test('monitoring menampilkan sisa poin siswa setelah dipotong pelanggaran', function () {
    [, , $siswa] = kelasBerisiSiswa();
    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06', 10);

    kesiswaanMasuk();

    $this->get("/kesiswaan/monitoring/{$siswa->nisn}")
        ->assertSee('Ahmad Fauzi')
        ->assertSee('Terlambat masuk kelas')
        ->assertSee('90');
});

// TS.MOS.008 / TC.MOS.008.001 — Positive
test('kesiswaan melihat riwayat kehadiran seorang siswa', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = kelasBerisiSiswa();
    catatKehadiran($siswa->nisn, '2026-07-06', 'alpha');

    kesiswaanMasuk();

    $this->get("/kesiswaan/monitoring/{$siswa->nisn}")
        ->assertSee('Ahmad Fauzi')
        ->assertSee('Riwayat Kehadiran')
        ->assertSee('Alpha');
});
