<?php

use App\Models\JenisPelanggaran;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Jenis Pelanggaran (Kesiswaan) — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Pengujian black box: kesiswaan masuk lewat halaman masuk, lalu mengelola daftar
| jenis pelanggaran yang nanti dipakai untuk mencatat pelanggaran siswa. Hasilnya
| diperiksa dari apa yang muncul di layar, bukan dari basis data.
|
| Tiap jenis pelanggaran punya kategori sanksi, dan poin yang dikurangi harus
| masuk rentang kategorinya:
|
|   Sanksi Ringan          5 - 25 poin
|   Sanksi Sedang         26 - 50 poin
|   Sanksi Berat          51 - 75 poin
|   Sanksi Sangat Berat   76 - 100 poin
|
| Jenis yang sudah dipakai pada catatan pelanggaran siswa tidak bisa dihapus,
| hanya bisa dinonaktifkan.
|
*/

/** Kesiswaan yang sudah masuk ke aplikasi. */
function kesiswaanMasuk(): Pengguna
{
    $kesiswaan = Pengguna::factory()->studentAffairs()->create([
        'nama' => 'Bagas Kesiswaan',
        'email' => 'kesiswaan@sentrisiswa.test',
        'status' => 'registered',
    ]);

    masukSebagai($kesiswaan);

    return $kesiswaan;
}

/** Isian jenis pelanggaran yang sah. */
function dataJenisPelanggaran(array $ubahan = []): array
{
    return array_merge([
        'nama' => 'Terlambat masuk kelas',
        'kategori' => 'ringan',
        'pengurangan_poin' => 10,
        'keterangan' => 'Datang setelah bel masuk berbunyi.',
        'aktif' => 1,
    ], $ubahan);
}

// TS.JEP.001 / TC.JEP.001.001 — Positive
test('kesiswaan menambah jenis pelanggaran baru', function () {
    kesiswaanMasuk();

    $this->followingRedirects()
        ->post('/kesiswaan/jenis-pelanggaran', dataJenisPelanggaran())
        ->assertSee('Jenis pelanggaran berhasil ditambahkan.')
        ->assertSee('Terlambat masuk kelas')
        ->assertSee('Sanksi Ringan');
});

// TS.JEP.002 / TC.JEP.002.001 — Negative
test('jenis pelanggaran ditolak ketika namanya sudah dipakai', function () {
    kesiswaanMasuk();
    JenisPelanggaran::create(dataJenisPelanggaran());

    $this->from('/kesiswaan/jenis-pelanggaran/create')
        ->followingRedirects()
        ->post('/kesiswaan/jenis-pelanggaran', dataJenisPelanggaran())
        ->assertSee('Nama pelanggaran sudah digunakan.');
});

// TS.JEP.003 / TC.JEP.003.001 — Negative
test('jenis pelanggaran ditolak ketika poinnya di luar rentang kategori', function () {
    kesiswaanMasuk();

    // Kategori Sanksi Ringan hanya menerima 5 sampai 25 poin.
    $this->from('/kesiswaan/jenis-pelanggaran/create')
        ->followingRedirects()
        ->post('/kesiswaan/jenis-pelanggaran', dataJenisPelanggaran([
            'kategori' => 'ringan',
            'pengurangan_poin' => 60,
        ]))
        ->assertSee('Poin untuk kategori ini harus berada di antara 5 sampai 25.');
});

// TS.JEP.004 / TC.JEP.004.001 — Negative
test('jenis pelanggaran ditolak ketika poinnya diisi huruf', function () {
    kesiswaanMasuk();

    $this->from('/kesiswaan/jenis-pelanggaran/create')
        ->followingRedirects()
        ->post('/kesiswaan/jenis-pelanggaran', dataJenisPelanggaran(['pengurangan_poin' => 'sepuluh']))
        ->assertSee('Poin pelanggaran harus berupa angka.');
});

// TS.JEP.005 / TC.JEP.005.001 — Positive
test('kesiswaan mengubah jenis pelanggaran yang sudah ada', function () {
    kesiswaanMasuk();
    $jenis = JenisPelanggaran::create(dataJenisPelanggaran());

    $this->followingRedirects()
        ->put("/kesiswaan/jenis-pelanggaran/{$jenis->id}", dataJenisPelanggaran([
            'nama' => 'Terlambat lebih dari 15 menit',
            'pengurangan_poin' => 15,
        ]))
        ->assertSee('Jenis pelanggaran berhasil diperbarui.')
        ->assertSee('Terlambat lebih dari 15 menit');
});

// TS.JEP.006 / TC.JEP.006.001 — Positive
test('kesiswaan menghapus jenis pelanggaran yang belum pernah dipakai', function () {
    kesiswaanMasuk();
    $jenis = JenisPelanggaran::create(dataJenisPelanggaran());

    $this->followingRedirects()
        ->delete("/kesiswaan/jenis-pelanggaran/{$jenis->id}")
        ->assertSee('Jenis pelanggaran berhasil dihapus.')
        ->assertSee('Belum ada data jenis pelanggaran.');
});

// TS.JEP.007 / TC.JEP.007.001 — Negative
test('jenis pelanggaran yang sudah dipakai tidak bisa dihapus', function () {
    [, , $siswa] = kelasBerisiSiswa();
    $pelanggaran = catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06');

    kesiswaanMasuk();

    $this->followingRedirects()
        ->delete("/kesiswaan/jenis-pelanggaran/{$pelanggaran->jenis_pelanggaran_id}")
        ->assertSee('Jenis pelanggaran sudah dipakai pada data pelanggaran siswa. Nonaktifkan jika tidak ingin digunakan lagi.')
        ->assertSee('Terlambat masuk kelas');
});

// TS.JEP.008 / TC.JEP.008.001 — Positive
test('kesiswaan menonaktifkan jenis pelanggaran agar tidak dipakai lagi', function () {
    kesiswaanMasuk();
    $jenis = JenisPelanggaran::create(dataJenisPelanggaran());

    $this->followingRedirects()
        ->put("/kesiswaan/jenis-pelanggaran/{$jenis->id}", dataJenisPelanggaran(['aktif' => 0]))
        ->assertSee('Jenis pelanggaran berhasil diperbarui.');

    $this->get('/kesiswaan/jenis-pelanggaran?status=inactive')
        ->assertSee('Terlambat masuk kelas');
});

// TS.JEP.009 / TC.JEP.009.001 — Positive
test('kesiswaan mencari jenis pelanggaran berdasarkan nama', function () {
    kesiswaanMasuk();
    JenisPelanggaran::create(dataJenisPelanggaran());
    JenisPelanggaran::create(dataJenisPelanggaran([
        'nama' => 'Berkelahi',
        'kategori' => 'berat',
        'pengurangan_poin' => 60,
    ]));

    $this->get('/kesiswaan/jenis-pelanggaran?search=Berkelahi')
        ->assertSee('Berkelahi')
        ->assertDontSee('Terlambat masuk kelas');
});

// TS.JEP.010 / TC.JEP.010.001 — Positive
test('kesiswaan menyaring jenis pelanggaran berdasarkan kategori', function () {
    kesiswaanMasuk();
    JenisPelanggaran::create(dataJenisPelanggaran());
    JenisPelanggaran::create(dataJenisPelanggaran([
        'nama' => 'Berkelahi',
        'kategori' => 'berat',
        'pengurangan_poin' => 60,
    ]));

    $this->get('/kesiswaan/jenis-pelanggaran?category=berat')
        ->assertSee('Berkelahi')
        ->assertDontSee('Terlambat masuk kelas');
});

// TS.JEP.011 / TC.JEP.011.001 — Positive
test('daftar jenis pelanggaran yang masih kosong menampilkan keterangannya', function () {
    kesiswaanMasuk();

    $this->get('/kesiswaan/jenis-pelanggaran')
        ->assertSee('Belum ada data jenis pelanggaran.');
});
