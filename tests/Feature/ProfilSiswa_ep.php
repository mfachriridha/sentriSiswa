<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Profil Siswa — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Pengujian black box: siswa yang sedang masuk memperbarui profilnya sendiri.
| Hasilnya diperiksa dari apa yang muncul di layar, bukan dari basis data.
|
| Siswa hanya boleh mengubah nomor HP, alamat, dan fotonya. Nama, NISN, NIS, dan
| kelasnya ditentukan sekolah, jadi tidak bisa diubah sendiri. Mengganti email
| harus dipastikan lewat kode OTP yang dikirim ke email barunya.
| Yang diuji di berkas ini adalah isiannya: nomor HP, alamat, email, dan jenis berkas foto.
|
*/

beforeEach(function () {
    Storage::fake('public');
});

/** Isian profil siswa yang sah. */
function dataProfilSiswa(Pengguna $siswa, array $ubahan = []): array
{
    return array_merge([
        'email' => $siswa->email,
        'telepon' => '081234567890',
        'alamat' => 'Jalan Merdeka Nomor 10, Bandung.',
    ], $ubahan);
}

// TS.PRS.002 / TC.PRS.002.001 — Positive
test('siswa memperbarui nomor hp dan alamatnya', function () {
    $siswa = siswaMasuk();

    $this->followingRedirects()
        ->put('/siswa/profil', dataProfilSiswa($siswa->pengguna, [
            'telepon' => '081298765432',
            'alamat' => 'Jalan Merdeka Nomor 10, Bandung.',
        ]))
        ->assertSee('Profil berhasil diperbarui.')
        ->assertSee('081298765432')
        ->assertSee('Jalan Merdeka Nomor 10, Bandung.');
});

// TS.PRS.003 / TC.PRS.003.001 — Negative
test('profil ditolak ketika nomor hp mengandung huruf', function () {
    $siswa = siswaMasuk();

    $this->from('/siswa/profil/edit')
        ->followingRedirects()
        ->put('/siswa/profil', dataProfilSiswa($siswa->pengguna, ['telepon' => '08123abc456']))
        ->assertSee('Format nomor telepon tidak valid.');
});

// TS.PRS.007 / TC.PRS.007.001 — Negative
test('profil ditolak ketika emailnya sudah dipakai akun lain', function () {
    $siswa = siswaMasuk();

    Pengguna::factory()->student()->create([
        'email' => 'sudah.dipakai@sentrisiswa.test',
        'status' => 'registered',
    ]);

    $this->from('/siswa/profil/edit')
        ->followingRedirects()
        ->put('/siswa/profil', dataProfilSiswa($siswa->pengguna, [
            'email' => 'sudah.dipakai@sentrisiswa.test',
        ]))
        ->assertSee('Email sudah digunakan.');
});

// TS.PRS.009 / TC.PRS.009.001 — Positive
test('siswa mengunggah foto profil', function () {
    siswaMasuk();

    $this->followingRedirects()
        ->post('/siswa/profil/photo', [
            'photo' => UploadedFile::fake()->image('foto.jpg'),
        ])
        ->assertSee('Foto berhasil diunggah.');
});

// TS.PRS.010 / TC.PRS.010.001 — Negative
test('foto profil ditolak ketika berkasnya bukan gambar', function () {
    siswaMasuk();

    $this->from('/siswa/profil')
        ->followingRedirects()
        ->post('/siswa/profil/photo', [
            'photo' => UploadedFile::fake()->create('catatan.pdf', 50, 'application/pdf'),
        ])
        ->assertSee('Foto harus berupa file gambar.');
});

// TS.PRS.011 / TC.PRS.011.001 — Positive
test('siswa menghapus foto profilnya', function () {
    siswaMasuk();

    $this->post('/siswa/profil/photo', [
        'photo' => UploadedFile::fake()->image('foto.jpg'),
    ]);

    $this->followingRedirects()
        ->delete('/siswa/profil/photo')
        ->assertSee('Foto berhasil dihapus.');
});

// TS.PRS.012 / TC.PRS.012.001 — Positive
test('siswa mengunggah foto lewat form edit profil, satu form sekaligus dengan data lain', function () {
    $siswa = siswaMasuk();

    $this->followingRedirects()
        ->put('/siswa/profil', dataProfilSiswa($siswa->pengguna, [
            'photo' => UploadedFile::fake()->image('foto.jpg'),
        ]))
        ->assertSee('Profil berhasil diperbarui.')
        ->assertSee('storage/photos/students/');
});

// TS.PRS.013 / TC.PRS.013.001 — Positive
test('siswa menghapus foto lewat form edit profil', function () {
    $siswa = siswaMasuk();

    $this->put('/siswa/profil', dataProfilSiswa($siswa->pengguna, [
        'photo' => UploadedFile::fake()->image('foto.jpg'),
    ]));

    $this->followingRedirects()
        ->put('/siswa/profil', dataProfilSiswa($siswa->pengguna, [
            'delete_photo' => '1',
        ]))
        ->assertSee('Profil berhasil diperbarui.')
        ->assertDontSee('storage/photos/students/');
});
