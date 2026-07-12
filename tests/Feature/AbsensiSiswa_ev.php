<?php

use App\Models\Pengaturan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Absensi (Siswa) — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Pengujian black box: siswa masuk lewat halaman masuk, lalu melakukan absensi
| harian. Hasilnya diperiksa dari apa yang muncul di layar, bukan dari basis data.
|
| Absensi hanya bisa dilakukan pada hari absensi dan di dalam jam absen yang
| ditentukan sekolah. Siswa mengambil selfie sebagai bukti kehadiran. Siswa yang
| absen sebelum batas keterlambatan tercatat Hadir, sesudahnya tercatat Terlambat.
| Satu siswa hanya bisa absen sekali sehari.
|
| Pada pengujian ini jam absen dibuka pukul 06:30 sampai 07:00, dengan batas
| keterlambatan pukul 06:45.
|
*/

beforeEach(function () {
    Storage::fake('public');
    Pengaturan::set('attendance_start_time', '06:30');
    Pengaturan::set('attendance_end_time', '07:00');
    Pengaturan::set('attendance_late_tolerance_minutes', 15); // Terlambat setelah 06:45.
});

afterEach(function () {
    Carbon::setTestNow();
});

// TS.ABS.001 / TC.ABS.001.001 — Positive
test('siswa absen tepat waktu dan tercatat hadir', function () {
    Carbon::setTestNow('2026-07-06 06:35:00'); // Senin, di dalam jam absen, sebelum batas terlambat.
    siswaMasuk();

    $this->followingRedirects()
        ->post('/siswa/absensi', ['selfie' => selfieAbsensi()])
        ->assertSee('Absen berhasil: Hadir.');
});

// TS.ABS.002 / TC.ABS.002.001 — Positive
test('siswa absen setelah batas keterlambatan dan tercatat terlambat', function () {
    Carbon::setTestNow('2026-07-06 06:50:00'); // Masih di dalam jam absen, tetapi lewat batas terlambat.
    siswaMasuk();

    $this->followingRedirects()
        ->post('/siswa/absensi', ['selfie' => selfieAbsensi()])
        ->assertSee('Absen tercatat: Terlambat.');
});

// TS.ABS.003 / TC.ABS.003.001 — Negative
test('siswa gagal absen sebelum jam absen dibuka', function () {
    Carbon::setTestNow('2026-07-06 06:00:00');
    siswaMasuk();

    $this->followingRedirects()
        ->post('/siswa/absensi', ['selfie' => selfieAbsensi()])
        ->assertSee('Waktu absen sudah lewat atau belum dimulai.');
});

// TS.ABS.004 / TC.ABS.004.001 — Negative
test('siswa gagal absen setelah jam absen berakhir', function () {
    Carbon::setTestNow('2026-07-06 07:30:00');
    siswaMasuk();

    $this->followingRedirects()
        ->post('/siswa/absensi', ['selfie' => selfieAbsensi()])
        ->assertSee('Waktu absen sudah lewat atau belum dimulai.');
});

// TS.ABS.005 / TC.ABS.005.001 — Negative
test('siswa gagal absen di hari yang bukan hari absensi', function () {
    Carbon::setTestNow('2026-07-05 06:35:00'); // Minggu.
    siswaMasuk();

    $this->followingRedirects()
        ->post('/siswa/absensi', ['selfie' => selfieAbsensi()])
        ->assertSee('Absensi hanya tersedia pada hari '.Pengaturan::labelHariAbsen().'.');
});

// TS.ABS.006 / TC.ABS.006.001 — Negative
test('siswa tidak bisa absen dua kali dalam sehari', function () {
    Carbon::setTestNow('2026-07-06 06:35:00');
    siswaMasuk();

    $this->post('/siswa/absensi', ['selfie' => selfieAbsensi()]);

    $this->followingRedirects()
        ->post('/siswa/absensi', ['selfie' => selfieAbsensi()])
        ->assertSee('Anda sudah absen hari ini.');
});

// TS.ABS.007 / TC.ABS.007.001 — Negative
test('absensi ditolak ketika berkas selfienya bukan gambar', function () {
    Carbon::setTestNow('2026-07-06 06:35:00');
    siswaMasuk();

    $this->from('/siswa/absensi')
        ->followingRedirects()
        ->post('/siswa/absensi', [
            'selfie' => UploadedFile::fake()->create('catatan.pdf', 50, 'application/pdf'),
        ])
        ->assertSee('File harus berupa gambar.');
});

// TS.ABS.008 / TC.ABS.008.001 — Positive
test('halaman absensi menampilkan status hari ini setelah siswa absen', function () {
    Carbon::setTestNow('2026-07-06 06:35:00');
    siswaMasuk();

    $this->post('/siswa/absensi', ['selfie' => selfieAbsensi()]);

    $this->get('/siswa/absensi')
        ->assertSee('Absensi Hari Ini')
        ->assertSee('Absensi Anda hari ini sudah tercatat. Sampai jumpa besok!');
});

// TS.ABS.009 / TC.ABS.009.001 — Positive
test('halaman absensi memberi tahu siswa bahwa hari ini bukan hari absensi', function () {
    Carbon::setTestNow('2026-07-05 06:35:00'); // Minggu.
    siswaMasuk();

    $this->get('/siswa/absensi')
        ->assertSee('Absensi hanya tersedia pada hari Senin sampai Jumat.');
});

// TS.ABS.010 / TC.ABS.010.001 — Positive
test('halaman absensi memberi tahu siswa bahwa waktunya belum tiba', function () {
    Carbon::setTestNow('2026-07-06 06:00:00');
    siswaMasuk();

    $this->get('/siswa/absensi')
        ->assertSee('Belum waktunya absen. Absen dimulai pukul 06:30.');
});

// TS.ABS.011 / TC.ABS.011.001 — Positive
test('halaman absensi memberi tahu siswa bahwa waktunya sudah berakhir', function () {
    Carbon::setTestNow('2026-07-06 07:30:00');
    siswaMasuk();

    $this->get('/siswa/absensi')
        ->assertSee('Waktu absen sudah berakhir pukul 07:00.');
});
