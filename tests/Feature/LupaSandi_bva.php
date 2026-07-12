<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Lupa Kata Sandi — Boundary Value Analysis
|--------------------------------------------------------------------------
|
| Batas yang diuji adalah panjang kata sandi baru, yaitu paling pendek 8
| karakter. Diuji tepat di bawah dan tepat pada batas itu.
|
*/

// TS.LKS.008 / TC.LKS.008.001 — Negative — sekarakter di bawah batas
test('kata sandi baru sepanjang 7 karakter ditolak', function () {
    Notification::fake();
    $pengguna = penggunaLupaSandi();

    $this->post('/lupa-sandi', ['email' => $pengguna->email]);
    $tautan = tautanPemulihan($pengguna);

    $this->from($tautan)
        ->followingRedirects()
        ->post('/reset-sandi', [
            'token' => basename($tautan),
            'email' => $pengguna->email,
            'password' => 'sandi12',
            'password_confirmation' => 'sandi12',
        ])
        ->assertSee('Kata sandi minimal 8 karakter.');
});

// TS.LKS.008 / TC.LKS.008.002 — Positive — tepat pada batas
test('kata sandi baru sepanjang 8 karakter diterima', function () {
    Notification::fake();
    $pengguna = penggunaLupaSandi();

    $this->post('/lupa-sandi', ['email' => $pengguna->email]);
    $tautan = tautanPemulihan($pengguna);

    $this->followingRedirects()
        ->post('/reset-sandi', [
            'token' => basename($tautan),
            'email' => $pengguna->email,
            'password' => 'sandi123',
            'password_confirmation' => 'sandi123',
        ])
        ->assertSee('Kata sandi berhasil direset. Silakan masuk.');
});
