<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Persetujuan Poin (Kesiswaan) — Use Case Testing
|--------------------------------------------------------------------------
|
| Alur pemakaiannya, tanpa isian apa pun: kesiswaan membuka daftar pengajuan yang
| menunggu keputusan, dan menelusuri riwayat keputusan yang sudah dibuat.
|
| Termasuk alur alternatifnya: belum ada pengajuan yang masuk sama sekali.
|
| Ditambah alamat yang dipanggil halaman diam-diam untuk menyegarkan angka merah
| di menu - jumlah pengajuan yang menunggu. Kalau ia rusak, angkanya salah tanpa
| ada pesan galat apa pun di layar.
|
*/

// TS.PSP.001 / TC.PSP.001.001 — Positive
test('kesiswaan melihat pengajuan poin yang menunggu keputusan', function () {
    [$wali, , $siswa] = kelasBerisiSiswa();
    pengajuanPoinMenunggu($siswa, $wali->id);

    kesiswaanMasuk();

    $this->get('/kesiswaan/pengajuan-poin')
        ->assertSee('Ahmad Fauzi')
        ->assertSee('10 IPA 1')
        ->assertSee('Juara lomba cerdas cermat.')
        ->assertSee('Raka Pradipta');
});

// TS.PSP.006 / TC.PSP.006.001 — Positive
test('kesiswaan melihat riwayat keputusan pengajuan poin', function () {
    [$wali, , $siswa] = kelasBerisiSiswa();
    $pengajuan = pengajuanPoinMenunggu($siswa, $wali->id);

    kesiswaanMasuk();
    $this->put("/kesiswaan/pengajuan-poin/{$pengajuan->id}/approve", ['jumlah_poin' => 5]);

    $this->get('/kesiswaan/pengajuan-poin/riwayat')
        ->assertSee('Ahmad Fauzi')
        ->assertSee('Juara lomba cerdas cermat.')
        ->assertSee('Disetujui');
});

// TS.PSP.008 / TC.PSP.008.001 — Positive — alur alternatif: belum ada pengajuan
test('daftar persetujuan yang masih kosong menampilkan keterangannya', function () {
    kesiswaanMasuk();

    $this->get('/kesiswaan/pengajuan-poin')
        ->assertSee('Tidak ada pengajuan poin yang menunggu persetujuan.');
});

// TS.PSP.013 / TC.PSP.013.001 — Positive — penyegar angka di menu
test('jumlah pengajuan yang menunggu ikut turun setelah salah satunya diputuskan', function () {
    [$wali, $kelas, $siswa] = kelasBerisiSiswa();
    $siswaLain = siswaLainDiKelas($kelas->id, 'Siti Aminah', '1234567892', '10003');

    $pengajuan = pengajuanPoinMenunggu($siswa, $wali->id);
    pengajuanPoinMenunggu($siswaLain, $wali->id, 'Aktif membantu kegiatan sekolah.');

    kesiswaanMasuk();

    // Dua pengajuan menunggu; angka merah di menu harus menunjukkan dua.
    $this->get('/kesiswaan/pengajuan-poin/pending-count')
        ->assertSee('2');

    $this->put("/kesiswaan/pengajuan-poin/{$pengajuan->id}/approve", ['jumlah_poin' => 5]);

    $this->get('/kesiswaan/pengajuan-poin/pending-count')
        ->assertSee('1');
});
