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

function absensiSiswaBvaStudent(): Pengguna
{
    $student = Pengguna::factory()->student()->create(['status' => 'registered']);
    ProfilSiswa::factory()->create([
        'pengguna_id' => $student->id,
        'nis' => fake()->unique()->numerify('#####'),
    ]);

    return $student;
}

// ── Boundary: jendela waktu attendance_start_time / attendance_end_time ─────

// TS.ABS.012 / TC.ABS.012.001 — jam tepat di batas mulai jendela waktu diperbolehkan (positive)
test('student can check in exactly at the attendance window start time', function () {
    Pengaturan::set('attendance_start_time', '06:30');
    Pengaturan::set('attendance_end_time', '07:00');
    Carbon::setTestNow('2026-06-08 06:30:00');
    Storage::fake('public');
    $student = absensiSiswaBvaStudent();

    $this->actingAs($student)->post(route('siswa.absensi.store'), [
        'selfie' => UploadedFile::fake()->image('selfie.jpg')->size(100),
    ])->assertRedirect(route('siswa.absensi'))
        ->assertSessionHas('success');
});

// TS.ABS.013 / TC.ABS.013.001 — 1 menit sebelum jam mulai jendela waktu ditolak (negative)
test('student cannot check in 1 minute before the attendance window start time', function () {
    Pengaturan::set('attendance_start_time', '06:30');
    Pengaturan::set('attendance_end_time', '07:00');
    Carbon::setTestNow('2026-06-08 06:29:00');
    Storage::fake('public');
    $student = absensiSiswaBvaStudent();

    $this->actingAs($student)->post(route('siswa.absensi.store'), [
        'selfie' => UploadedFile::fake()->image('selfie.jpg')->size(100),
    ])->assertSessionHas('error', 'Waktu absen sudah lewat atau belum dimulai.');
});

// TS.ABS.014 / TC.ABS.014.001 — jam tepat di batas selesai jendela waktu diperbolehkan (positive)
test('student can check in exactly at the attendance window end time', function () {
    Pengaturan::set('attendance_start_time', '06:30');
    Pengaturan::set('attendance_end_time', '07:00');
    Pengaturan::set('attendance_late_time', '07:00');
    Carbon::setTestNow('2026-06-08 07:00:00');
    Storage::fake('public');
    $student = absensiSiswaBvaStudent();

    $this->actingAs($student)->post(route('siswa.absensi.store'), [
        'selfie' => UploadedFile::fake()->image('selfie.jpg')->size(100),
    ])->assertRedirect(route('siswa.absensi'))
        ->assertSessionHas('success');
});

// TS.ABS.015 / TC.ABS.015.001 — 1 menit setelah jam selesai jendela waktu ditolak (negative)
test('student cannot check in 1 minute after the attendance window end time', function () {
    Pengaturan::set('attendance_start_time', '06:30');
    Pengaturan::set('attendance_end_time', '07:00');
    Carbon::setTestNow('2026-06-08 07:01:00');
    Storage::fake('public');
    $student = absensiSiswaBvaStudent();

    $this->actingAs($student)->post(route('siswa.absensi.store'), [
        'selfie' => UploadedFile::fake()->image('selfie.jpg')->size(100),
    ])->assertSessionHas('error', 'Waktu absen sudah lewat atau belum dimulai.');
});

// ── Boundary: batas toleransi telat (attendance_end_time - attendance_late_time) ─

// TS.ABS.016 / TC.ABS.016.001 — tepat di batas toleransi telat hasilnya hadir (positive)
test('student checking in exactly at the late tolerance boundary is marked hadir', function () {
    Pengaturan::set('attendance_start_time', '06:30');
    Pengaturan::set('attendance_end_time', '07:30');
    Pengaturan::set('attendance_late_time', '07:00');
    Carbon::setTestNow('2026-06-08 07:00:00');
    Storage::fake('public');
    $student = absensiSiswaBvaStudent();

    $this->actingAs($student)->post(route('siswa.absensi.store'), [
        'selfie' => UploadedFile::fake()->image('selfie.jpg')->size(100),
    ])->assertRedirect(route('siswa.absensi'));

    expect(Absensi::firstOrFail()->status)->toBe('hadir');
});

// TS.ABS.017 / TC.ABS.017.001 — 1 menit lewat batas toleransi telat hasilnya terlambat (positive, beda hasil)
test('student checking in 1 minute past the late tolerance boundary is marked terlambat', function () {
    Pengaturan::set('attendance_start_time', '06:30');
    Pengaturan::set('attendance_end_time', '07:30');
    Pengaturan::set('attendance_late_time', '07:00');
    Carbon::setTestNow('2026-06-08 07:01:00');
    Storage::fake('public');
    $student = absensiSiswaBvaStudent();

    $this->actingAs($student)->post(route('siswa.absensi.store'), [
        'selfie' => UploadedFile::fake()->image('selfie.jpg')->size(100),
    ])->assertRedirect(route('siswa.absensi'));

    expect(Absensi::firstOrFail()->status)->toBe('terlambat');
});

// ── Boundary: ukuran selfie max:300 KB ──────────────────────────────────────

// TS.ABS.018 / TC.ABS.018.001 — ukuran selfie tepat 300 KB diperbolehkan (positive)
test('student can check in with a selfie of exactly 300 KB', function () {
    Carbon::setTestNow('2026-06-08 06:45:00');
    Storage::fake('public');
    $student = absensiSiswaBvaStudent();

    $this->actingAs($student)->post(route('siswa.absensi.store'), [
        'selfie' => UploadedFile::fake()->image('selfie.jpg')->size(300),
    ])->assertRedirect(route('siswa.absensi'))
        ->assertSessionHas('success');
});

// TS.ABS.019 / TC.ABS.019.001 — ukuran selfie 301 KB ditolak (negative)
test('student cannot check in with a selfie of 301 KB', function () {
    Carbon::setTestNow('2026-06-08 06:45:00');
    Storage::fake('public');
    $student = absensiSiswaBvaStudent();

    $this->actingAs($student)->post(route('siswa.absensi.store'), [
        'selfie' => UploadedFile::fake()->image('selfie.jpg')->size(301),
    ])->assertSessionHasErrors('selfie');

    expect(Absensi::first()?->status ?? 'belum_absen')->toBe('belum_absen');
});
