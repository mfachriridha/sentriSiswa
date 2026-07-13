<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Profil Siswa — Use Case Testing
|--------------------------------------------------------------------------
|
| Pengujian black box: siswa yang sedang masuk memperbarui profilnya sendiri.
| Hasilnya diperiksa dari apa yang muncul di layar, bukan dari basis data.
|
| Siswa hanya boleh mengubah nomor HP, alamat, dan fotonya. Nama, NISN, NIS, dan
| kelasnya ditentukan sekolah, jadi tidak bisa diubah sendiri. Mengganti email
| harus dipastikan lewat kode OTP yang dikirim ke email barunya.
| Alur pemakaian tanpa isian: siswa membuka halaman profilnya sendiri. Termasuk
| pengecualiannya: nama dan kelasnya bukan miliknya untuk diubah - itu wewenang sekolah.
|
*/

beforeEach(function () {
    Storage::fake('public');
});

// TS.PRS.001 / TC.PRS.001.001 — Positive
test('siswa melihat profilnya sendiri', function () {
    $siswa = siswaMasuk();

    $this->get('/siswa/profil')
        ->assertSee('Profil Saya')
        ->assertSee('Ahmad Fauzi')
        ->assertSee('10 IPA 1')
        ->assertSee($siswa->nisn);
});

// TS.PRS.008 / TC.PRS.008.001 — Negative
test('siswa tidak bisa mengubah nama maupun kelasnya sendiri', function () {
    $siswa = siswaMasuk();

    // Nama dan kelas ditentukan sekolah, jadi kolomnya tidak ada di halaman ubah profil.
    $this->get('/siswa/profil/edit')
        ->assertDontSee('name="nama"', escape: false)
        ->assertDontSee('name="kelas_id"', escape: false);

    $this->followingRedirects()
        ->put('/siswa/profil', dataProfilSiswa($siswa->pengguna, ['nama' => 'Nama Karangan']))
        ->assertSee('Ahmad Fauzi')
        ->assertDontSee('Nama Karangan');
});
