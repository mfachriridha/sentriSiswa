<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Waktu Absen (Admin) — Boundary Value Analysis
|--------------------------------------------------------------------------
|
| Menguji nilai tepat di batas yang diperbolehkan dan tepat di luarnya:
|   - Pilihan jam : 00 sampai 23.
|
*/

// ── Batas pilihan jam: 00 sampai 23 ────────────────────────────────────────

// TS.WKA.007 / TC.WKA.007.001 — Positive
test('jam nol diterima karena berada di batas bawah pilihan jam', function () {
    adminWaktuAbsen();

    $this->followingRedirects()
        ->put('/admin/pengaturan/waktu-absen', konfigurasiWaktuAbsenSah([
            'attendance_start_hour' => '00',
        ]))
        ->assertSee('Konfigurasi waktu absen berhasil disimpan.');
});

// TS.WKA.008 / TC.WKA.008.001 — Positive
test('jam dua puluh tiga diterima karena berada di batas atas pilihan jam', function () {
    adminWaktuAbsen();

    $this->followingRedirects()
        ->put('/admin/pengaturan/waktu-absen', konfigurasiWaktuAbsenSah([
            'attendance_start_hour' => '22',
            'attendance_end_hour' => '23',
        ]))
        ->assertSee('Konfigurasi waktu absen berhasil disimpan.');
});

// TS.WKA.008 / TC.WKA.008.002 — Negative
test('jam dua puluh empat ditolak karena melebihi batas atas pilihan jam', function () {
    adminWaktuAbsen();

    $this->from('/admin/pengaturan/waktu-absen')
        ->followingRedirects()
        ->put('/admin/pengaturan/waktu-absen', konfigurasiWaktuAbsenSah([
            'attendance_start_hour' => '24',
        ]))
        ->assertSee('Pilihan jam atau menit tidak valid.');
});
