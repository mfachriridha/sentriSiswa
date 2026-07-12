<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Profil Guru — Boundary Value Analysis
|--------------------------------------------------------------------------
|
| Tiga batas yang diuji:
|
| 1. Nomor HP: paling pendek 10 digit, paling panjang 15 digit.
| 2. Nama: paling panjang 100 karakter.
| 3. Foto profil: paling besar 2 MB (2048 KB).
|
*/

beforeEach(function () {
    Storage::fake('public');
});

// TS.PRG.012 / TC.PRG.012.001 — Negative — sedigit di bawah batas bawah
test('nomor hp sepanjang 9 digit ditolak', function () {
    waliKelasDenganKelas();

    $this->from('/wali-kelas/profil/edit')
        ->followingRedirects()
        ->put('/wali-kelas/profil', dataProfilGuru(['telepon' => '081234567']))
        ->assertSee('Nomor telepon minimal 10 digit.');
});

// TS.PRG.012 / TC.PRG.012.002 — Positive — tepat pada batas bawah
test('nomor hp sepanjang 10 digit diterima', function () {
    waliKelasDenganKelas();

    $this->followingRedirects()
        ->put('/wali-kelas/profil', dataProfilGuru(['telepon' => '0812345678']))
        ->assertSee('Profil berhasil diperbarui.');
});

// TS.PRG.013 / TC.PRG.013.001 — Positive — tepat pada batas atas
test('nomor hp sepanjang 15 digit diterima', function () {
    waliKelasDenganKelas();

    $this->followingRedirects()
        ->put('/wali-kelas/profil', dataProfilGuru(['telepon' => '081234567890123']))
        ->assertSee('Profil berhasil diperbarui.');
});

// TS.PRG.013 / TC.PRG.013.002 — Negative — sedigit di atas batas atas
test('nomor hp sepanjang 16 digit ditolak', function () {
    waliKelasDenganKelas();

    $this->from('/wali-kelas/profil/edit')
        ->followingRedirects()
        ->put('/wali-kelas/profil', dataProfilGuru(['telepon' => '0812345678901234']))
        ->assertSee('Nomor telepon maksimal 15 digit.');
});

// TS.PRG.014 / TC.PRG.014.001 — Positive — tepat pada batas panjang nama
test('nama sepanjang 100 karakter diterima', function () {
    waliKelasDenganKelas();

    $this->followingRedirects()
        ->put('/wali-kelas/profil', dataProfilGuru(['nama' => str_repeat('a', 100)]))
        ->assertSee('Profil berhasil diperbarui.');
});

// TS.PRG.014 / TC.PRG.014.002 — Negative — sekarakter di atas batas panjang nama
test('nama sepanjang 101 karakter ditolak', function () {
    waliKelasDenganKelas();

    $this->from('/wali-kelas/profil/edit')
        ->followingRedirects()
        ->put('/wali-kelas/profil', dataProfilGuru(['nama' => str_repeat('a', 101)]))
        ->assertSee('Nama tidak boleh lebih dari 100 karakter.');
});

// TS.PRG.015 / TC.PRG.015.001 — Positive — tepat pada batas ukuran foto
test('foto berukuran tepat 2 MB diterima', function () {
    waliKelasDenganKelas();

    $this->followingRedirects()
        ->post('/wali-kelas/profil/photo', [
            'photo' => UploadedFile::fake()->image('foto.jpg')->size(2048),
        ])
        ->assertSee('Foto berhasil diunggah.');
});

// TS.PRG.015 / TC.PRG.015.002 — Negative — di atas batas ukuran foto
test('foto berukuran lebih dari 2 MB ditolak', function () {
    waliKelasDenganKelas();

    $this->from('/wali-kelas/profil')
        ->followingRedirects()
        ->post('/wali-kelas/profil/photo', [
            'photo' => UploadedFile::fake()->image('foto.jpg')->size(2049),
        ])
        ->assertSee('Ukuran foto maksimal 2MB.');
});
