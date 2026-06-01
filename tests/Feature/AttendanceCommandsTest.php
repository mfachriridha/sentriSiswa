<?php

use App\Models\Attendance;
use App\Models\Setting;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

afterEach(function () {
    Carbon::setTestNow();
});

function createStudentProfile(): StudentProfile
{
    $student = User::factory()->student()->create([
        'status' => 'registered',
    ]);

    return StudentProfile::factory()->create([
        'user_id' => $student->id,
        'nis' => fake()->unique()->numerify('#####'),
    ]);
}

test('daily attendance command creates belum absen records on weekday', function () {
    Carbon::setTestNow('2026-06-01 00:00:00');
    $student = createStudentProfile();

    $this->artisan('attendance:create-daily')
        ->expectsOutput('Created 1 attendance records for 2026-06-01.')
        ->assertSuccessful();

    $attendance = Attendance::firstOrFail();

    expect($attendance->student_profile_id)->toBe($student->id)
        ->and($attendance->date->toDateString())->toBe('2026-06-01')
        ->and($attendance->status)->toBe('belum_absen');
});

test('daily attendance command does nothing on weekend', function () {
    Carbon::setTestNow('2026-06-06 00:00:00');
    createStudentProfile();

    $this->artisan('attendance:create-daily')
        ->expectsOutput('Hari ini bukan hari aktif absensi. Tidak ada record yang dibuat.')
        ->assertSuccessful();

    expect(Attendance::count())->toBe(0);
});

test('unmarked attendance command converts records to alpha after attendance time', function () {
    Carbon::setTestNow('2026-06-01 07:06:00');
    $student = createStudentProfile();
    Setting::set('attendance_end_time', '07:00');
    Attendance::create([
        'student_profile_id' => $student->id,
        'date' => '2026-06-01',
        'status' => 'belum_absen',
    ]);

    $this->artisan('attendance:update-unmarked')
        ->expectsOutput('Updated 1 records from belum_absen to alpha for 2026-06-01.')
        ->assertSuccessful();

    expect(Attendance::firstOrFail()->status)->toBe('alpha');
});

test('unmarked attendance command does nothing on weekend', function () {
    Carbon::setTestNow('2026-06-06 07:06:00');
    $student = createStudentProfile();
    Attendance::create([
        'student_profile_id' => $student->id,
        'date' => '2026-06-06',
        'status' => 'belum_absen',
    ]);

    $this->artisan('attendance:update-unmarked')
        ->expectsOutput('Hari ini bukan hari aktif absensi. Tidak ada status yang diperbarui.')
        ->assertSuccessful();

    expect(Attendance::firstOrFail()->status)->toBe('belum_absen');
});
