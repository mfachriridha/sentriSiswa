<?php

use App\Models\PengajuanPoin;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Monitoring Siswa (Kesiswaan) — State Transition Testing
|--------------------------------------------------------------------------
|
| Pengujian black box: kesiswaan masuk lewat halaman masuk, lalu memantau seluruh
| siswa sekolah. Hasilnya diperiksa dari apa yang muncul di layar, bukan dari
| basis data.
|
| Monitoring menampilkan kehadiran hari ini, persentase kehadiran, dan sisa poin
| tiap siswa. Siswa yang alpha-nya sudah mencapai ambang batas ditandai supaya
| bisa ditindaklanjuti. Hanya siswa yang sudah mendaftarkan akunnya yang tampil.
| Keadaan yang berpindah: poin siswa naik begitu pengajuan poinnya disetujui, dan
| siswa yang belum mendaftarkan akun belum ikut terpantau sama sekali.
|
*/

afterEach(function () {
    Carbon::setTestNow();
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
