<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Poin Saya (Siswa) — Use Case Testing
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
| Alur pemakaian tanpa isian: siswa membaca sisa poinnya sendiri dan seluruh riwayat
| pelanggarannya. Termasuk alur pengecualiannya: pelanggaran siswa lain tidak boleh
| ikut terbaca.
|
*/

// TS.POS.001 / TC.POS.001.001 — Positive
test('siswa yang belum pernah melanggar melihat poinnya masih penuh', function () {
    siswaMasuk();

    $this->get('/siswa/poin')
        ->assertSee('Poin Saya')
        ->assertSee('100')
        ->assertSee('Baik')
        ->assertSee('Belum ada catatan pelanggaran untuk Anda.');
});

// TS.POS.004 / TC.POS.004.001 — Positive
test('siswa melihat seluruh riwayat pelanggarannya', function () {
    $siswa = siswaMasuk();

    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06', 10);
    catatPelanggaran($siswa, 'Berkelahi', 'berat', '2026-07-07', 60);

    $this->get('/siswa/poin')
        ->assertSee('Terlambat masuk kelas')
        ->assertSee('Berkelahi')
        ->assertSee('30');
});

// TS.POS.005 / TC.POS.005.001 — Negative
test('siswa tidak melihat pelanggaran siswa lain', function () {
    $siswa = siswaMasuk();

    $siswaLain = siswaLainDiKelas($siswa->kelas_id, 'Siti Aminah', '1234567892', '10003');
    catatPelanggaran($siswaLain, 'Membolos', 'sedang', '2026-07-06', 30);

    $this->get('/siswa/poin')
        ->assertDontSee('Membolos')
        ->assertSee('Belum ada catatan pelanggaran untuk Anda.');
});

test('perhitungan poin siswa berkurang dengan benar setelah penambahan poin dan pelanggaran baru', function () {
    $siswa = siswaMasuk();

    // Pelanggaran -25 pada tanggal 2026-07-01 -> sisa poin 75
    catatPelanggaran($siswa, 'Merokok', 'sedang', '2026-07-01', 25);
    expect($siswa->fresh()->poin)->toBe(75);

    // Penambahan +40 pada tanggal 2026-07-02 -> sisa poin dicap di 100
    pengajuanPoinDisetujui($siswa->nisn, $siswa->kelas->wali_kelas_id, 'Prestasi Lomba', 40, '2026-07-02 10:00:00');
    expect($siswa->fresh()->poin)->toBe(100);

    // Pelanggaran baru -5 pada tanggal 2026-07-03 -> sisa poin berkurang menjadi 95
    catatPelanggaran($siswa, 'Terlambat', 'ringan', '2026-07-03', 5);
    expect($siswa->fresh()->poin)->toBe(95);

    $this->get('/siswa/poin')
        ->assertSee('95');
});
