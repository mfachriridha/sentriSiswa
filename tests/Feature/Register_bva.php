<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Daftar Akun — Boundary Value Analysis
|--------------------------------------------------------------------------
|
| Menguji nilai tepat di batas yang diperbolehkan dan tepat di luarnya:
|   - Panjang kata sandi : minimal 8 karakter.
|   - Panjang nomor HP guru : 10 sampai 15 digit.
|
| Kondisi awal disiapkan memakai pembantu yang sama dengan pengujian
| Equivalence Partitioning, lalu kedua tahap pendaftaran ditempuh seperti
| pengguna dan hasilnya diperiksa dari apa yang muncul di layar.
|
*/

/** Menempuh tahap pertama pendaftaran untuk siswa yang datanya sudah disiapkan. */
function lanjutkanPendaftaranSiswa(string $nisn): void
{
    test()->post('/daftar/verifikasi', [
        'peran' => 'student',
        'identity' => $nisn,
    ])->assertSee('Lengkapi Profil');
}

/** Menempuh tahap pertama pendaftaran untuk guru yang datanya sudah disiapkan. */
function lanjutkanPendaftaranGuru(string $nip): void
{
    test()->post('/daftar/verifikasi', [
        'peran' => 'teacher',
        'identity' => $nip,
    ])->assertSee('Nomor HP');
}

// ── Batas panjang kata sandi: minimal 8 karakter ───────────────────────────

// TS.REG.013 / TC.REG.013.001 — Negative
test('kata sandi tujuh karakter ditolak karena kurang dari batas minimum', function () {
    siswaBelumPunyaAkun(nisn: '2234567890', nis: '20001');
    lanjutkanPendaftaranSiswa('2234567890');

    $this->from('/daftar/lengkapi')
        ->followingRedirects()
        ->post('/daftar/lengkapi', [
            'email' => 'sandi.tujuh@sentrisiswa.test',
            'password' => 'Rahas12',
            'password_confirmation' => 'Rahas12',
        ])
        ->assertSee('Kata sandi minimal 8 karakter.');
});

// TS.REG.013 / TC.REG.013.002 — Positive
test('kata sandi delapan karakter diterima karena tepat di batas minimum', function () {
    siswaBelumPunyaAkun(nisn: '2234567891', nis: '20002');
    lanjutkanPendaftaranSiswa('2234567891');

    $this->followingRedirects()
        ->post('/daftar/lengkapi', [
            'email' => 'sandi.delapan@sentrisiswa.test',
            'password' => 'Rahas123',
            'password_confirmation' => 'Rahas123',
        ])
        ->assertSee('Pendaftaran berhasil. Silakan masuk dengan akun Anda.');
});

// ── Batas panjang nomor HP guru: 10 sampai 15 digit ────────────────────────

// TS.REG.014 / TC.REG.014.001 — Negative
test('nomor hp guru sembilan digit ditolak karena kurang dari batas minimum', function () {
    guruBelumPunyaAkun(nip: '198501012020122001');
    lanjutkanPendaftaranGuru('198501012020122001');

    $this->from('/daftar/lengkapi')
        ->followingRedirects()
        ->post('/daftar/lengkapi', [
            'email' => 'hp.sembilan@sentrisiswa.test',
            'password' => 'Rahasia123',
            'password_confirmation' => 'Rahasia123',
            'telepon' => '081234567',
        ])
        ->assertSee('Nomor HP minimal 10 digit.');
});

// TS.REG.014 / TC.REG.014.002 — Positive
test('nomor hp guru sepuluh digit diterima karena tepat di batas minimum', function () {
    guruBelumPunyaAkun(nip: '198501012020122002');
    lanjutkanPendaftaranGuru('198501012020122002');

    $this->followingRedirects()
        ->post('/daftar/lengkapi', [
            'email' => 'hp.sepuluh@sentrisiswa.test',
            'password' => 'Rahasia123',
            'password_confirmation' => 'Rahasia123',
            'telepon' => '0812345678',
        ])
        ->assertSee('Pendaftaran berhasil. Silakan masuk dengan akun Anda.');
});

// TS.REG.015 / TC.REG.015.001 — Positive
test('nomor hp guru lima belas digit diterima karena tepat di batas maksimum', function () {
    guruBelumPunyaAkun(nip: '198501012020122003');
    lanjutkanPendaftaranGuru('198501012020122003');

    $this->followingRedirects()
        ->post('/daftar/lengkapi', [
            'email' => 'hp.limabelas@sentrisiswa.test',
            'password' => 'Rahasia123',
            'password_confirmation' => 'Rahasia123',
            'telepon' => '081234567890123',
        ])
        ->assertSee('Pendaftaran berhasil. Silakan masuk dengan akun Anda.');
});

// TS.REG.015 / TC.REG.015.002 — Negative
test('nomor hp guru enam belas digit ditolak karena melebihi batas maksimum', function () {
    guruBelumPunyaAkun(nip: '198501012020122004');
    lanjutkanPendaftaranGuru('198501012020122004');

    $this->from('/daftar/lengkapi')
        ->followingRedirects()
        ->post('/daftar/lengkapi', [
            'email' => 'hp.enambelas@sentrisiswa.test',
            'password' => 'Rahasia123',
            'password_confirmation' => 'Rahasia123',
            'telepon' => '0812345678901234',
        ])
        ->assertDontSee('Pendaftaran berhasil. Silakan masuk dengan akun Anda.');
});
