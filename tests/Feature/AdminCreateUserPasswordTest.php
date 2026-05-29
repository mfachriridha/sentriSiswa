<?php

use App\Models\SchoolClass;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('admin can create a student without a password and gets the default hashed fallback', function () {
    $admin = User::factory()->admin()->create();
    $class = SchoolClass::create([
        'name' => '10. 1',
        'grade' => '10',
    ]);

    $response = $this->actingAs($admin)->post(route('admin.siswa.store'), [
        'name' => 'Siswa Tes',
        'email' => 'siswa.tes@example.com',
        'nisn' => '1234567890',
        'nis' => '12345',
        'class_id' => (string) $class->id,
        'phone' => '081234567890',
        'address' => 'Jl. Testing 1',
    ]);

    expect($response->getStatusCode())->toBe(302);
    expect($response->headers->get('Location'))->toBe(route('admin.siswa.index'));

    $student = User::where('email', 'siswa.tes@example.com')->firstOrFail();

    expect($student->role)->toBe('student');
    expect($student->status)->toBe('unregistered');
    expect(Hash::check('password', $student->password))->toBeTrue();

    $this->assertDatabaseHas('student_profiles', [
        'user_id' => $student->id,
        'nisn' => '1234567890',
        'class_id' => (string) $class->id,
    ]);
    $this->assertDatabaseHas('users', [
        'id' => $student->id,
        'role' => 'student',
        'status' => 'unregistered',
    ]);

    $this->assertNotNull(StudentProfile::where('user_id', $student->id)->first());
});

test('admin can create a teacher without a password and gets the default hashed fallback', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('admin.guru.store'), [
        'name' => 'Guru Tes',
        'email' => 'guru.tes@example.com',
        'nip' => '198765432109876543',
        'phone' => '081298765432',
        'teacher_type' => 'homeroom',
    ]);

    $response->assertRedirect(route('admin.guru.index'));

    $teacher = User::where('email', 'guru.tes@example.com')->firstOrFail();

    expect($teacher->role)->toBe('teacher');
    expect($teacher->status)->toBe('unregistered');
    expect(Hash::check('password', $teacher->password))->toBeTrue();

    $this->assertDatabaseHas('teacher_profiles', [
        'user_id' => $teacher->id,
        'nip' => '198765432109876543',
        'teacher_type' => 'homeroom',
    ]);
    $this->assertDatabaseHas('users', [
        'id' => $teacher->id,
        'role' => 'teacher',
        'status' => 'unregistered',
    ]);

    $this->assertNotNull(TeacherProfile::where('user_id', $teacher->id)->first());
});

test('admin can create a student with a manual password and still gets unregistered status', function () {
    $admin = User::factory()->admin()->create();
    $class = SchoolClass::create([
        'name' => '10. 2',
        'grade' => '10',
    ]);

    $response = $this->actingAs($admin)->post(route('admin.siswa.store'), [
        'name' => 'Siswa Manual',
        'email' => 'siswa.manual@example.com',
        'password' => 'Secret123',
        'nisn' => '2234567890',
        'nis' => '54321',
        'class_id' => (string) $class->id,
        'phone' => '081200000000',
        'address' => 'Jl. Manual 2',
    ]);

    expect($response->getStatusCode())->toBe(302);
    expect($response->headers->get('Location'))->toBe(route('admin.siswa.index'));

    $student = User::where('email', 'siswa.manual@example.com')->firstOrFail();

    expect($student->role)->toBe('student');
    expect($student->status)->toBe('unregistered');
    expect(Hash::check('Secret123', $student->password))->toBeTrue();
    expect(Hash::check('password', $student->password))->toBeFalse();

    $this->assertDatabaseHas('student_profiles', [
        'user_id' => $student->id,
        'nisn' => '2234567890',
        'class_id' => (string) $class->id,
    ]);
});

test('admin can create a teacher with a manual password and still gets unregistered status', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('admin.guru.store'), [
        'name' => 'Guru Manual',
        'email' => 'guru.manual@example.com',
        'password' => 'Secret123',
        'nip' => '198765432109876544',
        'phone' => '081233344455',
        'teacher_type' => 'counselor',
        'grade' => '11',
    ]);

    expect($response->getStatusCode())->toBe(302);
    expect($response->headers->get('Location'))->toBe(route('admin.guru.index'));

    $teacher = User::where('email', 'guru.manual@example.com')->firstOrFail();

    expect($teacher->role)->toBe('teacher');
    expect($teacher->status)->toBe('unregistered');
    expect(Hash::check('Secret123', $teacher->password))->toBeTrue();
    expect(Hash::check('password', $teacher->password))->toBeFalse();

    $this->assertDatabaseHas('teacher_profiles', [
        'user_id' => $teacher->id,
        'nip' => '198765432109876544',
        'teacher_type' => 'counselor',
        'grade' => '11',
    ]);
});

test('unregistered students are blocked from siswa dashboard by the registered middleware', function () {
    $student = User::factory()->student()->create([
        'status' => 'unregistered',
        'password' => Hash::make('password'),
    ]);

    $student->studentProfile()->create([
        'nisn' => '3234567890',
        'nis' => '65432',
        'class_id' => null,
        'phone' => null,
        'address' => null,
    ]);

    $this->actingAs($student)
        ->get(route('siswa.dashboard'))
        ->assertRedirect(route('login'))
        ->assertSessionHas('error', 'Akun belum terdaftar. Silakan daftar terlebih dahulu.');

    $this->assertGuest();
});

test('unregistered teachers are blocked from guru dashboard by the registered middleware', function () {
    $teacher = User::factory()->homeroom()->create([
        'status' => 'unregistered',
        'password' => Hash::make('password'),
    ]);

    $teacher->teacherProfile()->create([
        'nip' => '298765432109876544',
        'phone' => '081244455566',
        'teacher_type' => 'homeroom',
        'grade' => null,
    ]);

    $this->actingAs($teacher)
        ->get(route('guru.dashboard'))
        ->assertRedirect(route('login'))
        ->assertSessionHas('error', 'Akun belum terdaftar. Silakan daftar terlebih dahulu.');

    $this->assertGuest();
});
