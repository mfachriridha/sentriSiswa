<?php

use App\Models\JenisPelanggaran;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Pelanggaran Siswa (Kesiswaan) — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Pengujian black box: kesiswaan masuk lewat halaman masuk, lalu mencatat
| pelanggaran seorang siswa. Hasilnya diperiksa dari apa yang muncul di layar,
| bukan dari basis data.
|
| Kesiswaan memilih siswa, memilih jenis pelanggaran, dan mengisi tanggal
| kejadiannya. Poin yang dikurangi mengikuti jenis pelanggaran yang dipilih,
| tidak diisi manual. Pelanggaran yang dicatat langsung berlaku dan memotong
| poin siswa, tanpa perlu persetujuan siapa pun.
|
| Siswa yang belum mendaftarkan akunnya belum bisa dicatat pelanggarannya, dan
| jenis pelanggaran yang sudah dinonaktifkan tidak boleh dipilih lagi.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

/** Isian catatan pelanggaran yang sah. */
function dataPelanggaranSiswa(ProfilSiswa $siswa, JenisPelanggaran $jenis, array $ubahan = []): array
{
    return array_merge([
        'profil_siswa_id' => $siswa->nisn,
        'jenis_pelanggaran_id' => $jenis->id,
        'tanggal_pelanggaran' => '2026-07-06',
        'catatan' => 'Terlambat 20 menit tanpa keterangan.',
    ], $ubahan);
}

// TS.PLS.001 / TC.PLS.001.001 — Positive
test('kesiswaan mencatat pelanggaran seorang siswa', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = kelasBerisiSiswa();
    kesiswaanMasuk();
    $jenis = jenisPelanggaranTersedia();

    $this->followingRedirects()
        ->post('/kesiswaan/pelanggaran-siswa', dataPelanggaranSiswa($siswa, $jenis))
        ->assertSee('Pelanggaran siswa berhasil dicatat.')
        ->assertSee('Ahmad Fauzi')
        ->assertSee('Terlambat masuk kelas');
});

// TS.PLS.002 / TC.PLS.002.001 — Positive
test('poin siswa berkurang sesuai jenis pelanggaran yang dipilih', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = kelasBerisiSiswa();
    $pelanggaran = catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06', 10);

    kesiswaanMasuk();

    // Poin siswa mulai dari 100, jadi setelah dipotong 10 sisanya 90.
    $this->get("/kesiswaan/pelanggaran-siswa/{$pelanggaran->id}")
        ->assertSee('10 poin')
        ->assertSee('Sisa Poin: 90');
});

// TS.PLS.003 / TC.PLS.003.001 — Negative
test('pelanggaran ditolak ketika siswanya belum mendaftarkan akun', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, $kelas] = kelasBerisiSiswa();

    $penggunaBelumDaftar = Pengguna::factory()->student()->create([
        'nama' => 'Siswa Belum Daftar',
        'status' => 'unregistered',
    ]);
    $siswaBelumDaftar = ProfilSiswa::factory()->create([
        'pengguna_id' => $penggunaBelumDaftar->id,
        'nisn' => '1234567895',
        'nis' => '10009',
        'kelas_id' => $kelas->id,
    ]);

    kesiswaanMasuk();
    $jenis = jenisPelanggaranTersedia();

    $this->from('/kesiswaan/pelanggaran-siswa/create')
        ->followingRedirects()
        ->post('/kesiswaan/pelanggaran-siswa', dataPelanggaranSiswa($siswaBelumDaftar, $jenis))
        ->assertSee('Siswa belum terdaftar, belum bisa dicatat pelanggarannya.');
});

// TS.PLS.004 / TC.PLS.004.001 — Negative
test('pelanggaran ditolak ketika jenis pelanggarannya sudah dinonaktifkan', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = kelasBerisiSiswa();
    kesiswaanMasuk();
    $jenisNonaktif = jenisPelanggaranTersedia(['aktif' => false]);

    $this->from('/kesiswaan/pelanggaran-siswa/create')
        ->followingRedirects()
        ->post('/kesiswaan/pelanggaran-siswa', dataPelanggaranSiswa($siswa, $jenisNonaktif))
        ->assertSee('Jenis pelanggaran tidak aktif dan tidak dapat dipilih.');
});

// TS.PLS.005 / TC.PLS.005.001 — Negative
test('pelanggaran ditolak ketika tanggalnya melebihi hari ini', function () {
    Carbon::setTestNow('2026-07-10 08:00:00');
    [, , $siswa] = kelasBerisiSiswa();
    kesiswaanMasuk();
    $jenis = jenisPelanggaranTersedia();

    $this->from('/kesiswaan/pelanggaran-siswa/create')
        ->followingRedirects()
        ->post('/kesiswaan/pelanggaran-siswa', dataPelanggaranSiswa($siswa, $jenis, [
            'tanggal_pelanggaran' => '2026-07-11',
        ]))
        ->assertSee('Tanggal pelanggaran tidak boleh melebihi hari ini.');
});

// TS.PLS.006 / TC.PLS.006.001 — Positive
test('kesiswaan mencari catatan pelanggaran berdasarkan nama siswa', function () {
    [, $kelas, $siswa] = kelasBerisiSiswa();
    $siswaLain = siswaLainDiKelas($kelas->id, 'Siti Aminah', '1234567892', '10003');

    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06');
    catatPelanggaran($siswaLain, 'Membolos', 'sedang', '2026-07-06', 30);

    kesiswaanMasuk();

    $this->get('/kesiswaan/pelanggaran-siswa?search=Siti')
        ->assertSee('Siti Aminah')
        ->assertDontSee('Ahmad Fauzi');
});

// TS.PLS.007 / TC.PLS.007.001 — Positive
test('kesiswaan menyaring catatan pelanggaran berdasarkan kategori', function () {
    [, , $siswa] = kelasBerisiSiswa();

    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06');
    catatPelanggaran($siswa, 'Berkelahi', 'berat', '2026-07-07', 60);

    kesiswaanMasuk();

    // Nama jenis pelanggaran juga muncul sebagai pilihan di kolom penyaring, jadi
    // yang diperiksa adalah poin di barisnya, yang hanya tampil di tabel.
    $this->get('/kesiswaan/pelanggaran-siswa?kategori=berat')
        ->assertSee('60 poin')
        ->assertDontSee('10 poin');
});

// TS.PLS.008 / TC.PLS.008.001 — Positive
test('kesiswaan menyaring catatan pelanggaran berdasarkan tanggal kejadian', function () {
    [, , $siswa] = kelasBerisiSiswa();

    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06');
    catatPelanggaran($siswa, 'Berkelahi', 'berat', '2026-07-07', 60);

    kesiswaanMasuk();

    $this->get('/kesiswaan/pelanggaran-siswa?tanggal_pelanggaran=2026-07-07')
        ->assertSee('60 poin')
        ->assertDontSee('10 poin');
});

// TS.PLS.009 / TC.PLS.009.001 — Positive
test('kesiswaan melihat rincian sebuah catatan pelanggaran', function () {
    [, , $siswa] = kelasBerisiSiswa();
    $pelanggaran = catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06');

    kesiswaanMasuk();

    $this->get("/kesiswaan/pelanggaran-siswa/{$pelanggaran->id}")
        ->assertSee('Ahmad Fauzi')
        ->assertSee('Terlambat masuk kelas')
        ->assertSee('10 IPA 1');
});

// TS.PLS.010 / TC.PLS.010.001 — Positive
test('kesiswaan menghapus catatan pelanggaran yang salah dicatat', function () {
    [, , $siswa] = kelasBerisiSiswa();
    $pelanggaran = catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06');

    kesiswaanMasuk();

    $this->followingRedirects()
        ->delete("/kesiswaan/pelanggaran-siswa/{$pelanggaran->id}")
        ->assertSee('Pelanggaran siswa berhasil dihapus.')
        ->assertSee('Belum ada catatan pelanggaran siswa.');
});

// TS.PLS.011 / TC.PLS.011.001 — Positive
test('daftar pelanggaran siswa yang masih kosong menampilkan keterangannya', function () {
    kesiswaanMasuk();

    $this->get('/kesiswaan/pelanggaran-siswa')
        ->assertSee('Belum ada catatan pelanggaran siswa.');
});
