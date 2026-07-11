<?php

use App\Models\Kelas;
use App\Models\PengajuanPoin;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Monitoring Poin (BK) — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Pengujian black box: guru BK masuk lewat halaman masuk, lalu menelusuri poin
| dan pelanggaran siswa di tingkatnya. Hasilnya diperiksa dari apa yang muncul di
| layar, bukan dari basis data.
|
| Poin siswa mulai dari 100, berkurang oleh pelanggaran, dan bertambah oleh
| pengajuan poin yang sudah disetujui kesiswaan. Guru BK hanya memantau: ia tidak
| bisa mencatat pelanggaran baru, karena itu wewenang kesiswaan.
|
*/

// TS.MPB.001 / TC.MPB.001.001 — Positive
test('guru bk melihat sisa poin siswa di daftar monitoring', function () {
    [, , $siswa] = kelasBerisiSiswa();
    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06', 10);

    bkMasuk('10');

    $this->get('/bk/monitoring')
        ->assertSee('Ahmad Fauzi')
        ->assertSee('90');
});

// TS.MPB.002 / TC.MPB.002.001 — Positive
test('guru bk menelusuri riwayat pelanggaran seorang siswa', function () {
    [, , $siswa] = kelasBerisiSiswa();
    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06', 10);
    catatPelanggaran($siswa, 'Berkelahi', 'berat', '2026-07-07', 60);

    bkMasuk('10');

    $this->get("/bk/monitoring/{$siswa->nisn}")
        ->assertSee('Terlambat masuk kelas')
        ->assertSee('Berkelahi')
        ->assertSee('30');
});

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

// TS.MPB.005 / TC.MPB.005.001 — Negative
test('guru bk tidak menemukan tombol untuk mencatat pelanggaran', function () {
    [, , $siswa] = kelasBerisiSiswa();

    bkMasuk('10');

    // Mencatat pelanggaran adalah wewenang kesiswaan, bukan BK.
    $this->get("/bk/monitoring/{$siswa->nisn}")
        ->assertSee('Ahmad Fauzi')
        ->assertDontSee('Catat Pelanggaran');
});

// TS.MPB.006 / TC.MPB.006.001 — Negative
test('guru bk tidak melihat poin siswa dari tingkat lain', function () {
    kelasBerisiSiswa();

    $kelasTingkatLain = Kelas::create(['nama' => '11 IPS 1', 'tingkat' => '11']);
    $siswaTingkatLain = siswaLainDiKelas($kelasTingkatLain->id, 'Siswa Tingkat 11', '1234567891', '10002');
    catatPelanggaran($siswaTingkatLain, 'Berkelahi', 'berat', '2026-07-07', 60);

    bkMasuk('10');

    $this->get('/bk/monitoring')
        ->assertDontSee('Siswa Tingkat 11');
});
