<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Persetujuan Poin (Kesiswaan) — State Transition Testing
|--------------------------------------------------------------------------
|
| Keadaan sebuah pengajuan poin:
|
|   menunggu  --(disetujui)--> disetujui   (poin siswa bertambah saat itu juga)
|   menunggu  --(ditolak)----> ditolak
|   disetujui --(diputus lagi)--> DITOLAK
|   ditolak   --(diputus lagi)--> DITOLAK
|
| Satu pengajuan hanya boleh diputuskan sekali. Yang menentukan boleh atau tidaknya
| bukan nilai poin yang diketik, melainkan keadaan pengajuan itu saat tombolnya
| ditekan - dan pengajuan yang sudah diputus pindah dari daftar persetujuan ke
| riwayat, jadi tombolnya pun hilang dari layar.
|
| Kalau penjagaan ini jebol, satu prestasi bisa dihitung berkali-kali dan poin
| seorang siswa bisa dinaikkan tanpa batas.
|
*/

// TS.PSP.002 / TC.PSP.002.001 — Positive — menunggu → disetujui
test('kesiswaan menyetujui pengajuan poin sambil menentukan besar poinnya', function () {
    [$wali, , $siswa] = kelasBerisiSiswa();
    $pengajuan = pengajuanPoinMenunggu($siswa, $wali->id);

    kesiswaanMasuk();

    $this->followingRedirects()
        ->put("/kesiswaan/pengajuan-poin/{$pengajuan->id}/approve", ['jumlah_poin' => 5])
        ->assertSee('Pengajuan penambahan poin berhasil diterima.')
        ->assertSee('Tidak ada pengajuan poin yang menunggu persetujuan.');
});

// TS.PSP.003 / TC.PSP.003.001 — Positive — poin siswa ikut berpindah keadaan
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

// TS.PSP.004 / TC.PSP.004.001 — Positive — menunggu → ditolak
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

// TS.PSP.005 / TC.PSP.005.001 — Negative — disetujui → disetujui lagi
test('pengajuan yang sudah disetujui tidak bisa disetujui lagi', function () {
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

// TS.PSP.011 / TC.PSP.011.001 — Negative — ditolak → disetujui
test('pengajuan yang sudah ditolak tidak bisa berbalik jadi disetujui', function () {
    [$wali, , $siswa] = kelasBerisiSiswa();
    // Poinnya dibuat 80 lebih dulu. Kalau poinnya masih 100, penambahan apa pun
    // tetap terbaca 100 karena dijepit di angka itu - dan pengujiannya jadi
    // tidak membuktikan apa-apa.
    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06', 20);
    $pengajuan = pengajuanPoinMenunggu($siswa, $wali->id);

    kesiswaanMasuk();
    $this->put("/kesiswaan/pengajuan-poin/{$pengajuan->id}/reject", [
        'alasan_penolakan' => 'Belum ada buktinya.',
    ]);

    $this->put("/kesiswaan/pengajuan-poin/{$pengajuan->id}/approve", ['jumlah_poin' => 10])
        ->assertForbidden();

    // Poin siswa tetap 80: penolakan itu tidak bisa dibatalkan diam-diam.
    $this->get("/kesiswaan/monitoring/{$siswa->nisn}")
        ->assertSee('80');
});

// TS.PSP.012 / TC.PSP.012.001 — Negative — disetujui → ditolak
test('pengajuan yang sudah disetujui tidak bisa berbalik jadi ditolak', function () {
    [$wali, , $siswa] = kelasBerisiSiswa();
    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06', 20);
    $pengajuan = pengajuanPoinMenunggu($siswa, $wali->id);

    kesiswaanMasuk();
    $this->put("/kesiswaan/pengajuan-poin/{$pengajuan->id}/approve", ['jumlah_poin' => 5]);

    $this->put("/kesiswaan/pengajuan-poin/{$pengajuan->id}/reject", [
        'alasan_penolakan' => 'Berubah pikiran.',
    ])->assertForbidden();

    // Poinnya tetap 85 seperti keputusan pertama: 100 - 20 + 5.
    $this->get("/kesiswaan/monitoring/{$siswa->nisn}")
        ->assertSee('85');
});
