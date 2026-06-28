<?php

use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

uses(RefreshDatabase::class);

test('google login redirect works', function () {
    $response = $this->get(route('google.redirect', ['mode' => 'login']));

    // It should redirect to google oauth domain
    $response->assertRedirect();
    expect(session('google_oauth_mode'))->toBe('login');
});

test('google callback handles student registration successfully', function () {
    $student = User::factory()->student()->create([
        'status' => 'unregistered',
        'email' => null,
    ]);
    StudentProfile::factory()->create([
        'user_id' => $student->id,
        'nisn' => '12345678',
    ]);

    // Mock Socialite User
    $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
    $abstractUser->shouldReceive('getId')->andReturn('google-id-123');
    $abstractUser->shouldReceive('getEmail')->andReturn('student-google@example.com');
    $abstractUser->shouldReceive('getName')->andReturn($student->name);

    $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
    $provider->shouldReceive('user')->andReturn($abstractUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    session([
        'google_oauth_mode' => 'register',
        'register_role' => 'student',
        'register_user_id' => $student->id,
        'register_identity' => '12345678',
        'register_name' => $student->name,
    ]);

    $this->get(route('google.callback'))
        ->assertRedirect(route('siswa.dashboard'));

    expect(Auth::check())->toBeTrue();
    expect(Auth::user()->id)->toBe($student->id);

    $student->refresh();
    expect($student->google_id)->toBe('google-id-123');
    expect($student->email)->toBe('student-google@example.com');
    expect($student->isRegistered())->toBeTrue();
});

test('google callback handles teacher registration successfully and prompts for whatsapp', function () {
    $teacher = User::factory()->homeroom()->create([
        'status' => 'unregistered',
        'email' => null,
    ]);
    TeacherProfile::factory()->homeroom()->create([
        'user_id' => $teacher->id,
        'nip' => '1991234567',
    ]);

    // Mock Socialite User
    $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
    $abstractUser->shouldReceive('getId')->andReturn('google-id-456');
    $abstractUser->shouldReceive('getEmail')->andReturn('teacher-google@example.com');
    $abstractUser->shouldReceive('getName')->andReturn($teacher->name);

    $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
    $provider->shouldReceive('user')->andReturn($abstractUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    session([
        'google_oauth_mode' => 'register',
        'register_role' => 'teacher',
        'register_user_id' => $teacher->id,
        'register_identity' => '1991234567',
        'register_name' => $teacher->name,
    ]);

    $this->get(route('google.callback'))
        ->assertRedirect(route('google.whatsapp'));

    expect(Auth::check())->toBeTrue();
    expect(session('google_pending_whatsapp'))->toBeTrue();

    // Check we can submit the whatsapp number
    $this->actingAs($teacher)
        ->post(route('google.whatsapp.store'), [
            'phone' => '081234567890',
        ])
        ->assertRedirect(route('wali-kelas.dashboard'));

    $teacher->refresh();
    expect($teacher->teacherProfile->phone)->toBe('081234567890');
    expect($teacher->google_id)->toBe('google-id-456');
    expect($teacher->email)->toBe('teacher-google@example.com');
});

test('google callback logs in registered user', function () {
    $user = User::factory()->student()->create([
        'status' => 'registered',
        'email' => 'registered-student@example.com',
        'google_id' => 'google-id-789',
    ]);
    StudentProfile::factory()->create(['user_id' => $user->id]);

    $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
    $abstractUser->shouldReceive('getId')->andReturn('google-id-789');
    $abstractUser->shouldReceive('getEmail')->andReturn('registered-student@example.com');

    $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
    $provider->shouldReceive('user')->andReturn($abstractUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    session(['google_oauth_mode' => 'login']);

    $this->get(route('google.callback'))
        ->assertRedirect(route('siswa.dashboard'));

    expect(Auth::check())->toBeTrue();
    expect(Auth::user()->id)->toBe($user->id);
});

test('google login fails for unregistered or non-existent user', function () {
    $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
    $abstractUser->shouldReceive('getId')->andReturn('google-id-999');
    $abstractUser->shouldReceive('getEmail')->andReturn('nonexistent@example.com');

    $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
    $provider->shouldReceive('user')->andReturn($abstractUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    session(['google_oauth_mode' => 'login']);

    $this->get(route('google.callback'))
        ->assertRedirect(route('register'))
        ->assertSessionHas('error', 'Akun Google ini belum terdaftar di Sentri Siswa. Silakan daftar terlebih dahulu.');

    expect(Auth::check())->toBeFalse();
});
