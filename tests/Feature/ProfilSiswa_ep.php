<?php

use App\Mail\OtpMail;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
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

// TS.PRS.001 / TC.PRS.001.001 — Positive
test('siswa melihat profilnya sendiri', function () {
    $siswa = siswaMasuk();

    $this->get('/siswa/profil')
        ->assertSee('Profil Saya')
        ->assertSee('Ahmad Fauzi')
        ->assertSee('10 IPA 1')
        ->assertSee($siswa->nisn);
});

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

// TS.PRS.004 / TC.PRS.004.001 — Positive
test('mengganti email memicu pengiriman kode otp lebih dulu', function () {
    Mail::fake();
    $siswa = siswaMasuk();

    $this->followingRedirects()
        ->put('/siswa/profil', dataProfilSiswa($siswa->pengguna, [
            'email' => 'ahmad.baru@sentrisiswa.test',
        ]))
        ->assertSee('Kode OTP telah dikirim ke email Anda saat ini untuk memverifikasi perubahan.');

    Mail::assertSent(OtpMail::class);
});

// TS.PRS.005 / TC.PRS.005.001 — Positive
test('email berganti setelah kode otp diverifikasi', function () {
    Mail::fake();
    $siswa = siswaMasuk();

    $this->put('/siswa/profil', dataProfilSiswa($siswa->pengguna, [
        'email' => 'ahmad.baru@sentrisiswa.test',
    ]));

    $this->followingRedirects()
        ->post('/otp/verifikasi', ['otp' => kodeOtpTerkirim()])
        ->assertSee('Perubahan berhasil disimpan.')
        ->assertSee('ahmad.baru@sentrisiswa.test');
});

// TS.PRS.006 / TC.PRS.006.001 — Negative
test('email belum berganti selama kode otp belum diverifikasi', function () {
    Mail::fake();
    $siswa = siswaMasuk();

    $this->put('/siswa/profil', dataProfilSiswa($siswa->pengguna, [
        'email' => 'ahmad.baru@sentrisiswa.test',
    ]));

    $this->get('/siswa/profil')
        ->assertDontSee('ahmad.baru@sentrisiswa.test');
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
