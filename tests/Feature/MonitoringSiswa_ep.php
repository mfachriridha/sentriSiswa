<?php

use App\Models\Kelas;
use App\Models\PengajuanPoin;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
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

// TS.MOS.003 / TC.MOS.003.001 — Positive
test('poin siswa bertambah setelah pengajuan poin disetujui', function () {
    [$wali, , $siswa] = kelasBerisiSiswa();
    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06', 20);

    PengajuanPoin::create([
        'profil_siswa_id' => $siswa->nisn,
        'diajukan_oleh_id' => $wali->id,
        'alasan' => 'Juara lomba cerdas cermat.',
        'status' => 'approved',
        'jumlah_poin' => 5,
    ]);

    kesiswaanMasuk();

    // Poin awal 100, dipotong 20 karena pelanggaran, ditambah 5 dari pengajuan.
    $this->get("/kesiswaan/monitoring/{$siswa->nisn}")
        ->assertSee('Juara lomba cerdas cermat.')
        ->assertSee('85');
});

// TS.MOS.004 / TC.MOS.004.001 — Negative
test('siswa yang belum mendaftarkan akun tidak ikut dipantau', function () {
    [, $kelas] = kelasBerisiSiswa();

    $penggunaBelumDaftar = Pengguna::factory()->student()->create([
        'nama' => 'Siswa Belum Daftar',
        'status' => 'unregistered',
    ]);
    ProfilSiswa::factory()->create([
        'pengguna_id' => $penggunaBelumDaftar->id,
        'nisn' => '1234567895',
        'nis' => '10009',
        'kelas_id' => $kelas->id,
    ]);

    kesiswaanMasuk();

    $this->get('/kesiswaan/monitoring')
        ->assertSee('Ahmad Fauzi')
        ->assertDontSee('Siswa Belum Daftar');
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

// TS.MOS.009 / TC.MOS.009.001 — Positive
test('daftar monitoring yang tidak menemukan siswa menampilkan keterangannya', function () {
    kelasBerisiSiswa();
    kesiswaanMasuk();

    $this->get('/kesiswaan/monitoring?search=Nama Yang Tidak Ada')
        ->assertSee('Tidak ada data siswa ditemukan.');
});
