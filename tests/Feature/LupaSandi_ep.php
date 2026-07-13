<?php

use App\Models\Pengguna;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Lupa Kata Sandi — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Pengujian black box: pengguna yang lupa kata sandinya meminta tautan pemulihan
| dari halaman masuk, lalu memakai tautan itu untuk menyetel kata sandi baru.
| Hasilnya diperiksa dari apa yang muncul di layar, bukan dari basis data.
|
| Sistem sengaja memberi jawaban yang sama untuk email terdaftar maupun tidak,
| supaya tidak ketahuan email mana yang punya akun di sekolah ini. Tautannya
| sendiri hanya dikirim kalau emailnya memang terdaftar.
|
*/

/** Pengguna yang sudah punya akun. */
function penggunaLupaSandi(): Pengguna
{
    return Pengguna::factory()->student()->create([
        'nama' => 'Ahmad Fauzi',
        'email' => 'ahmad@sentrisiswa.test',
        'status' => 'registered',
    ]);
}

/** Tautan pemulihan yang diterima pengguna lewat email. */
function tautanPemulihan(Pengguna $pengguna): string
{
    $tautan = '';

    Notification::assertSentTo($pengguna, ResetPassword::class, function (ResetPassword $notifikasi) use (&$tautan): bool {
        $tautan = "/reset-sandi/{$notifikasi->token}";

        return true;
    });

    return $tautan;
}

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

// TS.LKS.003 / TC.LKS.003.001 — Positive
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

// TS.LKS.004 / TC.LKS.004.001 — Positive
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

// TS.LKS.005 / TC.LKS.005.001 — Negative
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
