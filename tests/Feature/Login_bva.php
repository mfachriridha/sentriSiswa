<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

// TS.Log.007 / TC.Log.007.001 — email field length boundary: 0 characters (below the "required" minimum of 1)
test('login fails when email has 0 characters', function () {
    $this->post(route('login'), [
        'email' => '',
        'password' => 'password',
    ])->assertSessionHasErrors('email');

    expect(Auth::check())->toBeFalse();
});

// TS.Log.007 / TC.Log.007.002 — email field length boundary: 1 character (just above the minimum, passes "required" but fails email format)
test('login rejects email with 1 character as invalid format', function () {
    $this->post(route('login'), [
        'email' => 'a',
        'password' => 'password',
    ])->assertSessionHasErrors('email');

    expect(Auth::check())->toBeFalse();
});

// TS.Log.008 / TC.Log.008.001 — password field length boundary: 0 characters (below the "required" minimum of 1)
test('login fails when password has 0 characters', function () {
    Pengguna::factory()->student()->create([
        'email' => 'boundary.pass0@example.com',
        'status' => 'registered',
    ]);

    $this->post(route('login'), [
        'email' => 'boundary.pass0@example.com',
        'password' => '',
    ])->assertSessionHasErrors('password');

    expect(Auth::check())->toBeFalse();
});

// TS.Log.008 / TC.Log.008.002 — password field length boundary: 1 character (just above the minimum, passes "required" but wrong credential)
test('login accepts password field with 1 character but rejects wrong credential', function () {
    Pengguna::factory()->student()->create([
        'email' => 'boundary.pass1@example.com',
        'status' => 'registered',
    ]);

    $this->post(route('login'), [
        'email' => 'boundary.pass1@example.com',
        'password' => 'x',
    ])->assertSessionHasErrors('email');

    expect(Auth::check())->toBeFalse();
});
