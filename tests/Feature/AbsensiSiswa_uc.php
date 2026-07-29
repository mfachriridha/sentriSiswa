<?php

use App\Models\Pengaturan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Absensi (Siswa) — Use Case Testing
|--------------------------------------------------------------------------
|
| Yang dibaca siswa di halaman absensi, tanpa ia mengisi apa pun: apakah hari ini
| hari absensi, apakah waktunya sudah tiba, apakah sudah lewat, dan apakah ia
| sudah absen. Tiap keadaan punya kalimatnya sendiri supaya siswa tidak menebak-nebak
| kenapa tombolnya tidak bisa ditekan.
|
| Ditambah alamat yang dipanggil halaman diam-diam untuk menyegarkan status hari
| ini tanpa memuat ulang halaman.
|
| Pada pengujian ini jam absen dibuka pukul 06:30 sampai 07:00.
|
*/

beforeEach(function () {
    Storage::fake('public');
    Pengaturan::set('attendance_start_time', '06:30');
    Pengaturan::set('attendance_end_time', '07:00');
});

afterEach(function () {
    Carbon::setTestNow();
});

// TS.ABS.007 / TC.ABS.007.001 — Positive
test('halaman absensi menampilkan status hari ini setelah siswa absen', function () {
    Carbon::setTestNow('2026-07-06 06:35:00');
    siswaMasuk();

    $this->post('/siswa/absensi', ['selfie' => selfieAbsensi()]);

    $this->get('/siswa/absensi')
        ->assertSee('Absensi Hari Ini')
        ->assertSee('Absensi Anda hari ini sudah tercatat. Sampai jumpa besok!');
});

// TS.ABS.008 / TC.ABS.008.001 — Positive
test('halaman absensi memberi tahu siswa bahwa hari ini bukan hari absensi', function () {
    Carbon::setTestNow('2026-07-05 06:35:00'); // Minggu.
    siswaMasuk();

    $this->get('/siswa/absensi')
        ->assertSee('Absensi hanya tersedia pada hari Senin, Selasa, Rabu, Kamis dan Jumat.');
});

// TS.ABS.009 / TC.ABS.009.001 — Positive
test('halaman absensi memberi tahu siswa bahwa waktunya belum tiba', function () {
    Carbon::setTestNow('2026-07-06 06:00:00');
    siswaMasuk();

    $this->get('/siswa/absensi')
        ->assertSee('Belum waktunya absen. Absen dimulai pukul 06:30.');
});

// TS.ABS.010 / TC.ABS.010.001 — Positive
test('halaman absensi memberi tahu siswa bahwa waktunya sudah berakhir', function () {
    Carbon::setTestNow('2026-07-06 07:30:00');
    siswaMasuk();

    $this->get('/siswa/absensi')
        ->assertSee('Waktu absen sudah berakhir pukul 07:00.');
});

// TS.ABS.016 / TC.ABS.016.001 — Positive — penyegar status tanpa memuat ulang halaman
test('status absensi hari ini ikut berubah begitu siswa selesai absen', function () {
    Carbon::setTestNow('2026-07-06 06:35:00');
    siswaMasuk();

    // Halaman menanyakan statusnya secara berkala. Sebelum absen: belum tercatat.
    $this->get('/siswa/absensi/status')
        ->assertSee('belum_absen');

    $this->post('/siswa/absensi', ['selfie' => selfieAbsensi()]);

    $this->get('/siswa/absensi/status')
        ->assertSee('hadir');
});
