<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Tata Tertib (Kesiswaan) — Boundary Value Analysis
|--------------------------------------------------------------------------
|
| Dua batas yang diuji:
|
| 1. Ukuran berkas PDF paling besar 10 MB (10240 KB). Diuji tepat di bawah,
|    tepat pada, dan tepat di atas batas itu.
| 2. Judul tata tertib paling panjang 200 karakter.
|
*/

beforeEach(function () {
    Storage::fake('public');
});

// TS.TTK.011 / TC.TTK.011.001 — Positive — tepat di bawah batas
test('berkas berukuran tepat di bawah 10 MB diterima', function () {
    kesiswaanMasuk();

    $this->followingRedirects()
        ->post('/kesiswaan/tata-tertib', [
            'judul' => 'Tata Tertib Sekolah 2026',
            'file_pdf' => berkasTataTertib(10239),
        ])
        ->assertSee('Tata tertib berhasil diunggah.');
});

// TS.TTK.011 / TC.TTK.011.002 — Positive — tepat pada batas
test('berkas berukuran tepat 10 MB diterima', function () {
    kesiswaanMasuk();

    $this->followingRedirects()
        ->post('/kesiswaan/tata-tertib', [
            'judul' => 'Tata Tertib Sekolah 2026',
            'file_pdf' => berkasTataTertib(10240),
        ])
        ->assertSee('Tata tertib berhasil diunggah.');
});

// TS.TTK.011 / TC.TTK.011.003 — Negative — tepat di atas batas
test('berkas berukuran lebih dari 10 MB ditolak', function () {
    kesiswaanMasuk();

    $this->from('/kesiswaan/tata-tertib')
        ->followingRedirects()
        ->post('/kesiswaan/tata-tertib', [
            'judul' => 'Tata Tertib Sekolah 2026',
            'file_pdf' => berkasTataTertib(10241),
        ])
        ->assertSee('Ukuran PDF maksimal 10 MB.');
});

// TS.TTK.012 / TC.TTK.012.001 — Positive — tepat pada batas
test('judul sepanjang 200 karakter diterima', function () {
    kesiswaanMasuk();

    $this->followingRedirects()
        ->post('/kesiswaan/tata-tertib', [
            'judul' => str_repeat('a', 200),
            'file_pdf' => berkasTataTertib(),
        ])
        ->assertSee('Tata tertib berhasil diunggah.');
});

// TS.TTK.012 / TC.TTK.012.002 — Negative — sekarakter di atas batas
test('judul sepanjang 201 karakter ditolak', function () {
    kesiswaanMasuk();

    $this->from('/kesiswaan/tata-tertib')
        ->followingRedirects()
        ->post('/kesiswaan/tata-tertib', [
            'judul' => str_repeat('a', 201),
            'file_pdf' => berkasTataTertib(),
        ])
        ->assertSee('Judul tata tertib maksimal 200 karakter.');
});
