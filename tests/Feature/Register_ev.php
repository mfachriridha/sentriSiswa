<?php

use App\Models\Pengguna;
use App\Models\ProfilGuru;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

uses(RefreshDatabase::class);

// ── Step 1: verifikasi identitas ─────────────────────────────────────────

// TS.Reg.001 / TC.Reg.001.001 — valid unregistered student verified via NISN (positive)
test('verify succeeds for unregistered student using nisn', function () {
    $student = Pengguna::factory()->student()->create(['status' => 'unregistered']);
    ProfilSiswa::factory()->create([
        'pengguna_id' => $student->id,
        'nisn' => '1234567890',
        'nis' => '10001',
    ]);

    $this->get(route('register'))->assertSuccessful();

    $this->post(route('register.verify'), [
        'peran' => 'student',
        'identity' => '1234567890',
    ])->assertSuccessful();

    expect(session('register_role'))->toBe('student');
    expect(session('register_user_id'))->toBe($student->id);
});

// TS.Reg.002 / TC.Reg.002.001 — valid unregistered student verified via NIS (positive, alternate identity class)
test('verify succeeds for unregistered student using nis', function () {
    $student = Pengguna::factory()->student()->create(['status' => 'unregistered']);
    ProfilSiswa::factory()->create([
        'pengguna_id' => $student->id,
        'nisn' => '1234567891',
        'nis' => '10002',
    ]);

    $this->post(route('register.verify'), [
        'peran' => 'student',
        'identity' => '10002',
    ])->assertSuccessful();

    expect(session('register_user_id'))->toBe($student->id);
});

// TS.Reg.003 / TC.Reg.003.001 — valid unregistered teacher verified via NIP (positive)
test('verify succeeds for unregistered teacher using nip', function () {
    $teacher = Pengguna::factory()->homeroom()->create(['status' => 'unregistered']);
    ProfilGuru::factory()->create([
        'pengguna_id' => $teacher->id,
        'nip' => '198501012020121001',
    ]);

    $this->post(route('register.verify'), [
        'peran' => 'teacher',
        'identity' => '198501012020121001',
    ])->assertSuccessful();

    expect(session('register_role'))->toBe('teacher');
    expect(session('register_user_id'))->toBe($teacher->id);
});

// TS.Reg.004 / TC.Reg.004.001 — identity not found in database (negative)
test('verify fails when identity does not exist', function () {
    $this->post(route('register.verify'), [
        'peran' => 'student',
        'identity' => '9999999999',
    ])->assertSessionHasErrors('identity');
});

// TS.Reg.005 / TC.Reg.005.001 — identity already registered (negative)
test('verify fails when identity is already registered', function () {
    $student = Pengguna::factory()->student()->create(['status' => 'registered']);
    ProfilSiswa::factory()->create([
        'pengguna_id' => $student->id,
        'nisn' => '1234567892',
        'nis' => '10003',
    ]);

    $this->post(route('register.verify'), [
        'peran' => 'student',
        'identity' => '1234567892',
    ])->assertSessionHasErrors('identity');
});

// TS.Reg.006 / TC.Reg.006.001 — identity contains non-numeric characters (negative)
test('verify fails when identity contains non-numeric characters', function () {
    $this->post(route('register.verify'), [
        'peran' => 'student',
        'identity' => '12AB567890',
    ])->assertSessionHasErrors('identity');
});

// TS.Reg.007 / TC.Reg.007.001 — invalid peran value (negative)
test('verify fails when peran is not teacher or student', function () {
    $this->post(route('register.verify'), [
        'peran' => 'admin',
        'identity' => '1234567890',
    ])->assertSessionHasErrors('peran');
});

// TS.Reg.008 / TC.Reg.008.001 — empty identity (negative)
test('verify fails when identity is empty', function () {
    $this->post(route('register.verify'), [
        'peran' => 'student',
        'identity' => '',
    ])->assertSessionHasErrors('identity');
});

// ── Step 2: lengkapi pendaftaran (email + password) ──────────────────────

function registerStep2Student(): Pengguna
{
    $student = Pengguna::factory()->student()->create(['status' => 'unregistered', 'email' => null]);
    ProfilSiswa::factory()->create([
        'pengguna_id' => $student->id,
        'nisn' => '1111111111',
        'nis' => '20001',
    ]);

    session([
        'register_role' => 'student',
        'register_user_id' => $student->id,
        'register_identity' => '1111111111',
        'register_name' => $student->nama,
    ]);

    return $student;
}

function registerStep2Teacher(): Pengguna
{
    $teacher = Pengguna::factory()->homeroom()->create(['status' => 'unregistered', 'email' => null]);
    ProfilGuru::factory()->create([
        'pengguna_id' => $teacher->id,
        'nip' => '198501012020121099',
    ]);

    session([
        'register_role' => 'teacher',
        'register_user_id' => $teacher->id,
        'register_identity' => '198501012020121099',
        'register_name' => $teacher->nama,
    ]);

    return $teacher;
}

// TS.Reg.009 / TC.Reg.009.001 — valid registration data for a student (positive)
test('register store succeeds for student with valid data', function () {
    $student = registerStep2Student();

    $this->post(route('register.store'), [
        'email' => 'siswa.baru@example.com',
        'password' => 'Password123',
        'password_confirmation' => 'Password123',
    ])->assertRedirect(route('login'));

    expect(Auth::check())->toBeFalse();
    $student->refresh();
    expect($student->status)->toBe('registered');
    expect($student->email)->toBe('siswa.baru@example.com');
});

// TS.Reg.010 / TC.Reg.010.001 — valid registration data for a teacher including telepon (positive)
test('register store succeeds for teacher with valid data and telepon', function () {
    $teacher = registerStep2Teacher();

    $this->post(route('register.store'), [
        'email' => 'guru.baru@example.com',
        'password' => 'Password123',
        'password_confirmation' => 'Password123',
        'telepon' => '081234567890',
    ])->assertRedirect(route('login'));

    $teacher->refresh();
    expect($teacher->status)->toBe('registered');
    expect($teacher->profilGuru->telepon)->toBe('081234567890');
});

// TS.Reg.011 / TC.Reg.011.001 — email already used by another account (negative)
test('register store fails when email already taken', function () {
    Pengguna::factory()->create(['email' => 'sudah.ada@example.com']);
    registerStep2Student();

    $this->post(route('register.store'), [
        'email' => 'sudah.ada@example.com',
        'password' => 'Password123',
        'password_confirmation' => 'Password123',
    ])->assertSessionHasErrors('email');
});

// TS.Reg.012 / TC.Reg.012.001 — invalid email format (negative)
test('register store fails with invalid email format', function () {
    registerStep2Student();

    $this->post(route('register.store'), [
        'email' => 'bukan-email',
        'password' => 'Password123',
        'password_confirmation' => 'Password123',
    ])->assertSessionHasErrors('email');
});

// TS.Reg.013 / TC.Reg.013.001 — password missing a digit (negative)
test('register store fails when password has no digit', function () {
    registerStep2Student();

    $this->post(route('register.store'), [
        'email' => 'siswa.nodigit@example.com',
        'password' => 'PasswordOnly',
        'password_confirmation' => 'PasswordOnly',
    ])->assertSessionHasErrors('password');
});

// TS.Reg.014 / TC.Reg.014.001 — password missing a letter (negative)
test('register store fails when password has no letter', function () {
    registerStep2Student();

    $this->post(route('register.store'), [
        'email' => 'siswa.noletter@example.com',
        'password' => '12345678',
        'password_confirmation' => '12345678',
    ])->assertSessionHasErrors('password');
});

// TS.Reg.015 / TC.Reg.015.001 — password confirmation mismatch (negative)
test('register store fails when password confirmation does not match', function () {
    registerStep2Student();

    $this->post(route('register.store'), [
        'email' => 'siswa.mismatch@example.com',
        'password' => 'Password123',
        'password_confirmation' => 'Password999',
    ])->assertSessionHasErrors('password');
});

// TS.Reg.016 / TC.Reg.016.001 — teacher telepon empty (negative)
test('register store fails when teacher telepon is empty', function () {
    registerStep2Teacher();

    $this->post(route('register.store'), [
        'email' => 'guru.notelepon@example.com',
        'password' => 'Password123',
        'password_confirmation' => 'Password123',
        'telepon' => '',
    ])->assertSessionHasErrors('telepon');
});

// TS.Reg.017 / TC.Reg.017.001 — teacher telepon contains invalid characters (negative)
test('register store fails when teacher telepon has invalid characters', function () {
    registerStep2Teacher();

    $this->post(route('register.store'), [
        'email' => 'guru.badtelepon@example.com',
        'password' => 'Password123',
        'password_confirmation' => 'Password123',
        'telepon' => 'abc123xyz9',
    ])->assertSessionHasErrors('telepon');
});

// TS.Reg.018 / TC.Reg.018.001 — verification session expired before completing step 2 (negative)
test('register store redirects to register when verification session is missing', function () {
    $this->post(route('register.store'), [
        'email' => 'tanpa.sesi@example.com',
        'password' => 'Password123',
        'password_confirmation' => 'Password123',
    ])->assertRedirect(route('register'));
});

// TS.Reg.027 / TC.Reg.027.001 — student completes registration via Google after identity verification (positive)
test('register completes for student via google after identity verification', function () {
    $student = Pengguna::factory()->student()->create(['status' => 'unregistered', 'email' => null]);
    ProfilSiswa::factory()->create([
        'pengguna_id' => $student->id,
        'nisn' => '3333333330',
        'nis' => '40001',
    ]);

    $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
    $abstractUser->shouldReceive('getId')->andReturn('google-id-reg-student');
    $abstractUser->shouldReceive('getEmail')->andReturn('siswa.google.baru@example.com');
    $abstractUser->shouldReceive('getName')->andReturn($student->nama);

    $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
    $provider->shouldReceive('user')->andReturn($abstractUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    session([
        'google_oauth_mode' => 'register',
        'register_role' => 'student',
        'register_user_id' => $student->id,
        'register_identity' => '3333333330',
        'register_name' => $student->nama,
    ]);

    $this->get(route('google.callback'))
        ->assertRedirect(route('siswa.dashboard'));

    expect(Auth::check())->toBeTrue();
    $student->refresh();
    expect($student->status)->toBe('registered');
    expect($student->id_google)->toBe('google-id-reg-student');
    expect($student->email)->toBe('siswa.google.baru@example.com');
});

// TS.Reg.028 / TC.Reg.028.001 — teacher completes registration via Google, then submits telepon (positive)
test('register completes for teacher via google then submits telepon', function () {
    $teacher = Pengguna::factory()->homeroom()->create(['status' => 'unregistered', 'email' => null]);
    ProfilGuru::factory()->create([
        'pengguna_id' => $teacher->id,
        'nip' => '198501012020121077',
    ]);

    $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
    $abstractUser->shouldReceive('getId')->andReturn('google-id-reg-teacher');
    $abstractUser->shouldReceive('getEmail')->andReturn('guru.google.baru@example.com');
    $abstractUser->shouldReceive('getName')->andReturn($teacher->nama);

    $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
    $provider->shouldReceive('user')->andReturn($abstractUser);

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

    session([
        'google_oauth_mode' => 'register',
        'register_role' => 'teacher',
        'register_user_id' => $teacher->id,
        'register_identity' => '198501012020121077',
        'register_name' => $teacher->nama,
    ]);

    $this->get(route('google.callback'))
        ->assertRedirect(route('google.whatsapp'));

    expect(session('google_pending_whatsapp'))->toBeTrue();

    $this->actingAs($teacher)
        ->post(route('google.whatsapp.store'), ['telepon' => '081298765432'])
        ->assertRedirect(route('wali-kelas.dashboard'));

    $teacher->refresh();
    expect($teacher->status)->toBe('registered');
    expect($teacher->id_google)->toBe('google-id-reg-teacher');
    expect($teacher->profilGuru->telepon)->toBe('081298765432');
});
