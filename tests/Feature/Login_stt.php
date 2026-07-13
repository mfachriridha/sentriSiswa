<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Login — State Transition Testing
|--------------------------------------------------------------------------
|
| Keadaan sesi pengguna: tamu → masuk → tamu lagi.
|
|   tamu  --(email & kata sandi benar)-->  masuk
|   masuk --(klik Keluar)-------------->   tamu
|
| Yang diuji perpindahannya, bukan nilai yang diketik: halaman kerja hanya bisa
| dibuka dalam keadaan "masuk", dan begitu penggunanya keluar, halaman itu
| tertutup lagi. Menekan tombol kembali di peramban pun tidak membukanya.
|
*/

// TS.LOG.009 / TC.LOG.009.001 — Negative — keadaan tamu
test('halaman kerja tidak bisa dibuka sebelum masuk', function () {
    $this->followingRedirects()
        ->get('/siswa/dashboard')
        ->assertSee('Masuk')
        ->assertDontSee('Dashboard Siswa');
});

// TS.LOG.009 / TC.LOG.009.002 — Negative — kembali ke keadaan tamu sesudah keluar
test('halaman kerja tertutup lagi setelah pengguna keluar', function () {
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

    $this->post('/logout');

    // Membuka ulang alamat yang tadi terbuka: sekarang ditolak.
    $this->followingRedirects()
        ->get('/siswa/dashboard')
        ->assertSee('Masuk')
        ->assertDontSee('Dashboard Siswa');
});
