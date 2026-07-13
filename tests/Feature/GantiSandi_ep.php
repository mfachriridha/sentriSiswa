<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Ganti Kata Sandi lewat OTP — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Dua isian yang diuji di berkas ini:
|   - Kode OTP : cocok dengan yang dikirim, atau tidak (termasuk dikosongkan).
|   - Kata sandi baru : memuat huruf dan angka, dan konfirmasinya sama persis.
|
| Daur hidup kodenya sendiri - terkirim, kedaluwarsa, terpakai - diuji di
| GantiSandi_stt.php, dan panjang kode maupun kata sandi di GantiSandi_bva.php.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

// TS.GKS.004 / TC.GKS.004.001 — Negative
test('kode otp yang salah ditolak', function () {
    Mail::fake();
    $kode = mintaKodeGantiSandi();

    $kodeSalah = str_pad((string) ((((int) $kode) + 1) % 1000000), 6, '0', STR_PAD_LEFT);

    $this->from('/otp/verifikasi')
        ->followingRedirects()
        ->post('/otp/verifikasi', ['otp' => $kodeSalah])
        ->assertSee('Kode OTP salah atau sudah kedaluwarsa.');
});

// TS.GKS.006 / TC.GKS.006.001 — Negative
test('kode otp yang belum diisi ditolak', function () {
    Mail::fake();
    mintaKodeGantiSandi();

    $this->from('/otp/verifikasi')
        ->followingRedirects()
        ->post('/otp/verifikasi', ['otp' => ''])
        ->assertSee('Masukkan kode OTP terlebih dahulu.');
});

// TS.GKS.008 / TC.GKS.008.001 — Negative
test('konfirmasi kata sandi baru yang tidak sama ditolak', function () {
    Mail::fake();
    $kode = mintaKodeGantiSandi();

    $this->post('/otp/verifikasi', ['otp' => $kode]);

    $this->from('/wali-kelas/profil/set-sandi-baru')
        ->followingRedirects()
        ->post('/wali-kelas/profil/set-sandi-baru', [
            'password' => 'sandibaru123',
            'password_confirmation' => 'sandilain456',
        ])
        ->assertSee('Konfirmasi kata sandi tidak sesuai.');
});

// TS.GKS.009 / TC.GKS.009.001 — Negative
test('kata sandi baru tanpa angka ditolak', function () {
    Mail::fake();
    $kode = mintaKodeGantiSandi();

    $this->post('/otp/verifikasi', ['otp' => $kode]);

    $this->from('/wali-kelas/profil/set-sandi-baru')
        ->followingRedirects()
        ->post('/wali-kelas/profil/set-sandi-baru', [
            'password' => 'sandibarutanpaangka',
            'password_confirmation' => 'sandibarutanpaangka',
        ])
        ->assertSee('Kata sandi harus memuat huruf dan angka.');
});
