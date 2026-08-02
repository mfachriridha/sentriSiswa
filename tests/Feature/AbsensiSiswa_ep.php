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
| Satu-satunya yang dikirim siswa saat absen adalah berkas selfie-nya. Kelas
| sahnya: berkas gambar. Kelas tak sahnya: berkas apa pun yang bukan gambar.
|
| Keadaan jendela absensi dan larangan absen dua kali diuji di AbsensiSiswa_stt.php,
| batas jam dan ukuran berkasnya di _bva.php, dan halaman pemberitahuannya di _uc.php.
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

// TS.ABS.001 / TC.ABS.001.001 — Positive
test('siswa absen tepat waktu dan tercatat hadir', function () {
    Carbon::setTestNow('2026-07-06 06:35:00'); // Senin, di dalam jam absen.
    siswaMasuk();

    $this->followingRedirects()
        ->post('/siswa/absensi', ['status' => 'hadir', 'selfie' => selfieAbsensi()])
        ->assertSee('Absen berhasil: Hadir.');
});

// TS.ABS.006 / TC.ABS.006.001 — Negative
test('absensi ditolak ketika berkas selfienya bukan gambar', function () {
    Carbon::setTestNow('2026-07-06 06:35:00');
    siswaMasuk();

    $this->from('/siswa/absensi')
        ->followingRedirects()
        ->post('/siswa/absensi', [
            'status' => 'hadir',
            'selfie' => UploadedFile::fake()->create('catatan.pdf', 50, 'application/pdf'),
        ])
        ->assertSee('File harus berupa gambar.');
});
