<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Login — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Pengujian black box: setiap pengujian meniru apa yang dilakukan pengguna di
| halaman masuk, lalu memeriksa apa yang muncul di layar setelahnya. Isi basis
| data dan keadaan internal aplikasi tidak pernah diperiksa langsung, karena
| pengguna juga tidak bisa melihatnya.
|
| Catatan mengenai kolom yang dikosongkan dan format email yang salah:
| Kolom email dan kata sandi ditandai wajib pada formulir, dan kolom email
| bertipe email. Peramban menahan pengiriman formulir lebih dulu untuk kasus
| itu, sehingga pengguna tidak pernah sampai melihat pesan dari sistem. Karena
| tidak pernah dialami pengguna, kasus tersebut tidak didokumentasikan.
|
*/

// TS.LOG.001 / TC.LOG.001.001 — Positive
test('admin berhasil masuk dan diarahkan ke dashboard admin', function () {
    Pengguna::factory()->admin()->create([
        'email' => 'admin@sentrisiswa.test',
        'status' => 'registered',
    ]);

    $this->get('/login')->assertSee('Masuk');

    $this->followingRedirects()
        ->post('/login', [
            'email' => 'admin@sentrisiswa.test',
            'password' => 'password',
        ])
        ->assertSee('Selamat datang');
});

// TS.LOG.002 / TC.LOG.002.001 — Positive
test('wali kelas berhasil masuk dan diarahkan ke dashboard guru', function () {
    Pengguna::factory()->homeroom()->create([
        'email' => 'walikelas@sentrisiswa.test',
        'status' => 'registered',
    ]);

    $this->followingRedirects()
        ->post('/login', [
            'email' => 'walikelas@sentrisiswa.test',
            'password' => 'password',
        ])
        ->assertSee('Dashboard Guru');
});

// TS.LOG.003 / TC.LOG.003.001 — Positive
test('guru bk berhasil masuk dan diarahkan ke dashboard guru', function () {
    Pengguna::factory()->counselor()->create([
        'email' => 'bk@sentrisiswa.test',
        'status' => 'registered',
    ]);

    $this->followingRedirects()
        ->post('/login', [
            'email' => 'bk@sentrisiswa.test',
            'password' => 'password',
        ])
        ->assertSee('Dashboard Guru');
});

// TS.LOG.004 / TC.LOG.004.001 — Positive
test('guru kesiswaan berhasil masuk dan diarahkan ke dashboard guru', function () {
    Pengguna::factory()->studentAffairs()->create([
        'email' => 'kesiswaan@sentrisiswa.test',
        'status' => 'registered',
    ]);

    $this->followingRedirects()
        ->post('/login', [
            'email' => 'kesiswaan@sentrisiswa.test',
            'password' => 'password',
        ])
        ->assertSee('Dashboard Guru');
});

// TS.LOG.005 / TC.LOG.005.001 — Positive
test('siswa berhasil masuk dan diarahkan ke dashboard siswa', function () {
    Pengguna::factory()->student()->create([
        'email' => 'siswa@sentrisiswa.test',
        'status' => 'registered',
    ]);

    $this->followingRedirects()
        ->post('/login', [
            'email' => 'siswa@sentrisiswa.test',
            'password' => 'password',
        ])
        ->assertSee('Dashboard Siswa');
});

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

// TS.LOG.008 / TC.LOG.008.001 — Positive
test('pengguna keluar dari akun dan kembali ke halaman masuk', function () {
    Pengguna::factory()->student()->create([
        'email' => 'siswa@sentrisiswa.test',
        'status' => 'registered',
    ]);

    $this->followingRedirects()
        ->post('/login', [
            'email' => 'siswa@sentrisiswa.test',
            'password' => 'password',
        ])
        ->assertSee('Dashboard Siswa');

    $this->followingRedirects()
        ->post('/logout')
        ->assertSee('Masuk')
        ->assertDontSee('Dashboard Siswa');
});
