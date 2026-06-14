<?php

use App\Models\SchoolClass;
use App\Models\SchoolRule;
use App\Models\StudentProfile;
use App\Models\StudentViolation;
use App\Models\User;
use App\Models\ViolationType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('counselor can only monitor assigned grade and cannot approve violations', function () {
    [$counselor, $gradeTenStudent, $gradeElevenStudent] = actorWorkflowUsers();

    $this->actingAs($counselor)
        ->get(route('guru.bk.monitoring.index'))
        ->assertSuccessful()
        ->assertSee($gradeTenStudent->user->name)
        ->assertDontSee($gradeElevenStudent->user->name);

    $this->actingAs($counselor)
        ->get(route('guru.bk.monitoring.show', $gradeTenStudent))
        ->assertSuccessful()
        ->assertSee('Detail Siswa')
        ->assertSee('Biodata Lengkap');

    $this->actingAs($counselor)
        ->get(route('guru.bk.monitoring.show', $gradeElevenStudent))
        ->assertForbidden();

    $violation = StudentViolation::factory()->create([
        'student_profile_id' => $gradeTenStudent->id,
        'recorded_by_user_id' => $counselor->id,
        'status' => 'pending',
        'approved_at' => null,
    ]);

    $this->actingAs($counselor)
        ->put(route('guru.pelanggaran-siswa.approve', $violation))
        ->assertForbidden();
});

test('student affairs can view student detail across classes', function () {
    [, $gradeTenStudent, $gradeElevenStudent] = actorWorkflowUsers();
    $studentAffairs = createActorWorkflowTeacher('student_affairs');

    $this->actingAs($studentAffairs)
        ->get(route('guru.kesiswaan.monitoring.show', $gradeTenStudent))
        ->assertSuccessful()
        ->assertSee($gradeTenStudent->user->name);

    $this->actingAs($studentAffairs)
        ->get(route('guru.kesiswaan.monitoring.show', $gradeElevenStudent))
        ->assertSuccessful()
        ->assertSee($gradeElevenStudent->user->name);
});

test('counselor submits violation and student affairs approves it', function () {
    [$counselor, $student] = actorWorkflowUsers();
    $studentAffairs = createActorWorkflowTeacher('student_affairs');
    $violationType = ViolationType::factory()->create();

    $this->actingAs($counselor)
        ->post(route('guru.bk.pelanggaran.store'), [
            'student_profile_id' => $student->id,
            'violation_type_id' => $violationType->id,
            'violation_date' => now()->toDateString(),
            'notes' => 'Terlambat masuk kelas.',
        ])
        ->assertRedirect(route('guru.bk.pelanggaran.index'));

    $violation = StudentViolation::first();

    expect($violation->status)->toBe('pending');
    expect($student->fresh()->points)->toBe(100);

    $this->actingAs($studentAffairs)
        ->put(route('guru.pelanggaran-siswa.approve', $violation))
        ->assertRedirect(route('guru.pelanggaran-siswa.show', $violation));

    expect($violation->fresh()->status)->toBe('approved');
    expect($student->fresh()->points)->toBe(100 - $violationType->point_deduction);
});

test('student affairs can reject violation without reducing student points', function () {
    [$counselor, $student] = actorWorkflowUsers();
    $studentAffairs = createActorWorkflowTeacher('student_affairs');
    $violation = StudentViolation::factory()->create([
        'student_profile_id' => $student->id,
        'recorded_by_user_id' => $counselor->id,
        'status' => 'pending',
        'approved_at' => null,
    ]);

    $this->actingAs($studentAffairs)
        ->put(route('guru.pelanggaran-siswa.reject', $violation), [
            'rejection_reason' => 'Bukti belum cukup.',
        ])
        ->assertRedirect(route('guru.pelanggaran-siswa.show', $violation));

    expect($violation->fresh()->status)->toBe('rejected');
    expect($student->fresh()->points)->toBe(100);
});

test('school rule pdf can be uploaded by student affairs and viewed by student', function () {
    Storage::fake('public');

    $studentAffairs = createActorWorkflowTeacher('student_affairs');
    $studentUser = User::factory()->student()->create(['status' => 'registered']);
    StudentProfile::factory()->create(['user_id' => $studentUser->id]);

    $this->actingAs($studentAffairs)
        ->post(route('guru.kesiswaan.tata-tertib.store'), [
            'title' => 'Tata Tertib 2026',
            'rule_pdf' => UploadedFile::fake()->create('aturan.pdf', 64, 'application/pdf'),
            'is_published' => '1',
        ])
        ->assertRedirect(route('guru.kesiswaan.tata-tertib.index'));

    $rule = SchoolRule::first();

    expect($rule)->not->toBeNull();
    expect($rule->is_published)->toBeTrue();
    Storage::disk('public')->assertExists($rule->file_path);

    $this->actingAs($studentUser)
        ->get(route('siswa.tata-tertib.index'))
        ->assertSuccessful()
        ->assertSee('Tata Tertib 2026');
});

test('violation reports and attendance pdf routes render downloads for allowed roles', function () {
    [$counselor, $student] = actorWorkflowUsers();
    $studentAffairs = createActorWorkflowTeacher('student_affairs');
    $homeroom = createActorWorkflowTeacher('homeroom');
    $class = $student->class;
    $class->update(['homeroom_teacher_id' => $homeroom->id]);

    StudentViolation::factory()->create([
        'student_profile_id' => $student->id,
        'status' => 'approved',
        'approved_at' => now(),
    ]);

    $this->actingAs($counselor)
        ->get(route('guru.bk.laporan.index'))
        ->assertSuccessful()
        ->assertSee('Laporan BK');

    $this->actingAs($counselor)
        ->get(route('guru.bk.laporan.export-excel'))
        ->assertSuccessful();

    $this->actingAs($studentAffairs)
        ->get(route('guru.kesiswaan.laporan.index'))
        ->assertSuccessful()
        ->assertSee('Laporan Kesiswaan');

    $this->actingAs($studentAffairs)
        ->get(route('guru.kesiswaan.laporan.export-excel'))
        ->assertSuccessful();

    $this->actingAs($homeroom)
        ->get(route('guru.absensi.export-excel'))
        ->assertSuccessful();

    $this->actingAs($homeroom)
        ->get(route('guru.absensi.export-pdf'))
        ->assertSuccessful()
        ->assertHeader('content-type', 'application/pdf');
});

function actorWorkflowUsers(): array
{
    $classTen = SchoolClass::create(['name' => 'X RPL 1', 'grade' => '10']);
    $classEleven = SchoolClass::create(['name' => 'XI RPL 1', 'grade' => '11']);

    $counselor = createActorWorkflowTeacher('counselor', '10');
    $gradeTenStudent = createActorWorkflowStudentInClass($classTen);
    $gradeElevenStudent = createActorWorkflowStudentInClass($classEleven);

    return [$counselor, $gradeTenStudent, $gradeElevenStudent];
}

function createActorWorkflowTeacher(string $teacherType, ?string $grade = null): User
{
    if ($teacherType === 'student_affairs') {
        DB::statement('PRAGMA ignore_check_constraints = ON');
    }

    $teacher = User::factory()->homeroom()->create(['status' => 'registered']);
    $teacher->teacherProfile()->create([
        'nip' => fake()->unique()->numerify('19############'),
        'phone' => fake()->numerify('08##########'),
        'teacher_type' => $teacherType,
        'grade' => $grade,
    ]);

    if ($teacherType === 'student_affairs') {
        DB::statement('PRAGMA ignore_check_constraints = OFF');
    }

    return $teacher;
}

function createActorWorkflowStudentInClass(SchoolClass $class): StudentProfile
{
    $student = User::factory()->student()->create(['status' => 'registered']);

    return StudentProfile::factory()->create([
        'user_id' => $student->id,
        'class_id' => $class->id,
        'nisn' => fake()->unique()->numerify('##########'),
        'nis' => fake()->unique()->numerify('#####'),
    ])->load(['user', 'class']);
}
