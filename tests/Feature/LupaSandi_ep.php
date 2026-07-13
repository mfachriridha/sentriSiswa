<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Lupa Kata Sandi — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Dua isian yang diuji di berkas ini:
|   - Email : terdaftar atau tidak terdaftar.
|   - Kata sandi baru beserta konfirmasinya, dan token tautannya: dikenali atau tidak.
|
| Sistem sengaja memberi jawaban yang sama untuk email terdaftar maupun tidak,
| supaya tidak ketahuan email mana yang punya akun di sekolah ini. Tautannya
| sendiri hanya dikirim kalau emailnya memang terdaftar.
|
| Daur hidup tautannya - berlaku, terpakai, tak bisa dipakai lagi - diuji di
| LupaSandi_stt.php, dan panjang kata sandinya di LupaSandi_bva.php.
|
*/

// TS.LKS.001 / TC.LKS.001.001 — Positive
test('pengguna meminta tautan pemulihan dengan email terdaftar', function () {
    Notification::fake();
    penggunaLupaSandi();

    $this->from('/lupa-sandi')
        ->followingRedirects()
        ->post('/lupa-sandi', ['email' => 'ahmad@sentrisiswa.test'])
        ->assertSee('Jika email terdaftar, tautan reset kata sandi akan dikirimkan.');
});

// TS.LKS.002 / TC.LKS.002.001 — Negative
test('email yang belum terdaftar tetap diberi jawaban yang sama', function () {
    Notification::fake();
    penggunaLupaSandi();

    // Jawabannya sengaja dibuat sama, supaya tidak ketahuan email mana yang punya akun.
    $this->from('/lupa-sandi')
        ->followingRedirects()
        ->post('/lupa-sandi', ['email' => 'orang.asing@sentrisiswa.test'])
        ->assertSee('Jika email terdaftar, tautan reset kata sandi akan dikirimkan.');

    Notification::assertNothingSent();
});

// TS.LKS.006 / TC.LKS.006.001 — Negative
test('konfirmasi kata sandi yang tidak sama ditolak', function () {
    Notification::fake();
    $pengguna = penggunaLupaSandi();

    $this->post('/lupa-sandi', ['email' => $pengguna->email]);
    $tautan = tautanPemulihan($pengguna);

    $this->from($tautan)
        ->followingRedirects()
        ->post('/reset-sandi', [
            'token' => basename($tautan),
            'email' => $pengguna->email,
            'password' => 'sandibaru123',
            'password_confirmation' => 'sandilain456',
        ])
        ->assertSee('Konfirmasi kata sandi tidak cocok.');
});

// TS.LKS.007 / TC.LKS.007.001 — Negative
test('tautan pemulihan yang tidak dikenali ditolak', function () {
    Notification::fake();
    $pengguna = penggunaLupaSandi();

    $this->from('/reset-sandi/tautan-ngawur')
        ->followingRedirects()
        ->post('/reset-sandi', [
            'token' => 'tautan-ngawur',
            'email' => $pengguna->email,
            'password' => 'sandibaru123',
            'password_confirmation' => 'sandibaru123',
        ])
        ->assertDontSee('Kata sandi berhasil direset. Silakan masuk.');
});
