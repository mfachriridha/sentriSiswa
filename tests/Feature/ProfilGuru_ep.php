<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Profil Guru — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Pengujian black box: guru yang sedang masuk memperbarui profilnya sendiri.
| Hasilnya diperiksa dari apa yang muncul di layar, bukan dari basis data.
|
| Nama, nomor HP, dan foto langsung tersimpan begitu disimpan. Email berbeda:
| menggantinya harus dipastikan lewat kode OTP yang dikirim ke email barunya,
| supaya akun tidak bisa dipindahkan diam-diam ke email orang lain.
| Yang diuji di berkas ini adalah isiannya: nama, nomor HP, email, dan jenis berkas foto.
|
*/

beforeEach(function () {
    Storage::fake('public');
});

/** Isian profil guru yang sah. */
function dataProfilGuru(array $ubahan = []): array
{
    return array_merge([
        'nama' => 'Raka Pradipta',
        'email' => 'wali.kelas@sentrisiswa.test',
        'telepon' => '081234567890',
    ], $ubahan);
}

// TS.PRG.002 / TC.PRG.002.001 — Positive
test('guru memperbarui nama dan nomor hp', function () {
    waliKelasDenganKelas();

    $this->followingRedirects()
        ->put('/wali-kelas/profil', dataProfilGuru([
            'nama' => 'Raka Pradipta Wijaya',
            'telepon' => '081298765432',
        ]))
        ->assertSee('Profil berhasil diperbarui.')
        ->assertSee('Raka Pradipta Wijaya')
        ->assertSee('081298765432');
});

// TS.PRG.003 / TC.PRG.003.001 — Negative
test('profil ditolak ketika namanya dikosongkan', function () {
    waliKelasDenganKelas();

    $this->from('/wali-kelas/profil/edit')
        ->followingRedirects()
        ->put('/wali-kelas/profil', dataProfilGuru(['nama' => '']))
        ->assertSee('Nama tidak boleh kosong.');
});

// TS.PRG.004 / TC.PRG.004.001 — Negative
test('profil ditolak ketika nomor hp mengandung huruf', function () {
    waliKelasDenganKelas();

    $this->from('/wali-kelas/profil/edit')
        ->followingRedirects()
        ->put('/wali-kelas/profil', dataProfilGuru(['telepon' => '08123abc456']))
        ->assertSee('Format nomor telepon tidak valid.');
});

// TS.PRG.008 / TC.PRG.008.001 — Negative
test('profil ditolak ketika emailnya sudah dipakai akun lain', function () {
    waliKelasDenganKelas();

    Pengguna::factory()->counselor()->create([
        'email' => 'sudah.dipakai@sentrisiswa.test',
        'status' => 'registered',
    ]);

    $this->from('/wali-kelas/profil/edit')
        ->followingRedirects()
        ->put('/wali-kelas/profil', dataProfilGuru(['email' => 'sudah.dipakai@sentrisiswa.test']))
        ->assertSee('Email sudah digunakan.');
});

// TS.PRG.009 / TC.PRG.009.001 — Positive
test('guru mengunggah foto profil', function () {
    waliKelasDenganKelas();

    $this->followingRedirects()
        ->post('/wali-kelas/profil/photo', [
            'photo' => UploadedFile::fake()->image('foto.jpg'),
        ])
        ->assertSee('Foto berhasil diunggah.');
});

// TS.PRG.010 / TC.PRG.010.001 — Negative
test('foto profil ditolak ketika berkasnya bukan gambar', function () {
    waliKelasDenganKelas();

    $this->from('/wali-kelas/profil')
        ->followingRedirects()
        ->post('/wali-kelas/profil/photo', [
            'photo' => UploadedFile::fake()->create('catatan.pdf', 50, 'application/pdf'),
        ])
        ->assertSee('Foto harus berupa file gambar.');
});

// TS.PRG.011 / TC.PRG.011.001 — Positive
test('guru menghapus foto profilnya', function () {
    waliKelasDenganKelas();

    $this->post('/wali-kelas/profil/photo', [
        'photo' => UploadedFile::fake()->image('foto.jpg'),
    ]);

    $this->followingRedirects()
        ->delete('/wali-kelas/profil/photo')
        ->assertSee('Foto berhasil dihapus.');
});
