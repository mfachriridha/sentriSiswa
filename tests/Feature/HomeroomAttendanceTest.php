<?php

use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

afterEach(function () {
    Carbon::setTestNow();
});

function createHomeroomTeacherWithClass(string $className = 'X IPA 1'): array
{
    $teacher = User::factory()->homeroom()->create([
        'status' => 'registered',
    ]);

    $teacher->teacherProfile()->create([
        'nip' => fake()->unique()->numerify('19################'),
        'teacher_type' => 'homeroom',
    ]);

    $class = SchoolClass::create([
        'name' => $className,
        'grade' => '10',
        'homeroom_teacher_id' => $teacher->id,
    ]);

    return [$teacher, $class];
}

function createStudentInClass(SchoolClass $class, string $name, string $nis): StudentProfile
{
    $student = User::factory()->student()->create([
        'name' => $name,
        'status' => 'registered',
    ]);

    return StudentProfile::factory()->create([
        'user_id' => $student->id,
        'class_id' => $class->id,
        'nis' => $nis,
    ]);
}

test('kelas saya shows today attendance summary and all students without pagination', function () {
    Carbon::setTestNow('2026-06-01 08:00:00');

    [$teacher, $class] = createHomeroomTeacherWithClass();
    $firstStudent = createStudentInClass($class, 'Ayu', '10001');
    createStudentInClass($class, 'Bima', '10002');

    Attendance::create([
        'student_profile_id' => $firstStudent->id,
        'date' => now()->toDateString(),
        'status' => 'hadir',
        'check_in_time' => '06:40',
        'selfie_path' => 'attendance-selfies/1/selfie.jpg',
    ]);

    $this->actingAs($teacher)
        ->get(route('guru.kelas-saya'))
        ->assertSuccessful()
        ->assertSee('Pantau absensi hari ini')
        ->assertSee('10001')
        ->assertSee('Ayu')
        ->assertSee('10002')
        ->assertSee('Bima')
        ->assertSee('Selfie Ayu')
        ->assertViewHas('stats', fn (array $stats): bool => $stats['hadir'] === 1 && $stats['belum_absen'] === 1);
});

test('homeroom teacher can set manual attendance without fake check in data', function () {
    Carbon::setTestNow('2026-06-01 08:00:00');

    [$teacher, $class] = createHomeroomTeacherWithClass();
    $student = createStudentInClass($class, 'Ayu', '10001');

    $this->actingAs($teacher)
        ->put(route('guru.kelas-saya.absensi.update', $student), [
            'status' => 'hadir',
        ])
        ->assertRedirect(route('guru.kelas-saya'));

    $attendance = Attendance::firstOrFail();

    expect($attendance->status)->toBe('hadir')
        ->and($attendance->check_in_time)->toBeNull()
        ->and($attendance->selfie_path)->toBeNull();
});

test('homeroom teacher cannot update student from another class', function () {
    Carbon::setTestNow('2026-06-01 08:00:00');

    [$teacher] = createHomeroomTeacherWithClass();
    [, $otherClass] = createHomeroomTeacherWithClass('X IPA 2');
    $student = createStudentInClass($otherClass, 'Citra', '10003');

    $this->actingAs($teacher)
        ->put(route('guru.kelas-saya.absensi.update', $student), [
            'status' => 'hadir',
        ])
        ->assertForbidden();
});

test('homeroom teacher can view own student detail but not another class detail', function () {
    [$teacher, $class] = createHomeroomTeacherWithClass();
    $student = createStudentInClass($class, 'Ayu', '10001')->load('user');
    [, $otherClass] = createHomeroomTeacherWithClass('X IPA 2');
    $otherStudent = createStudentInClass($otherClass, 'Citra', '10003');

    $this->actingAs($teacher)
        ->get(route('guru.kelas-saya.show', $student))
        ->assertSuccessful()
        ->assertSee('Detail Siswa')
        ->assertSee('Biodata Lengkap')
        ->assertSee($student->user->name);

    $this->actingAs($teacher)
        ->get(route('guru.kelas-saya.show', $otherStudent))
        ->assertForbidden();
});

test('recap summarizes final weekday records and ignores weekend and belum absen records', function () {
    Carbon::setTestNow('2026-06-08 08:00:00');

    [$teacher, $class] = createHomeroomTeacherWithClass();
    $student = createStudentInClass($class, 'Ayu', '10001');

    foreach ([
        ['2026-06-01', 'hadir'],
        ['2026-06-02', 'terlambat'],
        ['2026-06-03', 'izin'],
        ['2026-06-06', 'alpha'],
        ['2026-06-08', 'belum_absen'],
    ] as [$date, $status]) {
        Attendance::create([
            'student_profile_id' => $student->id,
            'date' => $date,
            'status' => $status,
        ]);
    }

    $this->actingAs($teacher)
        ->get(route('guru.absensi.index', [
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-08',
        ]))
        ->assertSuccessful()
        ->assertViewHas('stats', function (array $stats) use ($student): bool {
            return $stats[$student->id] === [
                'hadir' => 1,
                'terlambat' => 1,
                'izin' => 1,
                'sakit' => 0,
                'alpha' => 0,
                'percentage' => 66.7,
            ];
        });
});

test('homeroom teacher can download excel recap for selected range', function () {
    Carbon::setTestNow('2026-06-08 08:00:00');

    [$teacher, $class] = createHomeroomTeacherWithClass();
    createStudentInClass($class, 'Ayu', '10001');

    $this->actingAs($teacher)
        ->get(route('guru.absensi.export-excel', [
            'start_date' => '2026-06-01',
            'end_date' => '2026-06-08',
        ]))
        ->assertDownload('rekap-absensi-X IPA 1-2026-06-01-sampai-2026-06-08.xlsx');
});
