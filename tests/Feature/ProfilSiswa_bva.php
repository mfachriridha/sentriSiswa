<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Profil Siswa — Boundary Value Analysis
|--------------------------------------------------------------------------
|
| Dua batas yang diuji:
|
| 1. Nomor HP: paling pendek 10 digit, paling panjang 15 digit.
| 2. Foto profil: paling besar 2 MB (2048 KB).
|
*/

beforeEach(function () {
    Storage::fake('public');
});

// TS.PRS.012 / TC.PRS.012.001 — Negative — sedigit di bawah batas bawah
test('nomor hp sepanjang 9 digit ditolak', function () {
    $siswa = siswaMasuk();

    $this->from('/siswa/profil/edit')
        ->followingRedirects()
        ->put('/siswa/profil', dataProfilSiswa($siswa->pengguna, ['telepon' => '081234567']))
        ->assertSee('Nomor telepon minimal 10 digit.');
});

// TS.PRS.012 / TC.PRS.012.002 — Positive — tepat pada batas bawah
test('nomor hp sepanjang 10 digit diterima', function () {
    $siswa = siswaMasuk();

    $this->followingRedirects()
        ->put('/siswa/profil', dataProfilSiswa($siswa->pengguna, ['telepon' => '0812345678']))
        ->assertSee('Profil berhasil diperbarui.');
});

// TS.PRS.013 / TC.PRS.013.001 — Positive — tepat pada batas atas
test('nomor hp sepanjang 15 digit diterima', function () {
    $siswa = siswaMasuk();

    $this->followingRedirects()
        ->put('/siswa/profil', dataProfilSiswa($siswa->pengguna, ['telepon' => '081234567890123']))
        ->assertSee('Profil berhasil diperbarui.');
});

// TS.PRS.013 / TC.PRS.013.002 — Negative — sedigit di atas batas atas
test('nomor hp sepanjang 16 digit ditolak', function () {
    $siswa = siswaMasuk();

    $this->from('/siswa/profil/edit')
        ->followingRedirects()
        ->put('/siswa/profil', dataProfilSiswa($siswa->pengguna, ['telepon' => '0812345678901234']))
        ->assertSee('Nomor telepon maksimal 15 digit.');
});

// TS.PRS.014 / TC.PRS.014.001 — Positive — tepat pada batas ukuran foto
test('foto berukuran tepat 2 MB diterima', function () {
    siswaMasuk();

    $this->followingRedirects()
        ->post('/siswa/profil/photo', [
            'photo' => UploadedFile::fake()->image('foto.jpg')->size(2048),
        ])
        ->assertSee('Foto berhasil diunggah.');
});

// TS.PRS.014 / TC.PRS.014.002 — Negative — di atas batas ukuran foto
test('foto berukuran lebih dari 2 MB ditolak', function () {
    siswaMasuk();

    $this->from('/siswa/profil')
        ->followingRedirects()
        ->post('/siswa/profil/photo', [
            'photo' => UploadedFile::fake()->image('foto.jpg')->size(2049),
        ])
        ->assertSee('Ukuran foto maksimal 2MB.');
});
