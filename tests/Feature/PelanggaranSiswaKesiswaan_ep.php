<?php

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
| Kesiswaan memilih siswa, memilih jenis pelanggaran, dan mengisi tanggal
| kejadiannya. Poin yang dikurangi mengikuti jenis pelanggaran yang dipilih,
| tidak diisi manual.
|
| Yang diuji di berkas ini adalah isiannya: siswa yang dipilih (sudah mendaftar
| atau belum), tanggal kejadian (sampai hari ini atau melampauinya), serta kata
| kunci pencarian dan pilihan penyaring.
|
| Poin yang berpindah naik-turun mengikuti catatan, dan jenis pelanggaran yang
| sudah dinonaktifkan, diuji di PelanggaranSiswaKesiswaan_stt.php; batas tanggal
| dan poin di _bva.php; serta rincian dan daftar kosong di _uc.php.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

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
