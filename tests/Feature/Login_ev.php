<?php

use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

// TS.LOG.001 / TC.LOG.001.001 — valid email + correct password (positive)
test('login succeeds with registered email and correct password', function () {
    $user = Pengguna::factory()->student()->create([
        'email' => 'siswa.valid@example.com',
        'status' => 'registered',
    ]);

    $this->get(route('login'))->assertSuccessful();

    $this->post(route('login'), [
        'email' => 'siswa.valid@example.com',
        'password' => 'password',
    ])->assertRedirect(route($user->dashboardRouteName()));

    expect(Auth::check())->toBeTrue();
    expect(Auth::id())->toBe($user->id);
});

// TS.LOG.002 / TC.LOG.002.001 — valid email format, wrong password (negative)
test('login fails with registered email and wrong password', function () {
    Pengguna::factory()->student()->create([
        'email' => 'siswa.salah@example.com',
        'status' => 'registered',
    ]);

    $this->post(route('login'), [
        'email' => 'siswa.salah@example.com',
        'password' => 'password-salah',
    ])->assertSessionHasErrors('email');

    expect(Auth::check())->toBeFalse();
});

// TS.LOG.003 / TC.LOG.003.001 — email valid format but not registered (negative)
test('login fails with email that does not exist', function () {
    $this->post(route('login'), [
        'email' => 'tidak.terdaftar@example.com',
        'password' => 'password',
    ])->assertSessionHasErrors('email');

    expect(Auth::check())->toBeFalse();
});

// TS.LOG.004 / TC.LOG.004.001 — invalid email format (negative)
test('login fails with invalid email format', function () {
    $this->post(route('login'), [
        'email' => 'bukan-email',
        'password' => 'password',
    ])->assertSessionHasErrors('email');

    expect(Auth::check())->toBeFalse();
});

// TS.LOG.005 / TC.LOG.005.001 — empty email (negative)
test('login fails when email is empty', function () {
    $this->post(route('login'), [
        'email' => '',
        'password' => 'password',
    ])->assertSessionHasErrors('email');

    expect(Auth::check())->toBeFalse();
});

// TS.LOG.006 / TC.LOG.006.001 — empty password (negative)
test('login fails when password is empty', function () {
    Pengguna::factory()->student()->create([
        'email' => 'siswa.kosongpass@example.com',
        'status' => 'registered',
    ]);

    $this->post(route('login'), [
        'email' => 'siswa.kosongpass@example.com',
        'password' => '',
    ])->assertSessionHasErrors('password');

    expect(Auth::check())->toBeFalse();
});
