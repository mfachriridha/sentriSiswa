<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Login — Use Case Testing
|--------------------------------------------------------------------------
|
| Alur pemakaiannya, bukan nilai masukannya: tiap peran masuk lewat halaman yang
| sama, lalu mendarat di ruang kerjanya masing-masing. Yang membedakan kasus di
| berkas ini bukan apa yang diketik, melainkan siapa yang mengetiknya.
|
| Diakhiri alur keluar dari akun.
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
