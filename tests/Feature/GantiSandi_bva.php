<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Ganti Kata Sandi lewat OTP — Boundary Value Analysis
|--------------------------------------------------------------------------
|
| Tiga batas yang diuji:
|
| 1. Kode OTP harus tepat 6 angka. Lima angka masih ditolak.
| 2. Kode berlaku 10 menit. Sesaat sebelum genap 10 menit masih diterima, tepat
|    saat genap 10 menit sudah kedaluwarsa.
| 3. Kata sandi baru paling pendek 8 karakter.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

// TS.GKS.012 / TC.GKS.012.001 — Negative — seangka di bawah batas
test('kode otp sepanjang 5 angka ditolak', function () {
    Mail::fake();
    mintaKodeGantiSandi();

    $this->from('/otp/verifikasi')
        ->followingRedirects()
        ->post('/otp/verifikasi', ['otp' => '12345'])
        ->assertSee('Kode OTP harus 6 digit angka.');
});

// TS.GKS.012 / TC.GKS.012.002 — Positive — tepat pada batas
test('kode otp sepanjang 6 angka diterima', function () {
    Mail::fake();
    $kode = mintaKodeGantiSandi();

    $this->followingRedirects()
        ->post('/otp/verifikasi', ['otp' => $kode])
        ->assertSee('OTP berhasil diverifikasi. Masukkan kata sandi baru Anda.');
});

// TS.GKS.013 / TC.GKS.013.001 — Positive — sesaat sebelum masa berlaku habis
test('kode otp masih diterima sesaat sebelum 10 menit', function () {
    Mail::fake();
    Carbon::setTestNow('2026-07-06 08:00:00');
    $kode = mintaKodeGantiSandi();

    Carbon::setTestNow('2026-07-06 08:09:59');

    $this->followingRedirects()
        ->post('/otp/verifikasi', ['otp' => $kode])
        ->assertSee('OTP berhasil diverifikasi. Masukkan kata sandi baru Anda.');
});

// TS.GKS.013 / TC.GKS.013.002 — Negative — tepat saat masa berlaku habis
test('kode otp ditolak tepat setelah 10 menit', function () {
    Mail::fake();
    Carbon::setTestNow('2026-07-06 08:00:00');
    $kode = mintaKodeGantiSandi();

    Carbon::setTestNow('2026-07-06 08:10:00');

    $this->from('/otp/verifikasi')
        ->followingRedirects()
        ->post('/otp/verifikasi', ['otp' => $kode])
        ->assertSee('Kode OTP salah atau sudah kedaluwarsa.');
});

// TS.GKS.014 / TC.GKS.014.001 — Negative — sekarakter di bawah batas
test('kata sandi baru sepanjang 7 karakter ditolak', function () {
    Mail::fake();
    $kode = mintaKodeGantiSandi();

    $this->post('/otp/verifikasi', ['otp' => $kode]);

    $this->from('/wali-kelas/profil/set-sandi-baru')
        ->followingRedirects()
        ->post('/wali-kelas/profil/set-sandi-baru', [
            'password' => 'sandi12',
            'password_confirmation' => 'sandi12',
        ])
        ->assertSee('Kata sandi minimal 8 karakter.');
});

// TS.GKS.014 / TC.GKS.014.002 — Positive — tepat pada batas
test('kata sandi baru sepanjang 8 karakter diterima', function () {
    Mail::fake();
    $kode = mintaKodeGantiSandi();

    $this->post('/otp/verifikasi', ['otp' => $kode]);

    $this->followingRedirects()
        ->post('/wali-kelas/profil/set-sandi-baru', [
            'password' => 'sandi123',
            'password_confirmation' => 'sandi123',
        ])
        ->assertSee('Kata sandi berhasil diubah.');
});
