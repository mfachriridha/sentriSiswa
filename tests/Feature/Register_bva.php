<?php

use App\Models\Pengguna;
use App\Models\ProfilGuru;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function bvaEmailOfLength(int $totalLength): string
{
    $domain = '@example.com';
    $localLength = $totalLength - strlen($domain);

    return str_repeat('a', $localLength).$domain;
}

function bvaRegisterStudentSession(): Pengguna
{
    $student = Pengguna::factory()->student()->create(['status' => 'unregistered', 'email' => null]);
    ProfilSiswa::factory()->create([
        'pengguna_id' => $student->id,
        'nisn' => '2222222220',
        'nis' => '30001',
    ]);

    session([
        'register_role' => 'student',
        'register_user_id' => $student->id,
        'register_identity' => '2222222220',
        'register_name' => $student->nama,
    ]);

    return $student;
}

function bvaRegisterTeacherSession(): Pengguna
{
    $teacher = Pengguna::factory()->homeroom()->create(['status' => 'unregistered', 'email' => null]);
    ProfilGuru::factory()->create([
        'pengguna_id' => $teacher->id,
        'nip' => '198501012020121088',
    ]);

    session([
        'register_role' => 'teacher',
        'register_user_id' => $teacher->id,
        'register_identity' => '198501012020121088',
        'register_name' => $teacher->nama,
    ]);

    return $teacher;
}

// ── Boundary: password length, min:8 ──────────────────────────────────────

// TS.REG.019 / TC.REG.019.001 — password with 7 characters (just below the minimum of 8, invalid)
test('register store rejects password with 7 characters', function () {
    bvaRegisterStudentSession();

    $this->post(route('register.store'), [
        'email' => 'boundary.pw7@example.com',
        'password' => 'Passw0r',
        'password_confirmation' => 'Passw0r',
    ])->assertSessionHasErrors('password');
});

// TS.REG.020 / TC.REG.020.001 — password with exactly 8 characters (at the minimum, valid)
test('register store accepts password with exactly 8 characters', function () {
    $student = bvaRegisterStudentSession();

    $this->post(route('register.store'), [
        'email' => 'boundary.pw8@example.com',
        'password' => 'Passw0rd',
        'password_confirmation' => 'Passw0rd',
    ])->assertRedirect(route('login'));

    expect($student->fresh()->status)->toBe('registered');
});

// ── Boundary: teacher telepon length, min:10 / max:20 ─────────────────────

// TS.REG.021 / TC.REG.021.001 — telepon with 9 characters (just below the minimum of 10, invalid)
test('register store rejects teacher telepon with 9 characters', function () {
    bvaRegisterTeacherSession();

    $this->post(route('register.store'), [
        'email' => 'boundary.tel9@example.com',
        'password' => 'Passw0rd',
        'password_confirmation' => 'Passw0rd',
        'telepon' => '081234567',
    ])->assertSessionHasErrors('telepon');
});

// TS.REG.022 / TC.REG.022.001 — telepon with exactly 10 characters (at the minimum, valid)
test('register store accepts teacher telepon with exactly 10 characters', function () {
    $teacher = bvaRegisterTeacherSession();

    $this->post(route('register.store'), [
        'email' => 'boundary.tel10@example.com',
        'password' => 'Passw0rd',
        'password_confirmation' => 'Passw0rd',
        'telepon' => '0812345678',
    ])->assertRedirect(route('login'));

    expect($teacher->fresh()->profilGuru->telepon)->toBe('0812345678');
});

// TS.REG.023 / TC.REG.023.001 — telepon with exactly 20 characters (at the maximum, valid)
test('register store accepts teacher telepon with exactly 20 characters', function () {
    $teacher = bvaRegisterTeacherSession();
    $telepon20 = str_repeat('0', 20);

    $this->post(route('register.store'), [
        'email' => 'boundary.tel20@example.com',
        'password' => 'Passw0rd',
        'password_confirmation' => 'Passw0rd',
        'telepon' => $telepon20,
    ])->assertRedirect(route('login'));

    expect($teacher->fresh()->profilGuru->telepon)->toBe($telepon20);
});

// TS.REG.024 / TC.REG.024.001 — telepon with 21 characters (just above the maximum, invalid)
test('register store rejects teacher telepon with 21 characters', function () {
    bvaRegisterTeacherSession();
    $telepon21 = str_repeat('0', 21);

    $this->post(route('register.store'), [
        'email' => 'boundary.tel21@example.com',
        'password' => 'Passw0rd',
        'password_confirmation' => 'Passw0rd',
        'telepon' => $telepon21,
    ])->assertSessionHasErrors('telepon');
});

// ── Boundary: email length, max:255 ───────────────────────────────────────

// TS.REG.025 / TC.REG.025.001 — email with exactly 255 characters (at the maximum, valid)
test('register store accepts email with exactly 255 characters', function () {
    $student = bvaRegisterStudentSession();
    $email255 = bvaEmailOfLength(255);

    $this->post(route('register.store'), [
        'email' => $email255,
        'password' => 'Passw0rd',
        'password_confirmation' => 'Passw0rd',
    ])->assertRedirect(route('login'));

    expect($student->fresh()->email)->toBe($email255);
});

// TS.REG.026 / TC.REG.026.001 — email with 256 characters (just above the maximum, invalid)
test('register store rejects email with 256 characters', function () {
    bvaRegisterStudentSession();
    $email256 = bvaEmailOfLength(256);

    $this->post(route('register.store'), [
        'email' => $email256,
        'password' => 'Passw0rd',
        'password_confirmation' => 'Passw0rd',
    ])->assertSessionHasErrors('email');
});
