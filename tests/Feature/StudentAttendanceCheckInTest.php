<?php

use App\Models\Absensi;
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

function createRegisteredStudent(): Pengguna
{
    $student = Pengguna::factory()->student()->create([
        'status' => 'registered',
    ]);

    ProfilSiswa::factory()->create([
        'pengguna_id' => $student->id,
        'nis' => fake()->unique()->numerify('#####'),
    ]);

    return $student;
}

test('student check in updates pre-created belum absen record', function () {
    Carbon::setTestNow('2026-06-01 06:45:00');
    Storage::fake('public');

    $student = createRegisteredStudent();
    $profile = $student->profilSiswa;
    Absensi::create([
        'profil_siswa_id' => $profile->nisn,
        'tanggal' => now()->toDateString(),
        'status' => 'belum_absen',
    ]);

    $this->actingAs($student)
        ->post(route('siswa.absensi.store'), [
            'selfie' => UploadedFile::fake()->image('selfie.jpg')->size(100),
        ])
        ->assertRedirect(route('siswa.absensi'));

    $attendance = Absensi::firstOrFail();

    expect($attendance->status)->toBe('hadir')
        ->and($attendance->waktu_masuk?->format('H:i'))->toBe('06:45')
        ->and($attendance->path_selfie)->not->toBeNull();

    Storage::disk('public')->assertExists($attendance->path_selfie);
});

test('student check in creates fallback record when daily command has not run', function () {
    Carbon::setTestNow('2026-06-01 06:45:00');
    Storage::fake('public');

    $student = createRegisteredStudent();

    $this->actingAs($student)
        ->post(route('siswa.absensi.store'), [
            'selfie' => UploadedFile::fake()->image('selfie.jpg')->size(100),
        ])
        ->assertRedirect(route('siswa.absensi'));

    $attendance = Absensi::firstOrFail();

    expect($attendance->profil_siswa_id)->toBe($student->profilSiswa->nisn)
        ->and($attendance->tanggal->toDateString())->toBe('2026-06-01')
        ->and($attendance->status)->toBe('hadir');
});

test('student cannot overwrite final manual attendance status', function () {
    Carbon::setTestNow('2026-06-01 06:45:00');
    Storage::fake('public');

    $student = createRegisteredStudent();
    Absensi::create([
        'profil_siswa_id' => $student->profilSiswa->nisn,
        'tanggal' => now()->toDateString(),
        'status' => 'izin',
    ]);

    $this->actingAs($student)
        ->post(route('siswa.absensi.store'), [
            'selfie' => UploadedFile::fake()->image('selfie.jpg')->size(100),
        ])
        ->assertRedirect(route('siswa.absensi'))
        ->assertSessionHas('error', 'Anda sudah absen hari ini.');

    expect(Absensi::firstOrFail()->status)->toBe('izin');
    Storage::disk('public')->assertDirectoryEmpty('/');
});

test('student cannot check in on weekend', function () {
    Carbon::setTestNow('2026-06-06 06:45:00');
    Storage::fake('public');

    $student = createRegisteredStudent();

    $this->actingAs($student)
        ->post(route('siswa.absensi.store'), [
            'selfie' => UploadedFile::fake()->image('selfie.jpg')->size(100),
        ])
        ->assertRedirect(route('siswa.absensi'))
        ->assertSessionHas('error', 'Absensi hanya tersedia pada hari Senin, Selasa, Rabu, Kamis dan Jumat.');

    expect(Absensi::count())->toBe(0);
});
