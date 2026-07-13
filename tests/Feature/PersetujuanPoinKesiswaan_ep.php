<?php

use App\Models\PengajuanPoin;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Persetujuan Poin (Kesiswaan) — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Pengujian black box: kesiswaan masuk lewat halaman masuk, lalu memutuskan
| pengajuan penambahan poin yang dikirim wali kelas. Hasilnya diperiksa dari apa
| yang muncul di layar, bukan dari basis data.
|
| Wali kelas hanya mengirim alasannya; besar poinnya ditentukan kesiswaan saat
| menyetujui, antara 1 sampai 100 poin. Menolak pengajuan wajib disertai alasan
| penolakan supaya wali kelas tahu sebabnya. Satu pengajuan hanya bisa diputuskan
| sekali; setelah diputuskan ia pindah dari daftar persetujuan ke riwayat.
|
*/

/** Sebuah pengajuan poin dari wali kelas yang masih menunggu keputusan. */
function pengajuanPoinMenunggu(ProfilSiswa $siswa, int $waliId, string $alasan = 'Juara lomba cerdas cermat.'): PengajuanPoin
{
    return PengajuanPoin::create([
        'profil_siswa_id' => $siswa->nisn,
        'diajukan_oleh_id' => $waliId,
        'alasan' => $alasan,
        'status' => 'pending',
    ]);
}

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

// TS.PSP.002 / TC.PSP.002.001 — Positive
test('kesiswaan menyetujui pengajuan poin sambil menentukan besar poinnya', function () {
    [$wali, , $siswa] = kelasBerisiSiswa();
    $pengajuan = pengajuanPoinMenunggu($siswa, $wali->id);

    kesiswaanMasuk();

    $this->followingRedirects()
        ->put("/kesiswaan/pengajuan-poin/{$pengajuan->id}/approve", ['jumlah_poin' => 5])
        ->assertSee('Pengajuan penambahan poin berhasil diterima.')
        ->assertSee('Tidak ada pengajuan poin yang menunggu persetujuan.');
});

// TS.PSP.003 / TC.PSP.003.001 — Positive
test('poin siswa bertambah setelah pengajuannya disetujui', function () {
    [$wali, , $siswa] = kelasBerisiSiswa();
    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06', 20);
    $pengajuan = pengajuanPoinMenunggu($siswa, $wali->id);

    kesiswaanMasuk();
    $this->put("/kesiswaan/pengajuan-poin/{$pengajuan->id}/approve", ['jumlah_poin' => 5]);

    // Poin awal 100, dipotong 20 karena pelanggaran, ditambah 5 dari pengajuan.
    $this->get("/kesiswaan/monitoring/{$siswa->nisn}")
        ->assertSee('85');
});

// TS.PSP.004 / TC.PSP.004.001 — Positive
test('kesiswaan menolak pengajuan poin dengan menuliskan alasannya', function () {
    [$wali, , $siswa] = kelasBerisiSiswa();
    $pengajuan = pengajuanPoinMenunggu($siswa, $wali->id);

    kesiswaanMasuk();

    $this->followingRedirects()
        ->put("/kesiswaan/pengajuan-poin/{$pengajuan->id}/reject", [
            'alasan_penolakan' => 'Prestasinya belum bisa dibuktikan dengan sertifikat.',
        ])
        ->assertSee('Pengajuan penambahan poin berhasil ditolak.')
        ->assertSee('Tidak ada pengajuan poin yang menunggu persetujuan.');
});

/*
| Jumlah poin diisi lewat kolom angka yang wajib diisi dan hanya menerima 1 sampai
| 100, sedangkan alasan penolakan wajib diisi juga. Layar sudah menahannya lebih
| dulu, jadi pengguna tidak pernah bisa mengirim kolom kosong, huruf, atau angka
| di luar rentang itu. Karena tidak pernah dialami pengguna, kasus tersebut tidak
| didokumentasikan.
*/

// TS.PSP.005 / TC.PSP.005.001 — Negative
test('pengajuan yang sudah diputuskan tidak bisa diputuskan lagi', function () {
    [$wali, , $siswa] = kelasBerisiSiswa();
    $pengajuan = pengajuanPoinMenunggu($siswa, $wali->id);

    kesiswaanMasuk();
    $this->put("/kesiswaan/pengajuan-poin/{$pengajuan->id}/approve", ['jumlah_poin' => 5]);

    // Pengajuan itu sudah pindah ke riwayat, jadi tidak ada lagi tombol untuk memutuskannya.
    $this->get('/kesiswaan/pengajuan-poin')
        ->assertSee('Tidak ada pengajuan poin yang menunggu persetujuan.');

    $this->put("/kesiswaan/pengajuan-poin/{$pengajuan->id}/approve", ['jumlah_poin' => 10])
        ->assertForbidden();
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

// TS.PSP.008 / TC.PSP.008.001 — Positive
test('daftar persetujuan yang masih kosong menampilkan keterangannya', function () {
    kesiswaanMasuk();

    $this->get('/kesiswaan/pengajuan-poin')
        ->assertSee('Tidak ada pengajuan poin yang menunggu persetujuan.');
});
