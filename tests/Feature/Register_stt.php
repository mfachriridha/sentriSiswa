<?php

use App\Models\Pengguna;
use App\Models\ProfilGuru;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Daftar Akun — State Transition Testing
|--------------------------------------------------------------------------
|
| Keadaan akun: belum terdaftar → terdaftar.
|
|   belum terdaftar --(mendaftar)-->  terdaftar
|   terdaftar       --(mendaftar)-->  DITOLAK
|
| Sekolah memasukkan data siswa dan guru lebih dulu; akunnya lahir dalam keadaan
| "belum terdaftar". Mendaftar memindahkannya ke "terdaftar", dan perpindahan itu
| hanya boleh terjadi sekali. Yang menentukan diterima atau ditolaknya bukan nilai
| yang diketik, melainkan keadaan akun itu saat identitasnya diperiksa.
|
*/

// TS.REG.007 / TC.REG.007.001 — Negative — mendaftar ulang akun yang sudah terdaftar
test('pendaftaran ditolak karena identitas siswa sudah pernah didaftarkan', function () {
    $pengguna = Pengguna::factory()->student()->create([
        'email' => 'sudah.punya.akun@sentrisiswa.test',
        'status' => 'registered',
    ]);

    ProfilSiswa::factory()->create([
        'pengguna_id' => $pengguna->id,
        'nisn' => '1234567893',
        'nis' => '10004',
    ]);

    $this->from('/daftar')
        ->followingRedirects()
        ->post('/daftar/verifikasi', [
            'peran' => 'student',
            'identity' => '1234567893',
        ])
        ->assertSee('NISN/NIS sudah terdaftar. Silakan masuk.');
});

// TS.REG.008 / TC.REG.008.001 — Negative — mendaftar ulang akun guru yang sudah terdaftar
test('pendaftaran ditolak karena identitas guru sudah pernah didaftarkan', function () {
    $pengguna = Pengguna::factory()->homeroom()->create([
        'email' => 'guru.sudah.punya@sentrisiswa.test',
        'status' => 'registered',
    ]);

    ProfilGuru::factory()->create([
        'pengguna_id' => $pengguna->id,
        'nip' => '198501012020121002',
    ]);

    $this->from('/daftar')
        ->followingRedirects()
        ->post('/daftar/verifikasi', [
            'peran' => 'teacher',
            'identity' => '198501012020121002',
        ])
        ->assertSee('NIP sudah terdaftar. Silakan masuk.');
});

// TS.REG.018 / TC.REG.018.001 — Negative — akunnya sudah dipakai orang, tak bisa didaftarkan lagi
test('identitas yang baru saja didaftarkan tidak bisa didaftarkan untuk kedua kalinya', function () {
    siswaBelumPunyaAkun(nisn: '1234567898', nis: '10009');

    $this->followingRedirects()->post('/daftar/verifikasi', [
        'peran' => 'student',
        'identity' => '1234567898',
    ])->assertSee('Lengkapi Profil');

    $this->followingRedirects()
        ->post('/daftar/lengkapi', [
            'email' => 'pendaftar.pertama@sentrisiswa.test',
            'password' => 'Rahasia123',
            'password_confirmation' => 'Rahasia123',
        ])
        ->assertSee('Pendaftaran berhasil. Silakan masuk dengan akun Anda.');

    // Akunnya kini "terdaftar". Orang lain yang tahu NISN-nya tidak boleh
    // mendaftarkannya lagi dan merebut akun itu dengan email miliknya sendiri.
    $this->from('/daftar')
        ->followingRedirects()
        ->post('/daftar/verifikasi', [
            'peran' => 'student',
            'identity' => '1234567898',
        ])
        ->assertSee('NISN/NIS sudah terdaftar. Silakan masuk.')
        ->assertDontSee('Lengkapi Profil');
});
