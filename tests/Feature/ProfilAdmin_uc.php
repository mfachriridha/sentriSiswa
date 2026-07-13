<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Profil Admin — Use Case Testing
|--------------------------------------------------------------------------
|
| Pengujian black box: admin masuk lewat halaman masuk, lalu mengelola
| profilnya sendiri. Hasilnya diperiksa dari apa yang muncul di layar, bukan
| dari basis data.
|
| Admin boleh mengganti email dan nomor WhatsApp secara langsung. Penggantian
| kata sandi punya alurnya sendiri: sistem mengirim kode OTP ke email admin
| lebih dulu.
| Alur pemakaian tanpa isian: admin membuka halaman profilnya sendiri.
|
*/

// TS.PAD.001 / TC.PAD.001.001 — Positive
test('admin melihat halaman profilnya sendiri', function () {
    adminProfil();

    $this->get('/admin/profil')
        ->assertSee('Admin Sekolah')
        ->assertSee('admin.profil@sentrisiswa.test');
});
