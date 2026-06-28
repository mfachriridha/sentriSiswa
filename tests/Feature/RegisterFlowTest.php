<?php

use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

test('student can register and is redirected to login without auto-login', function () {
    $student = User::factory()->student()->create(['status' => 'unregistered']);
    StudentProfile::factory()->create([
        'user_id' => $student->id,
        'nisn' => '0012345678',
        'nis' => '12345',
    ]);

    $this->withSession([
        'register_role' => 'student',
        'register_user_id' => $student->id,
        'register_identity' => '0012345678',
        'register_name' => $student->name,
    ])
        ->post(route('register.store'), [
            'email' => 'new-student@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ])
        ->assertRedirect(route('login'))
        ->assertSessionHas('success', 'Pendaftaran berhasil. Silakan masuk dengan akun Anda.');

    expect(Auth::check())->toBeFalse();

    $student->refresh();
    expect($student->status)->toBe('registered');
    expect($student->email)->toBe('new-student@example.com');
});

test('teacher can register and is redirected to login without auto-login', function () {
    $teacher = User::factory()->homeroom()->create(['status' => 'unregistered']);
    TeacherProfile::factory()->homeroom()->create([
        'user_id' => $teacher->id,
        'nip' => '199123456789',
    ]);

    $this->withSession([
        'register_role' => 'teacher',
        'register_user_id' => $teacher->id,
        'register_identity' => '199123456789',
        'register_name' => $teacher->name,
    ])
        ->post(route('register.store'), [
            'email' => 'new-teacher@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'phone' => '081234567890',
        ])
        ->assertRedirect(route('login'))
        ->assertSessionHas('success', 'Pendaftaran berhasil. Silakan masuk dengan akun Anda.');

    expect(Auth::check())->toBeFalse();

    $teacher->refresh();
    expect($teacher->status)->toBe('registered');
    expect($teacher->email)->toBe('new-teacher@example.com');

    expect($teacher->teacherProfile->phone)->toBe('081234567890');
});

test('login page shows back to landing link', function () {
    $this->get(route('login'))
        ->assertSuccessful()
        ->assertSee('Kembali ke Beranda')
        ->assertSee('href="'.route('home').'"', false);
});

test('register page shows back to landing link', function () {
    $this->get(route('register'))
        ->assertSuccessful()
        ->assertSee('Kembali ke Beranda')
        ->assertSee('href="'.route('home').'"', false);
});

test('register step 2 page shows back to landing link', function () {
    $student = User::factory()->student()->create(['status' => 'unregistered']);
    StudentProfile::factory()->create([
        'user_id' => $student->id,
        'nisn' => '0012345678',
        'nis' => '12345',
    ]);

    $this->withSession([
        'register_role' => 'student',
        'register_user_id' => $student->id,
        'register_identity' => '0012345678',
        'register_name' => $student->name,
    ])
        ->get(route('register.step2'))
        ->assertSuccessful()
        ->assertSee('Kembali ke Beranda')
        ->assertSee('href="'.route('home').'"', false);
});
