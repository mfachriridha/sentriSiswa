<?php

use App\Models\Pengaturan;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur WhatsApp API (Admin) — Boundary Value Analysis
|--------------------------------------------------------------------------
|
| Menguji nilai tepat di batas yang diperbolehkan dan tepat di luarnya:
|   - Panjang token layanan : maksimal 255 karakter.
|   - Panjang pesan uji     : maksimal 500 karakter.
|
*/

// ── Batas panjang token layanan: maksimal 255 karakter ────────────────────

// TS.WAP.009 / TC.WAP.009.001 — Positive
test('token dua ratus lima puluh lima karakter diterima karena tepat di batas maksimum', function () {
    adminWhatsapp();
    layananWhatsappBerhasil();

    $this->followingRedirects()
        ->put('/admin/pengaturan/whatsapp', [
            'fonnte_token' => str_repeat('a', 255),
        ])
        ->assertSee('Token Fonnte berhasil disimpan dan sudah diverifikasi.');
});

// TS.WAP.009 / TC.WAP.009.002 — Negative
test('token dua ratus lima puluh enam karakter ditolak karena melebihi batas maksimum', function () {
    adminWhatsapp();

    $this->from('/admin/pengaturan/whatsapp')
        ->followingRedirects()
        ->put('/admin/pengaturan/whatsapp', [
            'fonnte_token' => str_repeat('a', 256),
        ])
        ->assertSee('Token Fonnte tidak boleh lebih dari 255 karakter.');
});

// ── Batas panjang pesan uji: maksimal 500 karakter ────────────────────────

// TS.WAP.010 / TC.WAP.010.001 — Positive
test('pesan uji lima ratus karakter diterima karena tepat di batas maksimum', function () {
    adminWhatsapp();
    Pengaturan::set('fonnte_token', 'token-yang-sah');
    layananWhatsappBerhasil();

    $this->postJson('/admin/pengaturan/whatsapp/test', [
        'phone' => '081234567890',
        'message' => str_repeat('a', 500),
    ])->assertSee('true');
});

// TS.WAP.010 / TC.WAP.010.002 — Negative
test('pesan uji lima ratus satu karakter ditolak karena melebihi batas maksimum', function () {
    adminWhatsapp();
    Pengaturan::set('fonnte_token', 'token-yang-sah');

    // Tombol kirim uji bekerja tanpa memuat ulang halaman, sehingga jawabannya
    // dikirim balik untuk ditampilkan langsung di layar. Yang diperiksa di sini
    // adalah kalimat yang sampai ke pengguna itu.
    $jawaban = $this->postJson('/admin/pengaturan/whatsapp/test', [
        'phone' => '081234567890',
        'message' => str_repeat('a', 501),
    ]);

    expect($jawaban->getContent())->toContain('Pesan maksimal 500 karakter.');
});
