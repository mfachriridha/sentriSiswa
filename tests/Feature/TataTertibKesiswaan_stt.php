<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Tata Tertib (Kesiswaan) — State Transition Testing
|--------------------------------------------------------------------------
|
| Keadaan sebuah berkas tata tertib:
|
|   draft --(dipublikasikan)--> aktif
|   aktif --(dinonaktifkan)---> draft
|   aktif --(tata tertib lain dipublikasikan)--> draft
|
| Hanya satu tata tertib yang boleh aktif pada satu waktu, jadi mengaktifkan yang
| baru otomatis memundurkan yang lama jadi draft. Keadaan itu pula yang menentukan
| apa yang dibaca siswa: yang aktif tampil di halaman siswa, yang draft tidak.
|
| Jadi yang menentukan siswa bisa membaca atau tidak bukan isi berkasnya, melainkan
| keadaannya - dan keadaan itu bisa berubah tanpa berkasnya disentuh sama sekali.
|
*/

beforeEach(function () {
    Storage::fake('public');
});

// TS.TTK.002 / TC.TTK.002.001 — Positive — lahir langsung dalam keadaan aktif
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

// TS.TTK.005 / TC.TTK.005.001 — Positive — aktif → draft, karena ada yang menggantikan
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

// TS.TTK.006 / TC.TTK.006.001 — Positive — aktif → draft
test('kesiswaan menonaktifkan tata tertib yang sedang aktif', function () {
    kesiswaanMasuk();
    $aktif = tataTertibTersimpan('Tata Tertib Sekolah 2026', dipublikasikan: true);

    $this->followingRedirects()
        ->put("/kesiswaan/tata-tertib/{$aktif->id}/unpublish")
        ->assertSee('Tata tertib berhasil dinonaktifkan.')
        ->assertSee('Draft');
});

// TS.TTK.009 / TC.TTK.009.001 — Positive — keadaan aktif menentukan apa yang dibaca siswa
test('siswa membaca tata tertib yang sedang aktif', function () {
    [, , $siswa] = kelasBerisiSiswa();
    tataTertibTersimpan('Tata Tertib Sekolah 2026', dipublikasikan: true);

    masukSebagai($siswa->pengguna);

    $this->get('/siswa/tata-tertib')
        ->assertSee('Tata Tertib Sekolah 2026');
});

// TS.TTK.010 / TC.TTK.010.001 — Negative — keadaan draft menyembunyikannya dari siswa
test('siswa tidak melihat tata tertib yang masih draft', function () {
    [, , $siswa] = kelasBerisiSiswa();
    tataTertibTersimpan('Tata Tertib Sekolah 2026');

    masukSebagai($siswa->pengguna);

    $this->get('/siswa/tata-tertib')
        ->assertDontSee('Tata Tertib Sekolah 2026');
});

// TS.TTK.013 / TC.TTK.013.001 — Negative — dinonaktifkan, siswa langsung kehilangan aksesnya
test('siswa berhenti melihat tata tertib begitu kesiswaan menonaktifkannya', function () {
    [, , $siswa] = kelasBerisiSiswa();
    $aktif = tataTertibTersimpan('Tata Tertib Sekolah 2026', dipublikasikan: true);

    kesiswaanMasuk();
    $this->put("/kesiswaan/tata-tertib/{$aktif->id}/unpublish");
    $this->post('/logout');

    masukSebagai($siswa->pengguna);

    // Berkasnya tidak disentuh sama sekali; yang berubah hanya keadaannya.
    $this->get('/siswa/tata-tertib')
        ->assertDontSee('Tata Tertib Sekolah 2026');
});
