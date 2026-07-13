<?php

use App\Models\PengajuanPoin;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Poin Saya (Siswa) — State Transition Testing
|--------------------------------------------------------------------------
|
| Pengujian black box: siswa masuk lewat halaman masuk, lalu melihat poinnya
| sendiri. Hasilnya diperiksa dari apa yang muncul di layar, bukan dari basis
| data.
|
| Poin siswa mulai dari 100, berkurang oleh pelanggaran yang dicatat kesiswaan,
| dan bertambah oleh pengajuan poin yang sudah disetujui. Poin di atas 75 diberi
| keterangan Baik, di atas 50 Cukup, sisanya Perhatian. Siswa hanya melihat
| poinnya sendiri, tidak poin siswa lain.
| Keadaan yang berpindah: poin siswa turun begitu pelanggarannya dicatat, dan naik
| begitu pengajuan poinnya disetujui.
|
*/

// TS.POS.002 / TC.POS.002.001 — Positive
test('poin siswa berkurang setelah pelanggarannya dicatat', function () {
    $siswa = siswaMasuk();

    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06', 10);

    $this->get('/siswa/poin')
        ->assertSee('Terlambat masuk kelas')
        ->assertSee('90');
});

// TS.POS.003 / TC.POS.003.001 — Positive
test('poin siswa bertambah setelah pengajuan poinnya disetujui', function () {
    $siswa = siswaMasuk();
    $wali = $siswa->kelas->waliKelas;

    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06', 20);

    PengajuanPoin::create([
        'profil_siswa_id' => $siswa->nisn,
        'diajukan_oleh_id' => $wali->id,
        'alasan' => 'Juara lomba cerdas cermat.',
        'status' => 'approved',
        'jumlah_poin' => 5,
    ]);

    // Poin awal 100, dipotong 20 karena pelanggaran, ditambah 5 dari pengajuan.
    $this->get('/siswa/poin')
        ->assertSee('85');
});
