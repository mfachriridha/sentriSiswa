<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Data Siswa (Admin) — Boundary Value Analysis
|--------------------------------------------------------------------------
|
| Menguji nilai tepat di batas yang diperbolehkan dan tepat di luarnya:
|   - Panjang nama    : 3 sampai 100 karakter.
|   - Panjang NISN    : tepat 10 angka.
|   - Panjang NIS     : maksimal 15 angka.
|   - Panjang nomor HP: 10 sampai 15 karakter.
|
*/

/** Data siswa yang sah, agar setiap pengujian hanya mengubah satu kolom saja. */
function dataSiswaSah(array $ubahan = []): array
{
    return array_merge([
        'nama' => 'Ahmad Fauzi',
        'nisn' => '1234567890',
        'nis' => '10001',
        'jenis_kelamin' => 'L',
    ], $ubahan);
}

// ── Batas panjang nama: 3 sampai 100 karakter ──────────────────────────────

// TS.SIS.015 / TC.SIS.015.001 — Negative
test('nama dua karakter ditolak karena kurang dari batas minimum', function () {
    adminDataSiswa();

    $this->from('/admin/siswa/create')
        ->followingRedirects()
        ->post('/admin/siswa', dataSiswaSah(['nama' => 'Ab']))
        ->assertSee('Nama minimal 3 karakter.');
});

// TS.SIS.015 / TC.SIS.015.002 — Positive
test('nama tiga karakter diterima karena tepat di batas minimum', function () {
    adminDataSiswa();

    $this->followingRedirects()
        ->post('/admin/siswa', dataSiswaSah(['nama' => 'Abu']))
        ->assertSee('Siswa berhasil ditambahkan.');
});

// TS.SIS.016 / TC.SIS.016.001 — Positive
test('nama seratus karakter diterima karena tepat di batas maksimum', function () {
    adminDataSiswa();

    $this->followingRedirects()
        ->post('/admin/siswa', dataSiswaSah(['nama' => str_repeat('a', 100)]))
        ->assertSee('Siswa berhasil ditambahkan.');
});

// TS.SIS.016 / TC.SIS.016.002 — Negative
test('nama seratus satu karakter ditolak karena melebihi batas maksimum', function () {
    adminDataSiswa();

    $this->from('/admin/siswa/create')
        ->followingRedirects()
        ->post('/admin/siswa', dataSiswaSah(['nama' => str_repeat('a', 101)]))
        ->assertSee('Nama maksimal 100 karakter.');
});

// ── Panjang NISN: tepat 10 angka ───────────────────────────────────────────

// TS.SIS.017 / TC.SIS.017.001 — Negative
test('nisn sembilan angka ditolak karena kurang dari panjang yang ditentukan', function () {
    adminDataSiswa();

    $this->from('/admin/siswa/create')
        ->followingRedirects()
        ->post('/admin/siswa', dataSiswaSah(['nisn' => '123456789']))
        ->assertSee('NISN harus terdiri dari 10 angka.');
});

// TS.SIS.017 / TC.SIS.017.002 — Positive
test('nisn sepuluh angka diterima karena sesuai panjang yang ditentukan', function () {
    adminDataSiswa();

    $this->followingRedirects()
        ->post('/admin/siswa', dataSiswaSah(['nisn' => '1234567890']))
        ->assertSee('Siswa berhasil ditambahkan.');
});

// TS.SIS.018 / TC.SIS.018.001 — Negative
test('nisn sebelas angka ditolak karena melebihi panjang yang ditentukan', function () {
    adminDataSiswa();

    $this->from('/admin/siswa/create')
        ->followingRedirects()
        ->post('/admin/siswa', dataSiswaSah(['nisn' => '12345678901']))
        ->assertSee('NISN harus terdiri dari 10 angka.');
});

// ── Batas panjang NIS: maksimal 15 angka ───────────────────────────────────

// TS.SIS.019 / TC.SIS.019.001 — Positive
test('nis lima belas angka diterima karena tepat di batas maksimum', function () {
    adminDataSiswa();

    $this->followingRedirects()
        ->post('/admin/siswa', dataSiswaSah(['nis' => str_repeat('1', 15)]))
        ->assertSee('Siswa berhasil ditambahkan.');
});

// TS.SIS.019 / TC.SIS.019.002 — Negative
test('nis enam belas angka ditolak karena melebihi batas maksimum', function () {
    adminDataSiswa();

    $this->from('/admin/siswa/create')
        ->followingRedirects()
        ->post('/admin/siswa', dataSiswaSah(['nis' => str_repeat('1', 16)]))
        ->assertSee('NIS maksimal 15 karakter.');
});

// ── Batas panjang nomor HP: 10 sampai 15 karakter ──────────────────────────

// TS.SIS.020 / TC.SIS.020.001 — Negative
test('nomor hp siswa sembilan digit ditolak karena kurang dari batas minimum', function () {
    adminDataSiswa();

    $this->from('/admin/siswa/create')
        ->followingRedirects()
        ->post('/admin/siswa', dataSiswaSah(['telepon' => '081234567']))
        ->assertSee('Nomor HP minimal 10 karakter.');
});

// TS.SIS.020 / TC.SIS.020.002 — Positive
test('nomor hp siswa sepuluh digit diterima karena tepat di batas minimum', function () {
    adminDataSiswa();

    $this->followingRedirects()
        ->post('/admin/siswa', dataSiswaSah(['telepon' => '0812345678']))
        ->assertSee('Siswa berhasil ditambahkan.');
});

// TS.SIS.021 / TC.SIS.021.001 — Positive
test('nomor hp siswa lima belas digit diterima karena tepat di batas maksimum', function () {
    adminDataSiswa();

    $this->followingRedirects()
        ->post('/admin/siswa', dataSiswaSah(['telepon' => '081234567890123']))
        ->assertSee('Siswa berhasil ditambahkan.');
});

// TS.SIS.021 / TC.SIS.021.002 — Negative
test('nomor hp siswa enam belas digit ditolak karena melebihi batas maksimum', function () {
    adminDataSiswa();

    $this->from('/admin/siswa/create')
        ->followingRedirects()
        ->post('/admin/siswa', dataSiswaSah(['telepon' => '0812345678901234']))
        ->assertSee('Nomor HP maksimal 15 karakter.');
});
