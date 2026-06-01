<?php

use App\Models\Attendance;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

afterEach(function () {
    Carbon::setTestNow();
});

function createRegisteredStudent(): User
{
    $student = User::factory()->student()->create([
        'status' => 'registered',
    ]);

    StudentProfile::factory()->create([
        'user_id' => $student->id,
        'nis' => fake()->unique()->numerify('#####'),
    ]);

    return $student;
}

test('student check in updates pre-created belum absen record', function () {
    Carbon::setTestNow('2026-06-01 06:45:00');
    Storage::fake('public');

    $student = createRegisteredStudent();
    $profile = $student->studentProfile;
    Attendance::create([
        'student_profile_id' => $profile->id,
        'date' => now()->toDateString(),
        'status' => 'belum_absen',
    ]);

    $this->actingAs($student)
        ->post(route('siswa.absensi.store'), [
            'selfie' => UploadedFile::fake()->image('selfie.jpg')->size(100),
        ])
        ->assertRedirect(route('siswa.absensi'));

    $attendance = Attendance::firstOrFail();

    expect($attendance->status)->toBe('hadir')
        ->and($attendance->check_in_time?->format('H:i'))->toBe('06:45')
        ->and($attendance->selfie_path)->not->toBeNull();

    Storage::disk('public')->assertExists($attendance->selfie_path);
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

    $attendance = Attendance::firstOrFail();

    expect($attendance->student_profile_id)->toBe($student->studentProfile->id)
        ->and($attendance->date->toDateString())->toBe('2026-06-01')
        ->and($attendance->status)->toBe('hadir');
});

test('student cannot overwrite final manual attendance status', function () {
    Carbon::setTestNow('2026-06-01 06:45:00');
    Storage::fake('public');

    $student = createRegisteredStudent();
    Attendance::create([
        'student_profile_id' => $student->studentProfile->id,
        'date' => now()->toDateString(),
        'status' => 'izin',
    ]);

    $this->actingAs($student)
        ->post(route('siswa.absensi.store'), [
            'selfie' => UploadedFile::fake()->image('selfie.jpg')->size(100),
        ])
        ->assertRedirect(route('siswa.absensi'))
        ->assertSessionHas('error', 'Anda sudah absen hari ini.');

    expect(Attendance::firstOrFail()->status)->toBe('izin');
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
        ->assertSessionHas('error', 'Absensi hanya tersedia pada hari Senin sampai Jumat.');

    expect(Attendance::count())->toBe(0);
});
