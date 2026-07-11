<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Data Kelas (Admin) — Boundary Value Analysis
|--------------------------------------------------------------------------
|
| Menguji nilai tepat di batas yang diperbolehkan dan tepat di luarnya:
|   - Panjang nama kelas : maksimal 20 karakter.
|   - Pilihan tingkat    : hanya 10, 11, dan 12.
|
*/

// ── Batas panjang nama kelas: maksimal 20 karakter ─────────────────────────

// TS.KEL.012 / TC.KEL.012.001 — Positive
test('nama kelas dua puluh karakter diterima karena tepat di batas maksimum', function () {
    adminDataKelas();

    $this->followingRedirects()
        ->post('/admin/kelas', [
            'tingkat' => '10',
            'nama' => str_repeat('A', 20),
        ])
        ->assertSee('Kelas berhasil ditambahkan.');
});

// TS.KEL.012 / TC.KEL.012.002 — Negative
test('nama kelas dua puluh satu karakter ditolak karena melebihi batas maksimum', function () {
    adminDataKelas();

    $this->from('/admin/kelas/create')
        ->followingRedirects()
        ->post('/admin/kelas', [
            'tingkat' => '10',
            'nama' => str_repeat('A', 21),
        ])
        ->assertSee('Nama maksimal 20 karakter.');
});

// ── Batas pilihan tingkat: hanya 10, 11, dan 12 ────────────────────────────

// TS.KEL.013 / TC.KEL.013.001 — Negative
test('tingkat sembilan ditolak karena berada di bawah pilihan yang tersedia', function () {
    adminDataKelas();

    $this->from('/admin/kelas/create')
        ->followingRedirects()
        ->post('/admin/kelas', [
            'tingkat' => '9',
            'nama' => 'IPA 1',
        ])
        ->assertSee('Tingkat yang dipilih tidak valid.');
});

// TS.KEL.013 / TC.KEL.013.002 — Positive
test('tingkat sepuluh diterima karena berada di batas bawah pilihan', function () {
    adminDataKelas();

    $this->followingRedirects()
        ->post('/admin/kelas', [
            'tingkat' => '10',
            'nama' => 'IPA 1',
        ])
        ->assertSee('Kelas berhasil ditambahkan.');
});

// TS.KEL.014 / TC.KEL.014.001 — Positive
test('tingkat dua belas diterima karena berada di batas atas pilihan', function () {
    adminDataKelas();

    $this->followingRedirects()
        ->post('/admin/kelas', [
            'tingkat' => '12',
            'nama' => 'IPA 1',
        ])
        ->assertSee('Kelas berhasil ditambahkan.');
});

// TS.KEL.014 / TC.KEL.014.002 — Negative
test('tingkat tiga belas ditolak karena berada di atas pilihan yang tersedia', function () {
    adminDataKelas();

    $this->from('/admin/kelas/create')
        ->followingRedirects()
        ->post('/admin/kelas', [
            'tingkat' => '13',
            'nama' => 'IPA 1',
        ])
        ->assertSee('Tingkat yang dipilih tidak valid.');
});
