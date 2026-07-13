<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Daftar Akun — Use Case Testing
|--------------------------------------------------------------------------
|
| Alur pemakaian utuh, melintasi lebih dari satu halaman: siswa yang datanya
| sudah dimasukkan sekolah memverifikasi identitasnya, melengkapi pendaftaran,
| lalu langsung memakai akun barunya untuk masuk.
|
| Nilai-nilai yang diisi di sepanjang alur itu diuji terpisah di Register_ep.php
| dan Register_bva.php.
|
*/

// TS.REG.004 / TC.REG.004.001 — Positive
test('siswa yang baru mendaftar bisa langsung masuk memakai akunnya', function () {
    siswaBelumPunyaAkun(nisn: '1234567892', nis: '10003');

    $this->followingRedirects()->post('/daftar/verifikasi', [
        'peran' => 'student',
        'identity' => '1234567892',
    ])->assertSee('Lengkapi Profil');

    $this->followingRedirects()
        ->post('/daftar/lengkapi', [
            'email' => 'siswa.langsung@sentrisiswa.test',
            'password' => 'Rahasia123',
            'password_confirmation' => 'Rahasia123',
        ])
        ->assertSee('Pendaftaran berhasil. Silakan masuk dengan akun Anda.');

    $this->followingRedirects()
        ->post('/login', [
            'email' => 'siswa.langsung@sentrisiswa.test',
            'password' => 'Rahasia123',
        ])
        ->assertSee('Dashboard Siswa');
});
