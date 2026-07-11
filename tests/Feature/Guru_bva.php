<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Data Guru (Admin) — Boundary Value Analysis
|--------------------------------------------------------------------------
|
| Menguji nilai tepat di batas yang diperbolehkan dan tepat di luarnya:
|   - Panjang nama    : 3 sampai 100 karakter.
|   - Panjang NIP     : maksimal 30 karakter.
|   - Panjang nomor HP: 10 sampai 15 karakter.
|
*/

/** Data guru yang sah, agar setiap pengujian hanya mengubah satu kolom saja. */
function dataGuruSah(array $ubahan = []): array
{
    return array_merge([
        'nama' => 'Raka Pradipta',
        'nip' => '198501012020121001',
        'peran' => 'wali_kelas',
    ], $ubahan);
}

// ── Batas panjang nama: 3 sampai 100 karakter ──────────────────────────────

// TS.GUR.013 / TC.GUR.013.001 — Negative
test('nama guru dua karakter ditolak karena kurang dari batas minimum', function () {
    adminDataGuru();

    $this->from('/admin/guru/create')
        ->followingRedirects()
        ->post('/admin/guru', dataGuruSah(['nama' => 'Ab']))
        ->assertSee('Nama minimal 3 karakter.');
});

// TS.GUR.013 / TC.GUR.013.002 — Positive
test('nama guru tiga karakter diterima karena tepat di batas minimum', function () {
    adminDataGuru();

    $this->followingRedirects()
        ->post('/admin/guru', dataGuruSah(['nama' => 'Abu']))
        ->assertSee('Guru berhasil ditambahkan.');
});

// TS.GUR.014 / TC.GUR.014.001 — Positive
test('nama guru seratus karakter diterima karena tepat di batas maksimum', function () {
    adminDataGuru();

    $this->followingRedirects()
        ->post('/admin/guru', dataGuruSah(['nama' => str_repeat('a', 100)]))
        ->assertSee('Guru berhasil ditambahkan.');
});

// TS.GUR.014 / TC.GUR.014.002 — Negative
test('nama guru seratus satu karakter ditolak karena melebihi batas maksimum', function () {
    adminDataGuru();

    $this->from('/admin/guru/create')
        ->followingRedirects()
        ->post('/admin/guru', dataGuruSah(['nama' => str_repeat('a', 101)]))
        ->assertSee('Nama maksimal 100 karakter.');
});

// ── Batas panjang NIP: maksimal 30 karakter ────────────────────────────────

// TS.GUR.015 / TC.GUR.015.001 — Positive
test('nip tiga puluh karakter diterima karena tepat di batas maksimum', function () {
    adminDataGuru();

    $this->followingRedirects()
        ->post('/admin/guru', dataGuruSah(['nip' => str_repeat('1', 30)]))
        ->assertSee('Guru berhasil ditambahkan.');
});

// TS.GUR.015 / TC.GUR.015.002 — Negative
test('nip tiga puluh satu karakter ditolak karena melebihi batas maksimum', function () {
    adminDataGuru();

    $this->from('/admin/guru/create')
        ->followingRedirects()
        ->post('/admin/guru', dataGuruSah(['nip' => str_repeat('1', 31)]))
        ->assertSee('NIP maksimal 30 karakter.');
});

// ── Batas panjang nomor HP: 10 sampai 15 karakter ──────────────────────────

// TS.GUR.016 / TC.GUR.016.001 — Negative
test('nomor hp guru sembilan digit ditolak karena kurang dari batas minimum', function () {
    adminDataGuru();

    $this->from('/admin/guru/create')
        ->followingRedirects()
        ->post('/admin/guru', dataGuruSah(['telepon' => '081234567']))
        ->assertSee('Nomor HP minimal 10 karakter.');
});

// TS.GUR.016 / TC.GUR.016.002 — Positive
test('nomor hp guru sepuluh digit diterima karena tepat di batas minimum', function () {
    adminDataGuru();

    $this->followingRedirects()
        ->post('/admin/guru', dataGuruSah(['telepon' => '0812345678']))
        ->assertSee('Guru berhasil ditambahkan.');
});

// TS.GUR.017 / TC.GUR.017.001 — Positive
test('nomor hp guru lima belas digit diterima karena tepat di batas maksimum', function () {
    adminDataGuru();

    $this->followingRedirects()
        ->post('/admin/guru', dataGuruSah(['telepon' => '081234567890123']))
        ->assertSee('Guru berhasil ditambahkan.');
});

// TS.GUR.017 / TC.GUR.017.002 — Negative
test('nomor hp guru enam belas digit ditolak karena melebihi batas maksimum', function () {
    adminDataGuru();

    $this->from('/admin/guru/create')
        ->followingRedirects()
        ->post('/admin/guru', dataGuruSah(['telepon' => '0812345678901234']))
        ->assertSee('Nomor HP maksimal 15 karakter.');
});
