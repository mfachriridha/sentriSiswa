<?php

use App\Models\SchoolClass;
use App\Models\SchoolRule;
use App\Models\StudentBiodata;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('demo school seeder creates requested actor and class composition', function () {
    $this->seed();

    expect(User::where('role', 'admin')->count())->toBe(1);
    expect(User::where('role', 'teacher')->whereHas('teacherProfile', fn ($query) => $query->where('teacher_type', 'homeroom'))->count())->toBe(6);
    expect(User::where('role', 'teacher')->whereHas('teacherProfile', fn ($query) => $query->where('teacher_type', 'counselor'))->count())->toBe(3);
    expect(User::where('role', 'teacher')->whereHas('teacherProfile', fn ($query) => $query->where('teacher_type', 'student_affairs'))->count())->toBe(1);

    expect(SchoolClass::count())->toBe(6);
    expect(SchoolClass::where('grade', '10')->count())->toBe(2);
    expect(SchoolClass::where('grade', '11')->count())->toBe(2);
    expect(SchoolClass::where('grade', '12')->count())->toBe(2);

    expect(StudentProfile::count())->toBe(60);
    expect(User::where('role', 'student')->where('status', 'registered')->count())->toBe(48);
    expect(User::where('role', 'student')->where('status', 'unregistered')->count())->toBe(12);
    expect(SchoolClass::withCount('students')->get()->every(fn (SchoolClass $class): bool => $class->students_count === 10))->toBeTrue();

    expect(SchoolRule::where('is_published', true)->count())->toBe(1);
});

test('demo school seeder uses human names without numbers', function () {
    $this->seed();

    expect(User::pluck('name')->filter(fn (string $name): bool => preg_match('/\d/', $name) === 1)->count())->toBe(0);
    expect(StudentBiodata::pluck('father_name')->filter(fn (string $name): bool => preg_match('/\d/', $name) === 1)->count())->toBe(0);
    expect(StudentBiodata::pluck('mother_name')->filter(fn (string $name): bool => preg_match('/\d/', $name) === 1)->count())->toBe(0);
});
