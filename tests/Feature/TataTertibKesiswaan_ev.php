<?php

use App\Models\TataTertib;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Tata Tertib (Kesiswaan) — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Pengujian black box: kesiswaan masuk lewat halaman masuk, lalu mengunggah
| berkas tata tertib sekolah. Hasilnya diperiksa dari apa yang muncul di layar,
| bukan dari basis data.
|
| Berkas yang diunggah harus PDF dan paling besar 10 MB. Hanya satu tata tertib
| yang boleh aktif pada satu waktu: begitu satu tata tertib dipublikasikan, tata
| tertib lain otomatis kembali menjadi draft. Tata tertib yang aktif itulah yang
| dibaca siswa.
|
*/

beforeEach(function () {
    Storage::fake('public');
});

/** Sebuah tata tertib yang sudah ada di daftar. */
function tataTertibTersimpan(string $judul, bool $dipublikasikan = false): TataTertib
{
    return TataTertib::create([
        'judul' => $judul,
        'path_file' => 'school-rules/'.md5($judul).'.pdf',
        'dipublikasikan' => $dipublikasikan,
    ]);
}

// TS.TTK.001 / TC.TTK.001.001 — Positive
test('kesiswaan mengunggah tata tertib sebagai draft', function () {
    kesiswaanMasuk();

    $this->followingRedirects()
        ->post('/kesiswaan/tata-tertib', [
            'judul' => 'Tata Tertib Sekolah 2026',
            'file_pdf' => berkasTataTertib(),
        ])
        ->assertSee('Tata tertib berhasil diunggah.')
        ->assertSee('Tata Tertib Sekolah 2026')
        ->assertSee('Draft');
});

// TS.TTK.002 / TC.TTK.002.001 — Positive
test('kesiswaan mengunggah tata tertib dan langsung mengaktifkannya', function () {
    kesiswaanMasuk();

    $this->followingRedirects()
        ->post('/kesiswaan/tata-tertib', [
            'judul' => 'Tata Tertib Sekolah 2026',
            'file_pdf' => berkasTataTertib(),
            'dipublikasikan' => 1,
        ])
        ->assertSee('Tata tertib berhasil diunggah.')
        ->assertSee('Tata Tertib Sekolah 2026')
        ->assertSee('Aktif');
});

// TS.TTK.003 / TC.TTK.003.001 — Negative
test('tata tertib ditolak ketika berkasnya bukan pdf', function () {
    kesiswaanMasuk();

    $this->from('/kesiswaan/tata-tertib')
        ->followingRedirects()
        ->post('/kesiswaan/tata-tertib', [
            'judul' => 'Tata Tertib Sekolah 2026',
            'file_pdf' => UploadedFile::fake()->create('tata-tertib.docx', 100),
        ])
        ->assertSee('File tata tertib harus berupa PDF.');
});

// TS.TTK.004 / TC.TTK.004.001 — Negative
test('tata tertib ditolak ketika judulnya tidak diisi', function () {
    kesiswaanMasuk();

    $this->from('/kesiswaan/tata-tertib')
        ->followingRedirects()
        ->post('/kesiswaan/tata-tertib', [
            'judul' => '',
            'file_pdf' => berkasTataTertib(),
        ])
        ->assertSee('Judul tata tertib wajib diisi.');
});

// TS.TTK.005 / TC.TTK.005.001 — Positive
test('mengaktifkan sebuah tata tertib membuat tata tertib lain kembali jadi draft', function () {
    kesiswaanMasuk();
    tataTertibTersimpan('Tata Tertib Sekolah 2025', dipublikasikan: true);
    $baru = tataTertibTersimpan('Tata Tertib Sekolah 2026');

    $this->followingRedirects()
        ->put("/kesiswaan/tata-tertib/{$baru->id}/publish")
        ->assertSee('Tata tertib berhasil dipublikasikan.');

    // Hanya satu tata tertib yang boleh aktif, jadi yang lama otomatis jadi draft.
    $this->get('/kesiswaan/tata-tertib')
        ->assertSeeInOrder(['Tata Tertib Sekolah 2026', 'Aktif'])
        ->assertSeeInOrder(['Tata Tertib Sekolah 2025', 'Draft']);
});

// TS.TTK.006 / TC.TTK.006.001 — Positive
test('kesiswaan menonaktifkan tata tertib yang sedang aktif', function () {
    kesiswaanMasuk();
    $aktif = tataTertibTersimpan('Tata Tertib Sekolah 2026', dipublikasikan: true);

    $this->followingRedirects()
        ->put("/kesiswaan/tata-tertib/{$aktif->id}/unpublish")
        ->assertSee('Tata tertib berhasil dinonaktifkan.')
        ->assertSee('Draft');
});

// TS.TTK.007 / TC.TTK.007.001 — Positive
test('kesiswaan menghapus tata tertib', function () {
    kesiswaanMasuk();
    $tataTertib = tataTertibTersimpan('Tata Tertib Sekolah 2026');

    $this->followingRedirects()
        ->delete("/kesiswaan/tata-tertib/{$tataTertib->id}")
        ->assertSee('Tata tertib berhasil dihapus.')
        ->assertSee('Belum ada file tata tertib.');
});

// TS.TTK.008 / TC.TTK.008.001 — Positive
test('daftar tata tertib yang masih kosong menampilkan keterangannya', function () {
    kesiswaanMasuk();

    $this->get('/kesiswaan/tata-tertib')
        ->assertSee('Belum ada file tata tertib.');
});

// TS.TTK.009 / TC.TTK.009.001 — Positive
test('siswa membaca tata tertib yang sedang aktif', function () {
    [, , $siswa] = kelasBerisiSiswa();
    tataTertibTersimpan('Tata Tertib Sekolah 2026', dipublikasikan: true);

    masukSebagai($siswa->pengguna);

    $this->get('/siswa/tata-tertib')
        ->assertSee('Tata Tertib Sekolah 2026');
});

// TS.TTK.010 / TC.TTK.010.001 — Negative
test('siswa tidak melihat tata tertib yang masih draft', function () {
    [, , $siswa] = kelasBerisiSiswa();
    tataTertibTersimpan('Tata Tertib Sekolah 2026');

    masukSebagai($siswa->pengguna);

    $this->get('/siswa/tata-tertib')
        ->assertDontSee('Tata Tertib Sekolah 2026');
});
