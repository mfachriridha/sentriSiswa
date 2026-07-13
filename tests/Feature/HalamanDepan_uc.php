<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Halaman Depan — Use Case Testing
|--------------------------------------------------------------------------
|
| Yang dilihat orang sebelum masuk: halaman depan sekolah, beserta dua halaman
| kebijakan yang ditautkan dari sana.
|
| Pengujiannya memeriksa apa yang dibaca pengunjung dan ke mana ia bisa pergi,
| bukan bagaimana halamannya dibangun. Pengujian lama memeriksa nama kelas CSS,
| nama berkas gambar, dan berkas stylesheet - itu isi kode, bukan yang dialami
| pengunjung, dan ia akan gagal setiap kali tampilannya dipoles walau halamannya
| baik-baik saja.
|
| Alur alternatifnya: pengguna yang sudah masuk tidak melihat halaman depan lagi,
| melainkan langsung dibawa ke ruang kerjanya.
|
*/

// TS.HDP.001 / TC.HDP.001.001 — Positive
test('pengunjung membaca halaman depan beserta ajakan masuk dan mendaftar', function () {
    $this->get('/')
        ->assertSuccessful()
        ->assertSee('Sentri Siswa untuk SMAN 11 Kabupaten Tangerang')
        ->assertSee('Sistem kedisiplinan dan absensi sekolah')
        ->assertSee('Masuk')
        ->assertSee('Daftar');
});

// TS.HDP.002 / TC.HDP.002.001 — Positive
test('halaman depan menampilkan jumlah siswa, guru, dan kelas sekolah', function () {
    [, $kelas] = kelasBerisiSiswa();                                    // 1 kelas, 1 wali kelas, 1 siswa.
    siswaLainDiKelas($kelas->id, 'Siti Aminah', '1234567892', '10003'); // Siswa kedua.

    $this->get('/')
        ->assertSee('Siswa')
        ->assertSee('Guru')
        ->assertSeeInOrder(['2', 'Siswa']);
});

// TS.HDP.003 / TC.HDP.003.001 — Positive
test('pengunjung membuka kebijakan privasi', function () {
    $this->get('/privacy-policy')
        ->assertSuccessful()
        ->assertSee('Kebijakan Privasi');
});

// TS.HDP.004 / TC.HDP.004.001 — Positive
test('pengunjung membuka ketentuan layanan', function () {
    $this->get('/terms-of-service')
        ->assertSuccessful()
        ->assertSee('Ketentuan Layanan');
});

// TS.HDP.005 / TC.HDP.005.001 — Positive — alur alternatif: sudah masuk
test('pengguna yang sudah masuk langsung dibawa ke ruang kerjanya, bukan ke halaman depan', function () {
    siswaMasuk();

    $this->followingRedirects()
        ->get('/')
        ->assertSee('Dashboard Siswa')
        ->assertDontSee('Sistem kedisiplinan dan absensi sekolah');
});
