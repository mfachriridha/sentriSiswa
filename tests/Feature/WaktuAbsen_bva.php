<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Waktu Absen (Admin) — Boundary Value Analysis
|--------------------------------------------------------------------------
|
| Menguji nilai tepat di batas yang diperbolehkan dan tepat di luarnya:
|   - Toleransi keterlambatan : tidak boleh melebihi lama waktu absen.
|   - Pilihan jam             : 00 sampai 23.
|
| Waktu absen pada pengujian ini 06:00 sampai 07:00, sehingga lamanya
| 60 menit. Itulah batas atas toleransi keterlambatan.
|
*/

// ── Batas toleransi keterlambatan terhadap lama waktu absen (60 menit) ─────

// TS.WKA.009 / TC.WKA.009.001 — Positive
test('toleransi enam puluh menit diterima karena sama dengan lama waktu absen', function () {
    adminWaktuAbsen();

    $this->followingRedirects()
        ->put('/admin/pengaturan/waktu-absen', konfigurasiWaktuAbsenSah([
            'attendance_late_tolerance_minutes' => '60',
        ]))
        ->assertSee('Konfigurasi waktu absen berhasil disimpan.')
        ->assertSee('toleransi terlambat 60 menit');
});

// TS.WKA.009 / TC.WKA.009.002 — Negative
test('toleransi sembilan puluh menit ditolak karena melebihi lama waktu absen', function () {
    adminWaktuAbsen();

    $this->from('/admin/pengaturan/waktu-absen')
        ->followingRedirects()
        ->put('/admin/pengaturan/waktu-absen', konfigurasiWaktuAbsenSah([
            'attendance_late_tolerance_minutes' => '90',
        ]))
        ->assertSee('Toleransi terlambat tidak boleh lebih besar dari durasi absen.');
});

// TS.WKA.010 / TC.WKA.010.001 — Positive
test('toleransi nol menit diterima karena berada di batas bawah', function () {
    adminWaktuAbsen();

    $this->followingRedirects()
        ->put('/admin/pengaturan/waktu-absen', konfigurasiWaktuAbsenSah([
            'attendance_late_tolerance_minutes' => '0',
        ]))
        ->assertSee('Konfigurasi waktu absen berhasil disimpan.');
});

// ── Batas pilihan jam: 00 sampai 23 ────────────────────────────────────────

// TS.WKA.011 / TC.WKA.011.001 — Positive
test('jam nol diterima karena berada di batas bawah pilihan jam', function () {
    adminWaktuAbsen();

    $this->followingRedirects()
        ->put('/admin/pengaturan/waktu-absen', konfigurasiWaktuAbsenSah([
            'attendance_start_hour' => '00',
        ]))
        ->assertSee('Konfigurasi waktu absen berhasil disimpan.');
});

// TS.WKA.012 / TC.WKA.012.001 — Positive
test('jam dua puluh tiga diterima karena berada di batas atas pilihan jam', function () {
    adminWaktuAbsen();

    $this->followingRedirects()
        ->put('/admin/pengaturan/waktu-absen', konfigurasiWaktuAbsenSah([
            'attendance_start_hour' => '22',
            'attendance_end_hour' => '23',
        ]))
        ->assertSee('Konfigurasi waktu absen berhasil disimpan.');
});

// TS.WKA.012 / TC.WKA.012.002 — Negative
test('jam dua puluh empat ditolak karena melebihi batas atas pilihan jam', function () {
    adminWaktuAbsen();

    $this->from('/admin/pengaturan/waktu-absen')
        ->followingRedirects()
        ->put('/admin/pengaturan/waktu-absen', konfigurasiWaktuAbsenSah([
            'attendance_start_hour' => '24',
        ]))
        ->assertSee('Pilihan jam atau menit tidak valid.');
});
