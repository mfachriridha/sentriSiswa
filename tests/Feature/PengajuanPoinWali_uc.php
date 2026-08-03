<?php

use App\Models\Kelas;
use App\Models\PengajuanPoin;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Pengajuan Poin (Wali Kelas) — Use Case Testing
|--------------------------------------------------------------------------
|
| Pengujian black box: wali kelas masuk lewat halaman masuk, lalu mengajukan
| penambahan poin untuk siswa di kelasnya. Hasilnya diperiksa dari apa yang
| muncul di layar, bukan dari basis data.
|
| Wali kelas hanya memilih siswa dan menuliskan alasannya — jumlah poinnya
| ditentukan kesiswaan saat menyetujui. Pengajuan yang baru dikirim berstatus
| menunggu, dan wali kelas bisa memantaunya di daftar pengajuan.
| Alur pemakaian tanpa isian: wali kelas membuka daftar pengajuannya sendiri, dan
| pilihan siswanya hanya berisi kelasnya sendiri. Termasuk pengecualiannya: pengajuan
| wali kelas lain tidak terbaca, dan guru yang belum dipasangi kelas.
|
*/

// TS.PPW.002 / TC.PPW.002.001 — Positive
test('halaman buat pengajuan hanya menawarkan siswa dari kelas wali kelas itu', function () {
    [, , $siswa] = waliKelasDenganKelas();
    catatPelanggaran($siswa, 'Terlambat Masuk', 'ringan', '2026-07-05', 10);

    $kelasLain = Kelas::create(['nama' => '11 IPS 1', 'tingkat' => '11']);
    $penggunaLain = Pengguna::factory()->student()->create([
        'nama' => 'Siswa Kelas Lain',
        'status' => 'registered',
    ]);
    $siswaLain = ProfilSiswa::factory()->create([
        'pengguna_id' => $penggunaLain->id,
        'nisn' => '1234567891',
        'nis' => '10002',
        'kelas_id' => $kelasLain->id,
    ]);
    catatPelanggaran($siswaLain, 'Terlambat Masuk', 'ringan', '2026-07-05', 10);

    $this->get('/wali-kelas/pengajuan-poin/buat')
        ->assertSee('Ahmad Fauzi')
        ->assertDontSee('Siswa Kelas Lain');
});

// TS.PPW.006 / TC.PPW.006.001 — Positive
test('wali kelas melihat daftar pengajuan yang pernah dikirimnya', function () {
    [$wali, , $siswa] = waliKelasDenganKelas();

    PengajuanPoin::create([
        'profil_siswa_id' => $siswa->nisn,
        'diajukan_oleh_id' => $wali->id,
        'alasan' => 'Juara lomba cerdas cermat.',
        'status' => 'pending',
    ]);

    $this->get('/wali-kelas/pengajuan-poin')
        ->assertSee('Ahmad Fauzi')
        ->assertSee('Juara lomba cerdas cermat.')
        ->assertSee('Menunggu');
});

// TS.PPW.008 / TC.PPW.008.001 — Negative
test('wali kelas tidak melihat pengajuan yang dikirim wali kelas lain', function () {
    [, $kelas, $siswa] = waliKelasDenganKelas();

    $waliLain = Pengguna::factory()->homeroom()->create([
        'nama' => 'Wali Kelas Lain',
        'email' => 'wali.lain@sentrisiswa.test',
        'status' => 'registered',
    ]);

    PengajuanPoin::create([
        'profil_siswa_id' => $siswa->nisn,
        'diajukan_oleh_id' => $waliLain->id,
        'alasan' => 'Pengajuan milik wali kelas lain.',
        'status' => 'pending',
    ]);

    $this->get('/wali-kelas/pengajuan-poin')
        ->assertDontSee('Pengajuan milik wali kelas lain.')
        ->assertSee('Belum ada pengajuan.');
});

// TS.PPW.009 / TC.PPW.009.001 — Negative
test('guru yang belum dipasangi kelas tidak menemukan siswa untuk diajukan', function () {
    $wali = Pengguna::factory()->homeroom()->create([
        'email' => 'wali.tanpa.kelas@sentrisiswa.test',
        'status' => 'registered',
    ]);

    masukSebagai($wali);

    $this->get('/wali-kelas/pengajuan-poin/buat')
        ->assertSuccessful()
        ->assertDontSee('Ahmad Fauzi');
});

test('halaman buat pengajuan tidak menawarkan siswa yang sudah memiliki 100 poin', function () {
    [, , $siswa] = waliKelasDenganKelas();
    $siswaLain = siswaLainDiKelas($siswa->kelas_id, 'Doni Siswa Poin Kurang', '1000000099', '10099');
    catatPelanggaran($siswaLain, 'Terlambat Masuk', 'ringan', '2026-07-05', 15);

    $this->get('/wali-kelas/pengajuan-poin/buat')
        ->assertSuccessful()
        ->assertDontSee('Ahmad Fauzi (NISN:')
        ->assertSee('Doni Siswa Poin Kurang')
        ->assertSee('Sisa Poin: 85/100');
});
