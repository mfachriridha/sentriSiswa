<?php

use App\Models\PengajuanPoin;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Monitoring Poin (BK) — State Transition Testing
|--------------------------------------------------------------------------
|
| Pengujian black box: guru BK masuk lewat halaman masuk, lalu menelusuri poin
| dan pelanggaran siswa di tingkatnya. Hasilnya diperiksa dari apa yang muncul di
| layar, bukan dari basis data.
|
| Poin siswa mulai dari 100, berkurang oleh pelanggaran, dan bertambah oleh
| pengajuan poin yang sudah disetujui kesiswaan. Guru BK hanya memantau: ia tidak
| bisa mencatat pelanggaran baru, karena itu wewenang kesiswaan.
| Keadaan yang berpindah: poin siswa hanya naik setelah pengajuannya benar-benar
| disetujui. Pengajuan yang masih menunggu belum mengubah apa pun.
|
*/

// TS.MPB.003 / TC.MPB.003.001 — Positive
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

    bkMasuk('10');

    // Poin awal 100, dipotong 20 karena pelanggaran, ditambah 5 dari pengajuan.
    $this->get("/bk/monitoring/{$siswa->nisn}")
        ->assertSee('Juara lomba cerdas cermat.')
        ->assertSee('85');
});

// TS.MPB.004 / TC.MPB.004.001 — Negative
test('pengajuan poin yang belum disetujui belum menambah poin siswa', function () {
    [$wali, , $siswa] = kelasBerisiSiswa();
    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06', 20);

    PengajuanPoin::create([
        'profil_siswa_id' => $siswa->nisn,
        'diajukan_oleh_id' => $wali->id,
        'alasan' => 'Juara lomba cerdas cermat.',
        'status' => 'pending',
    ]);

    bkMasuk('10');

    // Pengajuan masih menunggu keputusan, jadi poinnya tetap 100 dikurangi 20.
    $this->get("/bk/monitoring/{$siswa->nisn}")
        ->assertSee('80')
        ->assertDontSee('Juara lomba cerdas cermat.');
});
