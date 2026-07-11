<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Persetujuan Poin (Kesiswaan) — Boundary Value Analysis
|--------------------------------------------------------------------------
|
| Dua batas yang diuji:
|
| 1. Besar poin yang diberikan saat menyetujui: 1 sampai 100 poin. Kolomnya di
|    layar sudah menolak angka di luar rentang itu, jadi yang bisa dialami
|    pengguna hanya nilai tepat di batasnya, yaitu 1 dan 100.
| 2. Panjang alasan penolakan: paling panjang 1000 karakter. Layar tidak
|    membatasi panjangnya, jadi kelebihan panjang bisa benar-benar terjadi.
|
*/

// TS.PSP.009 / TC.PSP.009.001 — Positive — tepat pada batas terendah
test('jumlah poin 1 diterima', function () {
    [$wali, , $siswa] = kelasBerisiSiswa();
    $pengajuan = pengajuanPoinMenunggu($siswa, $wali->id);

    kesiswaanMasuk();

    $this->followingRedirects()
        ->put("/kesiswaan/pengajuan-poin/{$pengajuan->id}/approve", ['jumlah_poin' => 1])
        ->assertSee('Pengajuan penambahan poin berhasil diterima.');
});

// TS.PSP.009 / TC.PSP.009.002 — Positive — tepat pada batas tertinggi
test('jumlah poin 100 diterima', function () {
    [$wali, , $siswa] = kelasBerisiSiswa();
    $pengajuan = pengajuanPoinMenunggu($siswa, $wali->id);

    kesiswaanMasuk();

    $this->followingRedirects()
        ->put("/kesiswaan/pengajuan-poin/{$pengajuan->id}/approve", ['jumlah_poin' => 100])
        ->assertSee('Pengajuan penambahan poin berhasil diterima.');
});

// TS.PSP.010 / TC.PSP.010.001 — Positive — tepat pada batas atas panjang alasan
test('alasan penolakan sepanjang 1000 karakter diterima', function () {
    [$wali, , $siswa] = kelasBerisiSiswa();
    $pengajuan = pengajuanPoinMenunggu($siswa, $wali->id);

    kesiswaanMasuk();

    $this->followingRedirects()
        ->put("/kesiswaan/pengajuan-poin/{$pengajuan->id}/reject", [
            'alasan_penolakan' => str_repeat('a', 1000),
        ])
        ->assertSee('Pengajuan penambahan poin berhasil ditolak.');
});

// TS.PSP.010 / TC.PSP.010.002 — Negative — sekarakter di atas batas atas
test('alasan penolakan sepanjang 1001 karakter ditolak', function () {
    [$wali, , $siswa] = kelasBerisiSiswa();
    $pengajuan = pengajuanPoinMenunggu($siswa, $wali->id);

    kesiswaanMasuk();

    $this->from('/kesiswaan/pengajuan-poin')
        ->followingRedirects()
        ->put("/kesiswaan/pengajuan-poin/{$pengajuan->id}/reject", [
            'alasan_penolakan' => str_repeat('a', 1001),
        ])
        ->assertSee('Alasan penolakan maksimal 1000 karakter.');
});
