<?php

use App\Models\JenisPelanggaran;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Jenis Pelanggaran (Kesiswaan) — State Transition Testing
|--------------------------------------------------------------------------
|
| Keadaan sebuah jenis pelanggaran:
|
|   aktif        --(dinonaktifkan)--> nonaktif
|   belum dipakai --(dihapus)-------> hilang
|   sudah dipakai --(dihapus)-------> DITOLAK, hanya boleh dinonaktifkan
|
| Begitu sebuah jenis dipakai pada catatan pelanggaran seorang siswa, ia tidak
| boleh dihapus lagi: catatan siswa itu akan kehilangan keterangannya. Yang
| menentukan boleh atau tidaknya bukan isian apa pun, melainkan apakah jenis itu
| sudah pernah dipakai.
|
| Jenis yang nonaktif juga berhenti muncul di pilihan saat kesiswaan mencatat
| pelanggaran baru, sementara catatan lama yang memakainya tetap utuh.
|
*/

// TS.JEP.007 / TC.JEP.007.001 — Negative — sudah dipakai, tak boleh dihapus
test('jenis pelanggaran yang sudah dipakai tidak bisa dihapus', function () {
    [, , $siswa] = kelasBerisiSiswa();
    $pelanggaran = catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06');

    kesiswaanMasuk();

    $this->followingRedirects()
        ->delete("/kesiswaan/jenis-pelanggaran/{$pelanggaran->jenis_pelanggaran_id}")
        ->assertSee('Jenis pelanggaran sudah dipakai pada data pelanggaran siswa. Nonaktifkan jika tidak ingin digunakan lagi.')
        ->assertSee('Terlambat masuk kelas');
});

// TS.JEP.008 / TC.JEP.008.001 — Positive — aktif → nonaktif
test('kesiswaan menonaktifkan jenis pelanggaran agar tidak dipakai lagi', function () {
    kesiswaanMasuk();
    $jenis = JenisPelanggaran::create(dataJenisPelanggaran());

    $this->followingRedirects()
        ->put("/kesiswaan/jenis-pelanggaran/{$jenis->id}", dataJenisPelanggaran(['aktif' => 0]))
        ->assertSee('Jenis pelanggaran berhasil diperbarui.');

    $this->get('/kesiswaan/jenis-pelanggaran?status=inactive')
        ->assertSee('Terlambat masuk kelas');
});

// TS.JEP.016 / TC.JEP.016.001 — Negative — nonaktif hilang dari pilihan, catatan lama tetap utuh
test('jenis pelanggaran yang dinonaktifkan tidak lagi ditawarkan saat mencatat pelanggaran', function () {
    [, , $siswa] = kelasBerisiSiswa();
    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06', 10);

    kesiswaanMasuk();
    $jenis = JenisPelanggaran::where('nama', 'Terlambat masuk kelas')->firstOrFail();

    $this->put("/kesiswaan/jenis-pelanggaran/{$jenis->id}", dataJenisPelanggaran(['aktif' => 0]));

    // Tidak ditawarkan lagi untuk catatan baru.
    $this->get('/kesiswaan/pelanggaran-siswa/create')
        ->assertDontSee('Terlambat masuk kelas');

    // Tetapi catatan siswa yang terlanjur memakainya tidak ikut hilang.
    $this->get('/kesiswaan/pelanggaran-siswa')
        ->assertSee('Terlambat masuk kelas')
        ->assertSee('Ahmad Fauzi');
});
