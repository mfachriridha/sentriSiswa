<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Waktu Absen (Admin) — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Pengujian black box: admin masuk lewat halaman masuk, lalu mengatur jam
| absensi seperti pengguna biasa. Hasilnya diperiksa dari apa yang muncul di
| layar, bukan dari basis data.
|
| Admin mengatur jam mulai, jam selesai, toleransi keterlambatan, dan hari
| aktif absensi. Toleransi keterlambatan tidak boleh melebihi lama waktu
| absensi, dan jam selesai harus setelah jam mulai.
|
*/

function adminWaktuAbsen(): Pengguna
{
    $admin = Pengguna::factory()->admin()->create([
        'email' => 'admin.waktu@sentrisiswa.test',
        'status' => 'registered',
    ]);

    masukSebagai($admin);

    return $admin;
}

/** Konfigurasi yang sah, agar setiap pengujian hanya mengubah satu bagian saja. */
function konfigurasiWaktuAbsenSah(array $ubahan = []): array
{
    return array_merge([
        'attendance_start_hour' => '06',
        'attendance_start_minute' => '00',
        'attendance_end_hour' => '07',
        'attendance_end_minute' => '00',
        'attendance_late_tolerance_minutes' => '30',
        'attendance_active_days' => [1, 2, 3, 4, 5],
    ], $ubahan);
}

// TS.WKA.001 / TC.WKA.001.001 — Positive
test('admin berhasil menyimpan konfigurasi waktu absen yang sah', function () {
    adminWaktuAbsen();

    $this->get('/admin/pengaturan/waktu-absen')->assertSee('Waktu Absen');

    $this->followingRedirects()
        ->put('/admin/pengaturan/waktu-absen', konfigurasiWaktuAbsenSah())
        ->assertSee('Konfigurasi waktu absen berhasil disimpan.')
        ->assertSee('Jam mulai 06:00, selesai 07:00, toleransi terlambat 30 menit.');
});

// TS.WKA.002 / TC.WKA.002.001 — Positive
test('admin berhasil mengatur hari aktif absensi termasuk hari sabtu', function () {
    adminWaktuAbsen();

    $this->followingRedirects()
        ->put('/admin/pengaturan/waktu-absen', konfigurasiWaktuAbsenSah([
            'attendance_active_days' => [1, 2, 3, 4, 5, 6],
        ]))
        ->assertSee('Konfigurasi waktu absen berhasil disimpan.')
        ->assertSee('Hari aktif: Senin, Selasa, Rabu, Kamis, Jumat dan Sabtu.');
});

// TS.WKA.003 / TC.WKA.003.001 — Positive
test('admin berhasil menyimpan tanpa toleransi keterlambatan', function () {
    adminWaktuAbsen();

    $this->followingRedirects()
        ->put('/admin/pengaturan/waktu-absen', konfigurasiWaktuAbsenSah([
            'attendance_late_tolerance_minutes' => '0',
        ]))
        ->assertSee('Konfigurasi waktu absen berhasil disimpan.')
        ->assertSee('toleransi terlambat 0 menit');
});

// TS.WKA.004 / TC.WKA.004.001 — Negative
test('admin gagal menyimpan karena jam selesai sama dengan jam mulai', function () {
    adminWaktuAbsen();

    $this->from('/admin/pengaturan/waktu-absen')
        ->followingRedirects()
        ->put('/admin/pengaturan/waktu-absen', konfigurasiWaktuAbsenSah([
            'attendance_end_hour' => '06',
            'attendance_end_minute' => '00',
        ]))
        ->assertSee('Jam selesai harus setelah jam mulai.')
        ->assertDontSee('Konfigurasi waktu absen berhasil disimpan.');
});

// TS.WKA.005 / TC.WKA.005.001 — Negative
test('admin gagal menyimpan karena jam selesai lebih awal dari jam mulai', function () {
    adminWaktuAbsen();

    $this->from('/admin/pengaturan/waktu-absen')
        ->followingRedirects()
        ->put('/admin/pengaturan/waktu-absen', konfigurasiWaktuAbsenSah([
            'attendance_start_hour' => '08',
            'attendance_end_hour' => '07',
        ]))
        ->assertSee('Jam selesai harus setelah jam mulai.');
});

// TS.WKA.006 / TC.WKA.006.001 — Negative
test('admin gagal menyimpan karena toleransi melebihi lama waktu absen', function () {
    adminWaktuAbsen();

    // Waktu absen 06:00 sampai 07:00 hanya selama 60 menit.
    $this->from('/admin/pengaturan/waktu-absen')
        ->followingRedirects()
        ->put('/admin/pengaturan/waktu-absen', konfigurasiWaktuAbsenSah([
            'attendance_late_tolerance_minutes' => '90',
        ]))
        ->assertSee('Toleransi terlambat tidak boleh lebih besar dari durasi absen.');
});

// TS.WKA.007 / TC.WKA.007.001 — Negative
test('admin gagal menyimpan karena tidak memilih satu pun hari aktif', function () {
    adminWaktuAbsen();

    $konfigurasi = konfigurasiWaktuAbsenSah();
    unset($konfigurasi['attendance_active_days']);

    $this->from('/admin/pengaturan/waktu-absen')
        ->followingRedirects()
        ->put('/admin/pengaturan/waktu-absen', $konfigurasi)
        ->assertSee('Pilih minimal satu hari aktif absensi.');
});

// TS.WKA.008 / TC.WKA.008.001 — Negative
test('admin gagal menyimpan karena jam yang dipilih di luar rentang yang tersedia', function () {
    adminWaktuAbsen();

    $this->from('/admin/pengaturan/waktu-absen')
        ->followingRedirects()
        ->put('/admin/pengaturan/waktu-absen', konfigurasiWaktuAbsenSah([
            'attendance_start_hour' => '24',
        ]))
        ->assertSee('Pilihan jam atau menit tidak valid.');
});
