<?php

use App\Models\JenisPelanggaran;
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
| Yang diuji di berkas ini adalah isiannya: nama (baru atau sudah dipakai), poin
| (di dalam rentang kategorinya, di luar rentang, atau berisi huruf), serta kata
| kunci pencarian dan pilihan kategori pada penyaring.
|
| Keadaan jenis pelanggaran - aktif, nonaktif, dan sudah dipakai sehingga tak bisa
| dihapus - diuji di JenisPelanggaran_stt.php; batas poin tiap kategori di _bva.php;
| serta menghapus dan daftar kosong di _uc.php.
|
*/

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
