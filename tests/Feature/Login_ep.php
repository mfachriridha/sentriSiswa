<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Login — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Kondisi masukannya adalah pasangan email dan kata sandi: sah (cocok dengan
| akun yang ada) atau tidak sah. Kelas tak sahnya dua, dan keduanya diuji di
| sini: kata sandi yang salah, dan email yang belum pernah didaftarkan.
|
| Alur masuk tiap peran beserta keluar dari akun ada di Login_uc.php, dan akun
| yang belum mendaftar ada di Login_stt.php.
|
| Catatan mengenai kolom yang dikosongkan dan format email yang salah:
| Kolom email dan kata sandi ditandai wajib pada formulir, dan kolom email
| bertipe email. Peramban menahan pengiriman formulir lebih dulu untuk kasus
| itu, sehingga pengguna tidak pernah sampai melihat pesan dari sistem. Karena
| tidak pernah dialami pengguna, kasus tersebut tidak didokumentasikan.
|
*/

// TS.LOG.006 / TC.LOG.006.001 — Negative
test('pengguna gagal masuk karena kata sandi yang dimasukkan salah', function () {
    Pengguna::factory()->student()->create([
        'email' => 'siswa@sentrisiswa.test',
        'status' => 'registered',
    ]);

    $this->from('/login')
        ->followingRedirects()
        ->post('/login', [
            'email' => 'siswa@sentrisiswa.test',
            'password' => 'kata-sandi-salah',
        ])
        ->assertSee('Email atau kata sandi tidak sesuai.')
        ->assertDontSee('Dashboard Siswa');
});

// TS.LOG.007 / TC.LOG.007.001 — Negative
test('pengguna gagal masuk karena email belum pernah didaftarkan', function () {
    $this->from('/login')
        ->followingRedirects()
        ->post('/login', [
            'email' => 'belum.terdaftar@sentrisiswa.test',
            'password' => 'password',
        ])
        ->assertSee('Email atau kata sandi tidak sesuai.');
});
