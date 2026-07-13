<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Lupa Kata Sandi — State Transition Testing
|--------------------------------------------------------------------------
|
| Dua keadaan yang berpindah sekaligus.
|
| Tautan pemulihan:
|   belum diminta --(minta tautan)-->  berlaku
|   berlaku       --(dipakai)------->  terpakai
|   terpakai      --(dipakai lagi)-->  DITOLAK
|
| Kata sandi:
|   lama --(pemulihan berhasil)--> baru, dan yang lama mati saat itu juga
|
| Yang diuji perpindahannya, bukan nilai yang diketik: tautan yang sama persis
| berhasil sekali dan gagal di percobaan kedua, dan kata sandi lama yang tadinya
| benar berubah jadi salah tanpa pernah disentuh pemiliknya.
|
*/

// TS.LKS.003 / TC.LKS.003.001 — Positive — tautan berlaku → terpakai
test('pengguna menyetel kata sandi baru lewat tautan pemulihan', function () {
    Notification::fake();
    $pengguna = penggunaLupaSandi();

    $this->post('/lupa-sandi', ['email' => $pengguna->email]);

    $this->followingRedirects()
        ->post('/reset-sandi', [
            'token' => basename(tautanPemulihan($pengguna)),
            'email' => $pengguna->email,
            'password' => 'sandibaru123',
            'password_confirmation' => 'sandibaru123',
        ])
        ->assertSee('Kata sandi berhasil direset. Silakan masuk.');
});

// TS.LKS.004 / TC.LKS.004.001 — Positive — kata sandi baru berlaku
test('pengguna bisa masuk memakai kata sandi barunya', function () {
    Notification::fake();
    $pengguna = penggunaLupaSandi();

    $this->post('/lupa-sandi', ['email' => $pengguna->email]);
    $this->post('/reset-sandi', [
        'token' => basename(tautanPemulihan($pengguna)),
        'email' => $pengguna->email,
        'password' => 'sandibaru123',
        'password_confirmation' => 'sandibaru123',
    ]);

    $this->post('/logout');

    $this->followingRedirects()
        ->post('/login', [
            'email' => $pengguna->email,
            'password' => 'sandibaru123',
        ])
        ->assertSee('Dashboard');
});

// TS.LKS.005 / TC.LKS.005.001 — Negative — kata sandi lama mati
test('kata sandi lama tidak berlaku lagi setelah dipulihkan', function () {
    Notification::fake();
    $pengguna = penggunaLupaSandi();

    $this->post('/lupa-sandi', ['email' => $pengguna->email]);
    $this->post('/reset-sandi', [
        'token' => basename(tautanPemulihan($pengguna)),
        'email' => $pengguna->email,
        'password' => 'sandibaru123',
        'password_confirmation' => 'sandibaru123',
    ]);

    $this->post('/logout');

    $this->from('/login')
        ->followingRedirects()
        ->post('/login', [
            'email' => $pengguna->email,
            'password' => 'password', // Kata sandi lama.
        ])
        ->assertSee('Email atau kata sandi tidak sesuai.');
});

// TS.LKS.009 / TC.LKS.009.001 — Negative — tautan terpakai → dipakai lagi
test('tautan pemulihan yang sudah terpakai tidak bisa dipakai untuk kedua kalinya', function () {
    Notification::fake();
    $pengguna = penggunaLupaSandi();

    $this->post('/lupa-sandi', ['email' => $pengguna->email]);
    $tautan = tautanPemulihan($pengguna);

    $this->followingRedirects()
        ->post('/reset-sandi', [
            'token' => basename($tautan),
            'email' => $pengguna->email,
            'password' => 'sandibaru123',
            'password_confirmation' => 'sandibaru123',
        ])
        ->assertSee('Kata sandi berhasil direset. Silakan masuk.');

    $this->post('/logout');

    // Tautan yang sama dibuka lagi. Kalau ia masih hidup, siapa pun yang sempat
    // membaca email itu - termasuk lama sesudahnya - masih bisa mengganti kata
    // sandi pemiliknya.
    $this->from($tautan)
        ->followingRedirects()
        ->post('/reset-sandi', [
            'token' => basename($tautan),
            'email' => $pengguna->email,
            'password' => 'sandiperampas99',
            'password_confirmation' => 'sandiperampas99',
        ])
        ->assertDontSee('Kata sandi berhasil direset. Silakan masuk.');

    // Kata sandi hasil percobaan kedua itu memang tidak pernah berlaku.
    $this->from('/login')
        ->followingRedirects()
        ->post('/login', [
            'email' => $pengguna->email,
            'password' => 'sandiperampas99',
        ])
        ->assertSee('Email atau kata sandi tidak sesuai.');
});
