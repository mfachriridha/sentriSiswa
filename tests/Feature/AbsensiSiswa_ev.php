<?php

use App\Models\Absensi;
use App\Models\Pengaturan;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

afterEach(function () {
    Carbon::setTestNow();
});

function absensiSiswaStudent(): Pengguna
{
    $student = Pengguna::factory()->student()->create(['status' => 'registered']);
    ProfilSiswa::factory()->create([
        'pengguna_id' => $student->id,
        'nis' => fake()->unique()->numerify('#####'),
    ]);

    return $student;
}

function absensiSiswaSquareGeofence(): void
{
    Pengaturan::set('attendance_geofence_data', json_encode([
        'coordinates' => [
            ['lat' => 0.0000, 'lng' => 0.0000],
            ['lat' => 0.0000, 'lng' => 0.0020],
            ['lat' => 0.0020, 'lng' => 0.0020],
            ['lat' => 0.0020, 'lng' => 0.0000],
        ],
    ]));
    Pengaturan::set('attendance_tolerance_meters', '10');
}

// TS.ABS.001 / TC.ABS.001.001 — check-in sebelum jendela waktu mulai ditolak (negative)
test('student cannot check in before the attendance window starts', function () {
    Carbon::setTestNow('2026-06-08 06:00:00');
    Storage::fake('public');
    $student = absensiSiswaStudent();

    $this->actingAs($student)->post(route('siswa.absensi.store'), [
        'selfie' => UploadedFile::fake()->image('selfie.jpg')->size(100),
    ])->assertRedirect(route('siswa.absensi'))
        ->assertSessionHas('error', 'Waktu absen sudah lewat atau belum dimulai.');

    expect(Absensi::first()->status)->toBe('belum_absen');
});

// TS.ABS.002 / TC.ABS.002.001 — check-in setelah jendela waktu berakhir ditolak (negative)
test('student cannot check in after the attendance window ends', function () {
    Carbon::setTestNow('2026-06-08 08:00:00');
    Storage::fake('public');
    $student = absensiSiswaStudent();

    $this->actingAs($student)->post(route('siswa.absensi.store'), [
        'selfie' => UploadedFile::fake()->image('selfie.jpg')->size(100),
    ])->assertRedirect(route('siswa.absensi'))
        ->assertSessionHas('error', 'Waktu absen sudah lewat atau belum dimulai.');

    expect(Absensi::first()->status)->toBe('belum_absen');
});

// TS.ABS.003 / TC.ABS.003.001 — check-in yang telat dari batas toleransi hasilnya status terlambat (positive)
test('student checking in past the late tolerance is marked terlambat', function () {
    Pengaturan::set('attendance_start_time', '06:30');
    Pengaturan::set('attendance_end_time', '07:30');
    Pengaturan::set('attendance_late_time', '07:00');
    Carbon::setTestNow('2026-06-08 07:15:00');
    Storage::fake('public');
    $student = absensiSiswaStudent();

    $this->actingAs($student)->post(route('siswa.absensi.store'), [
        'selfie' => UploadedFile::fake()->image('selfie.jpg')->size(100),
    ])->assertRedirect(route('siswa.absensi'))
        ->assertSessionHas('success', 'Absen tercatat: Terlambat.');

    expect(Absensi::firstOrFail()->status)->toBe('terlambat');
});

// TS.ABS.004 / TC.ABS.004.001 — selfie tidak diisi ditolak (negative)
test('student cannot check in without a selfie', function () {
    Carbon::setTestNow('2026-06-08 06:45:00');
    Storage::fake('public');
    $student = absensiSiswaStudent();

    $this->actingAs($student)->post(route('siswa.absensi.store'), [])
        ->assertSessionHasErrors('selfie');

    expect(Absensi::first()->status)->toBe('belum_absen');
});

// TS.ABS.005 / TC.ABS.005.001 — selfie bukan format gambar yang diizinkan ditolak (negative)
test('student cannot check in with a disallowed selfie file type', function () {
    Carbon::setTestNow('2026-06-08 06:45:00');
    Storage::fake('public');
    $student = absensiSiswaStudent();

    $this->actingAs($student)->post(route('siswa.absensi.store'), [
        'selfie' => UploadedFile::fake()->create('selfie.pdf', 100, 'application/pdf'),
    ])->assertSessionHasErrors('selfie');

    expect(Absensi::first()->status)->toBe('belum_absen');
});

// TS.ABS.006 / TC.ABS.006.001 — profil siswa tidak ditemukan ditolak (negative, edge case)
test('student without a linked profil siswa cannot check in', function () {
    Carbon::setTestNow('2026-06-08 06:45:00');
    Storage::fake('public');
    $student = Pengguna::factory()->student()->create(['status' => 'registered']);

    $this->actingAs($student)->post(route('siswa.absensi.store'), [
        'selfie' => UploadedFile::fake()->image('selfie.jpg')->size(100),
    ])->assertRedirect(route('siswa.absensi'))
        ->assertSessionHas('error', 'Profil siswa tidak ditemukan.');

    expect(Absensi::count())->toBe(0);
});

// TS.ABS.007 / TC.ABS.007.001 — cek lokasi GPS di dalam area geofence diperbolehkan (positive)
test('checking location inside the geofence is allowed', function () {
    Carbon::setTestNow('2026-06-08 06:45:00');
    absensiSiswaSquareGeofence();
    $student = absensiSiswaStudent();

    $this->actingAs($student)->postJson(route('siswa.absensi.cek-lokasi'), [
        'latitude' => 0.0010,
        'longitude' => 0.0010,
        'accuracy' => 10,
    ])->assertSuccessful()
        ->assertJson(['allowed' => true, 'status' => 'inside']);
});

// TS.ABS.008 / TC.ABS.008.001 — cek lokasi GPS di luar area geofence dan di luar toleransi ditolak (negative)
test('checking location outside the geofence and beyond tolerance is rejected', function () {
    Carbon::setTestNow('2026-06-08 06:45:00');
    absensiSiswaSquareGeofence();
    $student = absensiSiswaStudent();

    $this->actingAs($student)->postJson(route('siswa.absensi.cek-lokasi'), [
        'latitude' => 10,
        'longitude' => 10,
        'accuracy' => 10,
    ])->assertSuccessful()
        ->assertJson(['allowed' => false, 'status' => 'outside']);
});

// TS.ABS.009 / TC.ABS.009.001 — check-in dengan geofence aktif tapi lokasi tidak dikirim ditolak (negative)
test('student cannot check in without location data when the geofence is active', function () {
    Carbon::setTestNow('2026-06-08 06:45:00');
    Storage::fake('public');
    absensiSiswaSquareGeofence();
    $student = absensiSiswaStudent();

    $this->actingAs($student)->post(route('siswa.absensi.store'), [
        'selfie' => UploadedFile::fake()->image('selfie.jpg')->size(100),
    ])->assertSessionHasErrors(['latitude', 'longitude', 'accuracy']);

    expect(Absensi::first()->status)->toBe('belum_absen');
});

// TS.ABS.010 / TC.ABS.010.001 — check-in dengan lokasi di luar geofence ditolak (negative)
test('student cannot check in when their location is outside the geofence', function () {
    Carbon::setTestNow('2026-06-08 06:45:00');
    Storage::fake('public');
    absensiSiswaSquareGeofence();
    $student = absensiSiswaStudent();

    $this->actingAs($student)->post(route('siswa.absensi.store'), [
        'selfie' => UploadedFile::fake()->image('selfie.jpg')->size(100),
        'latitude' => 10,
        'longitude' => 10,
        'accuracy' => 10,
    ])->assertRedirect(route('siswa.absensi'))
        ->assertSessionHas('error', 'Lokasi Anda di luar area absensi dan tidak bisa absen.');

    expect(Absensi::first()->status)->toBe('belum_absen');
});

// TS.ABS.011 / TC.ABS.011.001 — endpoint status hari ini nampilin status yang sesuai (positive)
test('today status endpoint reports the existing attendance status', function () {
    Carbon::setTestNow('2026-06-08 06:45:00');
    $student = absensiSiswaStudent();
    Absensi::create([
        'profil_siswa_id' => $student->profilSiswa->nisn,
        'tanggal' => now()->toDateString(),
        'status' => 'hadir',
    ]);

    $this->actingAs($student)->getJson(route('siswa.absensi.status'))
        ->assertSuccessful()
        ->assertJson(['sudah_absen' => true, 'status' => 'hadir']);
});
