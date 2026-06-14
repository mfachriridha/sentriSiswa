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

    expect(StudentProfile::count())->toBe(18);
    expect(User::where('role', 'student')->where('status', 'registered')->count())->toBe(12);
    expect(User::where('role', 'student')->where('status', 'unregistered')->count())->toBe(6);
    expect(SchoolClass::withCount('students')->get()->every(fn (SchoolClass $class): bool => $class->students_count === 3))->toBeTrue();

    expect(SchoolRule::where('is_published', true)->count())->toBe(1);
});

test('demo school seeder uses human names without numbers', function () {
    $this->seed();

    expect(User::pluck('name')->filter(fn (string $name): bool => preg_match('/\d/', $name) === 1)->count())->toBe(0);
    expect(StudentBiodata::pluck('father_name')->filter(fn (string $name): bool => preg_match('/\d/', $name) === 1)->count())->toBe(0);
    expect(StudentBiodata::pluck('mother_name')->filter(fn (string $name): bool => preg_match('/\d/', $name) === 1)->count())->toBe(0);
    expect(StudentBiodata::pluck('guardian_name')->filter(fn (string $name): bool => preg_match('/\d/', $name) === 1)->count())->toBe(0);
});

test('demo school seeder creates complete biodata for every student', function () {
    $this->seed();

    expect(StudentBiodata::count())->toBe(18);
    expect(StudentBiodata::get()->every(fn (StudentBiodata $biodata): bool => filled($biodata->place_of_birth)
        && filled($biodata->date_of_birth)
        && filled($biodata->gender)
        && filled($biodata->religion)
        && filled($biodata->family_status)
        && filled($biodata->child_number)
        && filled($biodata->school_of_origin)
        && filled($biodata->admission_date)
        && filled($biodata->father_name)
        && filled($biodata->father_occupation)
        && filled($biodata->mother_name)
        && filled($biodata->mother_occupation)
        && filled($biodata->parent_address)
        && filled($biodata->parent_phone)
        && filled($biodata->guardian_name)
        && filled($biodata->guardian_occupation)
        && filled($biodata->guardian_address)
        && filled($biodata->guardian_phone)
    ))->toBeTrue();
});
