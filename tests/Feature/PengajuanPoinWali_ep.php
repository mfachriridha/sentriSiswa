<?php

use App\Models\Kelas;
use App\Models\PengajuanPoin;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Pengajuan Poin (Wali Kelas) — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Pengujian black box: wali kelas masuk lewat halaman masuk, lalu mengajukan
| penambahan poin untuk siswa di kelasnya. Hasilnya diperiksa dari apa yang
| muncul di layar, bukan dari basis data.
|
| Wali kelas hanya memilih siswa dan menuliskan alasannya — jumlah poinnya
| ditentukan kesiswaan saat menyetujui. Pengajuan yang baru dikirim berstatus
| menunggu, dan wali kelas bisa memantaunya di daftar pengajuan.
| Yang diuji di berkas ini adalah isiannya: siswa yang dipilih, alasan pengajuan, dan
| penyaring status.
|
*/

/** Isian pengajuan poin yang sah. */
function pengajuanPoinSah(ProfilSiswa $siswa, array $ubahan = []): array
{
    $kategori = \App\Models\KategoriPengajuanPoin::firstOrCreate(
        ['nama' => 'Lomba Cerdas Cermat'],
        ['grup' => 'Lomba Eksternal', 'poin' => 20, 'urutan' => 1]
    );

    return array_merge([
        'profil_siswa_id' => $siswa->nisn,
        'kategori_pengajuan_poin_id' => $kategori->id,
        'alasan' => 'Menjadi juara pertama lomba cerdas cermat tingkat kabupaten.',
    ], $ubahan);
}

// TS.PPW.001 / TC.PPW.001.001 — Positive
test('wali kelas mengirim pengajuan penambahan poin untuk siswa di kelasnya', function () {
    [, , $siswa] = waliKelasDenganKelas();

    $this->followingRedirects()
        ->post('/wali-kelas/pengajuan-poin', pengajuanPoinSah($siswa))
        ->assertSee('Pengajuan penambahan poin berhasil dikirim ke kesiswaan.')
        ->assertSee('Ahmad Fauzi')
        ->assertSee('Menunggu');
});

// TS.PPW.003 / TC.PPW.003.001 — Negative
test('wali kelas tidak bisa mengajukan poin untuk siswa dari kelas lain', function () {
    waliKelasDenganKelas();

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

    $this->post('/wali-kelas/pengajuan-poin', pengajuanPoinSah($siswaLain))
        ->assertForbidden();
});

// TS.PPW.004 / TC.PPW.004.001 — Negative
test('pengajuan ditolak ketika alasan tidak diisi', function () {
    [, , $siswa] = waliKelasDenganKelas();

    $this->from('/wali-kelas/pengajuan-poin/buat')
        ->followingRedirects()
        ->post('/wali-kelas/pengajuan-poin', pengajuanPoinSah($siswa, ['alasan' => '']))
        ->assertSee('Keterangan detail wajib diisi.');
});

// TS.PPW.005 / TC.PPW.005.001 — Negative
test('pengajuan ditolak ketika siswa belum dipilih', function () {
    waliKelasDenganKelas();

    $this->from('/wali-kelas/pengajuan-poin/buat')
        ->followingRedirects()
        ->post('/wali-kelas/pengajuan-poin', ['profil_siswa_id' => '', 'alasan' => 'Berprestasi.'])
        ->assertSee('Siswa wajib dipilih.');
});

// TS.PPW.007 / TC.PPW.007.001 — Positive
test('wali kelas menyaring daftar pengajuan berdasarkan status', function () {
    [$wali, $kelas, $siswa] = waliKelasDenganKelas();
    $siswaLain = siswaLainDiKelas($kelas->id, 'Siti Aminah', '1234567892', '10003');

    PengajuanPoin::create([
        'profil_siswa_id' => $siswa->nisn,
        'diajukan_oleh_id' => $wali->id,
        'alasan' => 'Juara lomba cerdas cermat.',
        'status' => 'pending',
    ]);
    PengajuanPoin::create([
        'profil_siswa_id' => $siswaLain->nisn,
        'diajukan_oleh_id' => $wali->id,
        'alasan' => 'Aktif membantu kegiatan sekolah.',
        'status' => 'approved',
        'jumlah_poin' => 10,
    ]);

    $this->get('/wali-kelas/pengajuan-poin?status=pending')
        ->assertSee('Juara lomba cerdas cermat.')
        ->assertDontSee('Aktif membantu kegiatan sekolah.');
});
