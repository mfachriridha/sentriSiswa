<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Persetujuan Poin (Kesiswaan) — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Isian yang diuji di berkas ini adalah penyaring riwayat: status keputusan
| yang dipilih menentukan pengajuan mana yang tampil dan mana yang tidak.
|
| Perpindahan keadaan pengajuannya sendiri (menunggu, disetujui, ditolak, dan
| larangan memutuskannya dua kali) diuji di PersetujuanPoinKesiswaan_stt.php,
| batas besar poin di _bva.php, dan alur melihat daftar serta riwayatnya di _uc.php.
|
| Jumlah poin diisi lewat kolom angka yang wajib diisi dan hanya menerima 1 sampai
| 100, sedangkan alasan penolakan wajib diisi juga. Layar sudah menahannya lebih
| dulu, jadi pengguna tidak pernah bisa mengirim kolom kosong, huruf, atau angka
| di luar rentang itu. Karena tidak pernah dialami pengguna, kasus tersebut tidak
| didokumentasikan.
|
*/

// TS.PSP.007 / TC.PSP.007.001 — Positive
test('kesiswaan menyaring riwayat pengajuan berdasarkan status', function () {
    [$wali, $kelas, $siswa] = kelasBerisiSiswa();
    $siswaLain = siswaLainDiKelas($kelas->id, 'Siti Aminah', '1234567892', '10003');

    $disetujui = pengajuanPoinMenunggu($siswa, $wali->id, 'Juara lomba cerdas cermat.');
    $ditolak = pengajuanPoinMenunggu($siswaLain, $wali->id, 'Aktif membantu kegiatan sekolah.');

    kesiswaanMasuk();
    $this->put("/kesiswaan/pengajuan-poin/{$disetujui->id}/approve", ['jumlah_poin' => 5]);
    $this->put("/kesiswaan/pengajuan-poin/{$ditolak->id}/reject", [
        'alasan_penolakan' => 'Belum ada buktinya.',
    ]);

    $this->get('/kesiswaan/pengajuan-poin/riwayat?status=rejected')
        ->assertSee('Aktif membantu kegiatan sekolah.')
        ->assertDontSee('Juara lomba cerdas cermat.');
});
