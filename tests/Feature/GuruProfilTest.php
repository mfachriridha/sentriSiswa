<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('registered teacher can view and update own profile', function () {
    $teacher = User::factory()->homeroom()->create([
        'email' => 'guru.lama@example.com',
        'password' => Hash::make('password'),
        'status' => 'registered',
    ]);

    $teacher->teacherProfile()->create([
        'nip' => '198765432109876543',
        'phone' => '081234567890',
        'teacher_type' => 'homeroom',
        'grade' => null,
    ]);

    $this->actingAs($teacher)
        ->get(route('guru.profil'))
        ->assertSuccessful()
        ->assertSee('Profil Saya')
        ->assertSee('198765432109876543')
        ->assertSee('Wali Kelas');

    $this->actingAs($teacher)
        ->put(route('guru.profil.update'), [
            'name' => $teacher->name,
            'email' => 'guru.baru@example.com',
            'phone' => '081298765432',
            'password' => 'Secret123',
        ])
        ->assertRedirect(route('guru.profil'));

    $teacher->refresh();

    expect($teacher->email)->toBe('guru.baru@example.com');
    expect(Hash::check('Secret123', $teacher->password))->toBeTrue();

    $this->assertDatabaseHas('teacher_profiles', [
        'user_id' => $teacher->id,
        'phone' => '081298765432',
        'nip' => '198765432109876543',
        'teacher_type' => 'homeroom',
    ]);
});

test('guru dashboard redirects to profile while dashboard is deferred', function () {
    $teacher = User::factory()->homeroom()->create([
        'status' => 'registered',
    ]);

    $teacher->teacherProfile()->create([
        'nip' => '198765432109876544',
        'phone' => '081234567891',
        'teacher_type' => 'homeroom',
        'grade' => null,
    ]);

    $this->actingAs($teacher)
        ->get(route('guru.dashboard'))
        ->assertRedirect(route('guru.profil'));
});

test('registered student cannot access guru profile', function () {
    $student = User::factory()->student()->create([
        'status' => 'registered',
    ]);

    $this->actingAs($student)
        ->get(route('guru.profil'))
        ->assertForbidden();
});
