<?php

use App\Mail\OtpMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Profil Siswa — State Transition Testing
|--------------------------------------------------------------------------
|
| Pengujian black box: siswa yang sedang masuk memperbarui profilnya sendiri.
| Hasilnya diperiksa dari apa yang muncul di layar, bukan dari basis data.
|
| Siswa hanya boleh mengubah nomor HP, alamat, dan fotonya. Nama, NISN, NIS, dan
| kelasnya ditentukan sekolah, jadi tidak bisa diubah sendiri. Mengganti email
| harus dipastikan lewat kode OTP yang dikirim ke email barunya.
| Keadaan yang berpindah: email hanya berganti setelah kode OTP-nya diverifikasi.
| Selama belum, email lamanya tetap berlaku.
|
*/

beforeEach(function () {
    Storage::fake('public');
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
