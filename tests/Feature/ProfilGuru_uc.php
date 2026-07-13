<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Profil Guru — Use Case Testing
|--------------------------------------------------------------------------
|
| Pengujian black box: guru yang sedang masuk memperbarui profilnya sendiri.
| Hasilnya diperiksa dari apa yang muncul di layar, bukan dari basis data.
|
| Nama, nomor HP, dan foto langsung tersimpan begitu disimpan. Email berbeda:
| menggantinya harus dipastikan lewat kode OTP yang dikirim ke email barunya,
| supaya akun tidak bisa dipindahkan diam-diam ke email orang lain.
| Alur pemakaian tanpa isian: guru membuka halaman profilnya sendiri.
|
*/

beforeEach(function () {
    Storage::fake('public');
});

// TS.PRG.001 / TC.PRG.001.001 — Positive
test('guru melihat profilnya sendiri', function () {
    waliKelasDenganKelas();

    $this->get('/wali-kelas/profil')
        ->assertSee('Profil Saya')
        ->assertSee('Raka Pradipta')
        ->assertSee('wali.kelas@sentrisiswa.test');
});
