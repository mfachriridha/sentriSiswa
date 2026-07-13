<?php

use App\Mail\OtpMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Profil Guru — State Transition Testing
|--------------------------------------------------------------------------
|
| Pengujian black box: guru yang sedang masuk memperbarui profilnya sendiri.
| Hasilnya diperiksa dari apa yang muncul di layar, bukan dari basis data.
|
| Nama, nomor HP, dan foto langsung tersimpan begitu disimpan. Email berbeda:
| menggantinya harus dipastikan lewat kode OTP yang dikirim ke email barunya,
| supaya akun tidak bisa dipindahkan diam-diam ke email orang lain.
| Keadaan yang berpindah: email hanya berganti setelah kode OTP-nya diverifikasi.
| Selama belum, email lamanya tetap berlaku - jadi email yang salah ketik tidak bisa
| mengunci pemiliknya di luar akunnya sendiri.
|
*/

beforeEach(function () {
    Storage::fake('public');
});

// TS.PRG.005 / TC.PRG.005.001 — Positive
test('mengganti email memicu pengiriman kode otp lebih dulu', function () {
    Mail::fake();
    waliKelasDenganKelas();

    $this->followingRedirects()
        ->put('/wali-kelas/profil', dataProfilGuru(['email' => 'raka.baru@sentrisiswa.test']))
        ->assertSee('Kode OTP telah dikirim ke email Anda saat ini untuk memverifikasi perubahan.');

    Mail::assertSent(OtpMail::class);
});

// TS.PRG.006 / TC.PRG.006.001 — Positive
test('email berganti setelah kode otp diverifikasi', function () {
    Mail::fake();
    waliKelasDenganKelas();

    $this->put('/wali-kelas/profil', dataProfilGuru(['email' => 'raka.baru@sentrisiswa.test']));

    $this->followingRedirects()
        ->post('/otp/verifikasi', ['otp' => kodeOtpTerkirim()])
        ->assertSee('Perubahan berhasil disimpan.')
        ->assertSee('raka.baru@sentrisiswa.test');
});

// TS.PRG.007 / TC.PRG.007.001 — Negative
test('email belum berganti selama kode otp belum diverifikasi', function () {
    Mail::fake();
    waliKelasDenganKelas();

    $this->put('/wali-kelas/profil', dataProfilGuru(['email' => 'raka.baru@sentrisiswa.test']));

    // Kode OTP belum dimasukkan, jadi email lamanya masih yang berlaku.
    $this->get('/wali-kelas/profil')
        ->assertSee('wali.kelas@sentrisiswa.test')
        ->assertDontSee('raka.baru@sentrisiswa.test');
});
