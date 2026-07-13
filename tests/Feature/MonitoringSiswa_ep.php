<?php

use App\Models\Kelas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Monitoring Siswa (Kesiswaan) — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Pengujian black box: kesiswaan masuk lewat halaman masuk, lalu memantau seluruh
| siswa sekolah. Hasilnya diperiksa dari apa yang muncul di layar, bukan dari
| basis data.
|
| Monitoring menampilkan kehadiran hari ini, persentase kehadiran, dan sisa poin
| tiap siswa. Siswa yang alpha-nya sudah mencapai ambang batas ditandai supaya
| bisa ditindaklanjuti. Hanya siswa yang sudah mendaftarkan akunnya yang tampil.
| Yang diuji di berkas ini adalah pencarian dan penyaringnya: nama, NIS, dan kelas.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

// TS.MOS.005 / TC.MOS.005.001 — Positive
test('kesiswaan mencari siswa berdasarkan nama', function () {
    [, $kelas] = kelasBerisiSiswa();
    siswaLainDiKelas($kelas->id, 'Siti Aminah', '1234567892', '10003');

    kesiswaanMasuk();

    $this->get('/kesiswaan/monitoring?search=Siti')
        ->assertSee('Siti Aminah')
        ->assertDontSee('Ahmad Fauzi');
});

// TS.MOS.006 / TC.MOS.006.001 — Positive
test('kesiswaan mencari siswa berdasarkan NIS', function () {
    [, $kelas] = kelasBerisiSiswa();
    siswaLainDiKelas($kelas->id, 'Siti Aminah', '1234567892', '10003');

    kesiswaanMasuk();

    $this->get('/kesiswaan/monitoring?search=10003')
        ->assertSee('Siti Aminah')
        ->assertDontSee('Ahmad Fauzi');
});

// TS.MOS.007 / TC.MOS.007.001 — Positive
test('kesiswaan menyaring siswa berdasarkan kelas', function () {
    [, $kelas] = kelasBerisiSiswa();

    $kelasLain = Kelas::create(['nama' => '11 IPS 1', 'tingkat' => '11']);
    siswaLainDiKelas($kelasLain->id, 'Siswa Kelas Lain', '1234567891', '10002');

    kesiswaanMasuk();

    $this->get("/kesiswaan/monitoring?kelas_id={$kelas->id}")
        ->assertSee('Ahmad Fauzi')
        ->assertDontSee('Siswa Kelas Lain');
});

// TS.MOS.009 / TC.MOS.009.001 — Positive
test('daftar monitoring yang tidak menemukan siswa menampilkan keterangannya', function () {
    kelasBerisiSiswa();
    kesiswaanMasuk();

    $this->get('/kesiswaan/monitoring?search=Nama Yang Tidak Ada')
        ->assertSee('Tidak ada data siswa ditemukan.');
});
