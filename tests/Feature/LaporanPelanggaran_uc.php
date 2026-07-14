<?php

use App\Models\Kelas;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Laporan Kesiswaan — Use Case Testing
|--------------------------------------------------------------------------
|
| Pengujian black box: kesiswaan masuk lewat halaman masuk, lalu menyusun laporan
| pelanggaran seluruh sekolah. Hasilnya diperiksa dari apa yang muncul di layar,
| bukan dari basis data.
|
| Laporan memuat dua tabel: pelanggaran yang memotong poin, dan penambahan poin
| yang sudah disetujui. Keduanya bisa disaring per rentang tanggal, per kelas,
| per tingkat, dan per kategori, lalu diunduh sebagai berkas Excel maupun PDF.
| Alur pemakaian tanpa isian: kesiswaan membaca laporan pelanggaran seluruh sekolah
| beserta penambahan poin yang sudah disetujui, mengunduhnya jadi berkas Excel, dan
| membukanya di halaman cetak. Termasuk alur alternatifnya: laporan yang tidak
| menemukan data sama sekali.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

// TS.LAP.001 / TC.LAP.001.001 — Positive
test('kesiswaan melihat laporan pelanggaran seluruh sekolah', function () {
    [, , $siswa] = kelasBerisiSiswa();
    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06', 10);

    kesiswaanMasuk();

    $this->get('/kesiswaan/laporan')
        ->assertSee('Laporan Kesiswaan')
        ->assertSee('Ahmad Fauzi')
        ->assertSee('10 IPA 1')
        ->assertSee('Terlambat masuk kelas')
        ->assertSee('-10');
});

// TS.LAP.002 / TC.LAP.002.001 — Positive
test('laporan memuat penambahan poin yang sudah disetujui', function () {
    [$wali, , $siswa] = kelasBerisiSiswa();
    pengajuanPoinDisetujui($siswa->nisn, $wali->id, 'Juara lomba cerdas cermat.', 5, '2026-07-06');

    kesiswaanMasuk();

    $this->get('/kesiswaan/laporan')
        ->assertSee('Penambahan Poin (Disetujui)')
        ->assertSee('Juara lomba cerdas cermat.')
        ->assertSee('+5');
});

// TS.LAP.007 / TC.LAP.007.001 — Positive
test('kesiswaan mengunduh laporan dalam berkas excel', function () {
    Excel::fake();
    [, , $siswa] = kelasBerisiSiswa();
    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06', 10);

    kesiswaanMasuk();

    $this->get('/kesiswaan/laporan/ekspor-excel')
        ->assertSuccessful();

    Excel::assertDownloaded('laporan-pelanggaran.xlsx');
});

// TS.LAP.013 / TC.LAP.013.001 — Negative
test('ekspor laporan ditolak ketika tidak ada pelanggaran yang cocok', function () {
    Excel::fake();
    kelasBerisiSiswa();
    kesiswaanMasuk();

    // Belum ada pelanggaran sama sekali. Tanpa penjagaan ini, berkasnya tetap
    // terunduh - cuma berisi judul kolom - dan kesiswaan mengira ekspornya berhasil.
    $this->followingRedirects()
        ->get('/kesiswaan/laporan/ekspor-excel')
        ->assertSee('Tidak ada pelanggaran yang cocok dengan penyaring ini, jadi tidak ada yang bisa diekspor.');

    // Kalau berkasnya benar-benar terunduh, yang diterima peramban adalah berkas -
    // bukan halaman - dan pesan di atas tidak akan pernah muncul.
});

// TS.LAP.008 / TC.LAP.008.001 — Positive
test('kesiswaan membuka halaman cetak laporan', function () {
    [, , $siswa] = kelasBerisiSiswa();
    catatPelanggaran($siswa, 'Terlambat masuk kelas', 'ringan', '2026-07-06', 10);

    kesiswaanMasuk();

    $this->get('/kesiswaan/laporan/cetak')
        ->assertSuccessful()
        ->assertSee('Laporan Kesiswaan')
        ->assertSee('Ahmad Fauzi')
        ->assertSee('10 IPA 1')
        ->assertSee('Penambahan Poin (Disetujui)')
        ->assertSee('Cetak / Simpan PDF');
});

// TS.LAP.009 / TC.LAP.009.001 — Positive
test('laporan yang tidak menemukan data menampilkan keterangannya', function () {
    kelasBerisiSiswa();
    kesiswaanMasuk();

    $this->get('/kesiswaan/laporan')
        ->assertSee('Tidak ada data laporan.')
        ->assertSee('Tidak ada penambahan poin disetujui pada rentang ini.');
});
