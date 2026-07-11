<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Profil Admin — Boundary Value Analysis
|--------------------------------------------------------------------------
|
| Menguji nilai tepat di batas yang diperbolehkan dan tepat di luarnya:
|   - Panjang nomor WhatsApp : 10 sampai 20 karakter.
|   - Ukuran foto profil     : maksimal 2 MB.
|
*/

// ── Batas panjang nomor WhatsApp: 10 sampai 20 karakter ────────────────────

// TS.PAD.010 / TC.PAD.010.001 — Negative
test('nomor whatsapp sembilan digit ditolak karena kurang dari batas minimum', function () {
    adminProfil();

    $this->from('/admin/profil/edit')
        ->followingRedirects()
        ->put('/admin/profil', [
            'nama' => 'Admin Sekolah',
            'email' => 'admin.profil@sentrisiswa.test',
            'whatsapp_number' => '081234567',
        ])
        ->assertSee('Nomor WhatsApp minimal 10 digit.');
});

// TS.PAD.010 / TC.PAD.010.002 — Positive
test('nomor whatsapp sepuluh digit diterima karena tepat di batas minimum', function () {
    adminProfil();

    $this->followingRedirects()
        ->put('/admin/profil', [
            'nama' => 'Admin Sekolah',
            'email' => 'admin.profil@sentrisiswa.test',
            'whatsapp_number' => '0812345678',
        ])
        ->assertSee('Profil admin berhasil diperbarui.');
});

// TS.PAD.011 / TC.PAD.011.001 — Positive
test('nomor whatsapp dua puluh digit diterima karena tepat di batas maksimum', function () {
    adminProfil();

    $this->followingRedirects()
        ->put('/admin/profil', [
            'nama' => 'Admin Sekolah',
            'email' => 'admin.profil@sentrisiswa.test',
            'whatsapp_number' => str_repeat('1', 20),
        ])
        ->assertSee('Profil admin berhasil diperbarui.');
});

// TS.PAD.011 / TC.PAD.011.002 — Negative
test('nomor whatsapp dua puluh satu digit ditolak karena melebihi batas maksimum', function () {
    adminProfil();

    $this->from('/admin/profil/edit')
        ->followingRedirects()
        ->put('/admin/profil', [
            'nama' => 'Admin Sekolah',
            'email' => 'admin.profil@sentrisiswa.test',
            'whatsapp_number' => str_repeat('1', 21),
        ])
        ->assertSee('Nomor WhatsApp maksimal 20 karakter.');
});

// ── Batas ukuran foto profil: maksimal 2 MB ────────────────────────────────

// TS.PAD.012 / TC.PAD.012.001 — Positive
test('foto profil berukuran tepat dua megabita diterima karena berada di batas maksimum', function () {
    Storage::fake('public');
    adminProfil();

    $this->followingRedirects()
        ->put('/admin/profil', [
            'nama' => 'Admin Sekolah',
            'email' => 'admin.profil@sentrisiswa.test',
            'photo' => UploadedFile::fake()->image('foto.jpg')->size(2048),
        ])
        ->assertSee('Profil admin berhasil diperbarui.');
});

// TS.PAD.012 / TC.PAD.012.002 — Negative
test('foto profil yang melebihi dua megabita ditolak karena di atas batas maksimum', function () {
    Storage::fake('public');
    adminProfil();

    $this->from('/admin/profil/edit')
        ->followingRedirects()
        ->put('/admin/profil', [
            'nama' => 'Admin Sekolah',
            'email' => 'admin.profil@sentrisiswa.test',
            'photo' => UploadedFile::fake()->image('foto.jpg')->size(2049),
        ])
        ->assertSee('Ukuran foto maksimal 2 MB.');
});
