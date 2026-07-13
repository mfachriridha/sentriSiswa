<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Profil Admin — State Transition Testing
|--------------------------------------------------------------------------
|
| Pengujian black box: admin masuk lewat halaman masuk, lalu mengelola
| profilnya sendiri. Hasilnya diperiksa dari apa yang muncul di layar, bukan
| dari basis data.
|
| Admin boleh mengganti email dan nomor WhatsApp secara langsung. Penggantian
| kata sandi punya alurnya sendiri: sistem mengirim kode OTP ke email admin
| lebih dulu.
| Keadaan yang berpindah: admin meminta penggantian kata sandi, dan sistem mengirimkan
| kode OTP ke emailnya. Daur hidup kodenya sendiri diuji di GantiSandi_stt.php.
|
*/

// TS.PAD.009 / TC.PAD.009.001 — Positive
test('admin meminta penggantian kata sandi dan diarahkan ke halaman kode otp', function () {
    Mail::fake();
    adminProfil();

    $this->get('/admin/profil/ganti-sandi')->assertSee('Ganti Kata Sandi');

    $this->followingRedirects()
        ->post('/admin/profil/ganti-sandi')
        ->assertSee('Kode OTP telah dikirim ke email Anda.')
        ->assertSee('Verifikasi OTP');
});
