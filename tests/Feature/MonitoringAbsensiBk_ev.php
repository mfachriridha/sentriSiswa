<?php

use App\Models\Kelas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Monitoring Absensi (BK) — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Pengujian black box: guru BK masuk lewat halaman masuk, lalu memantau kehadiran
| siswa. Hasilnya diperiksa dari apa yang muncul di layar, bukan dari basis data.
|
| Tiap guru BK hanya memegang satu tingkat, dan hanya boleh memantau siswa di
| tingkat itu. Siswa dari tingkat lain tidak tampil dan rinciannya pun tidak bisa
| dibuka.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

// TS.MAB.001 / TC.MAB.001.001 — Positive
test('guru bk memantau kehadiran hari ini siswa di tingkatnya', function () {
    Carbon::setTestNow('2026-07-06 08:00:00'); // Senin, hari absensi.
    [, , $siswa] = kelasBerisiSiswa();
    catatKehadiran($siswa->nisn, '2026-07-06', 'hadir');

    bkMasuk('10');

    $this->get('/bk/monitoring')
        ->assertSee('Monitoring BK')
        ->assertSee('Ahmad Fauzi')
        ->assertSee('10 IPA 1')
        ->assertSee('Hadir');
});

// TS.MAB.002 / TC.MAB.002.001 — Negative
test('guru bk tidak memantau siswa dari tingkat lain', function () {
    kelasBerisiSiswa();

    $kelasTingkatLain = Kelas::create(['nama' => '11 IPS 1', 'tingkat' => '11']);
    siswaLainDiKelas($kelasTingkatLain->id, 'Siswa Tingkat 11', '1234567891', '10002');

    bkMasuk('10');

    $this->get('/bk/monitoring')
        ->assertSee('Ahmad Fauzi')
        ->assertDontSee('Siswa Tingkat 11');
});

// TS.MAB.003 / TC.MAB.003.001 — Negative
test('guru bk tidak bisa membuka rincian siswa dari tingkat lain', function () {
    kelasBerisiSiswa();

    $kelasTingkatLain = Kelas::create(['nama' => '11 IPS 1', 'tingkat' => '11']);
    $siswaTingkatLain = siswaLainDiKelas($kelasTingkatLain->id, 'Siswa Tingkat 11', '1234567891', '10002');

    bkMasuk('10');

    $this->get("/bk/monitoring/{$siswaTingkatLain->nisn}")
        ->assertForbidden();
});

// TS.MAB.004 / TC.MAB.004.001 — Positive
test('guru bk melihat riwayat kehadiran seorang siswa di tingkatnya', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = kelasBerisiSiswa();
    catatKehadiran($siswa->nisn, '2026-07-06', 'alpha');

    bkMasuk('10');

    $this->get("/bk/monitoring/{$siswa->nisn}")
        ->assertSee('Ahmad Fauzi')
        ->assertSee('Riwayat Kehadiran')
        ->assertSee('Alpha');
});

// TS.MAB.005 / TC.MAB.005.001 — Positive
test('guru bk mencari siswa berdasarkan nama', function () {
    [, $kelas] = kelasBerisiSiswa();
    siswaLainDiKelas($kelas->id, 'Siti Aminah', '1234567892', '10003');

    bkMasuk('10');

    $this->get('/bk/monitoring?search=Siti')
        ->assertSee('Siti Aminah')
        ->assertDontSee('Ahmad Fauzi');
});

// TS.MAB.006 / TC.MAB.006.001 — Positive
test('guru bk menyaring siswa berdasarkan kelas di tingkatnya', function () {
    [, $kelas] = kelasBerisiSiswa();

    $kelasLain = Kelas::create(['nama' => '10 IPA 2', 'tingkat' => '10']);
    siswaLainDiKelas($kelasLain->id, 'Siswa Kelas Lain', '1234567891', '10002');

    bkMasuk('10');

    $this->get("/bk/monitoring?kelas_id={$kelas->id}")
        ->assertSee('Ahmad Fauzi')
        ->assertDontSee('Siswa Kelas Lain');
});

// TS.MAB.007 / TC.MAB.007.001 — Positive
test('pencarian yang tidak menemukan siswa menampilkan keterangannya', function () {
    kelasBerisiSiswa();
    bkMasuk('10');

    $this->get('/bk/monitoring?search=Nama Yang Tidak Ada')
        ->assertSee('Tidak ada data siswa ditemukan.');
});
