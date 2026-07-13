<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Tata Tertib (Kesiswaan) — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Dua isian yang diuji di berkas ini:
|   - Berkasnya : PDF (sah) atau bukan PDF (tak sah).
|   - Judulnya  : terisi (sah) atau dikosongkan (tak sah).
|
| Keadaan berkasnya - draft, aktif, dan apa yang boleh dibaca siswa - diuji di
| TataTertibKesiswaan_stt.php; ukuran berkas dan panjang judul di _bva.php; serta
| menghapus dan daftar kosong di _uc.php.
|
*/

beforeEach(function () {
    Storage::fake('public');
});

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
