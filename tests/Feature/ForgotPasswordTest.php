<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

uses(RefreshDatabase::class);

test('guest can view forgot password page', function () {
    $this->get(route('password.request'))
        ->assertSuccessful()
        ->assertSee('Lupa Kata Sandi')
        ->assertSee('Kirim Tautan Reset');
});

test('submitting forgot password form sends reset email', function () {
    Notification::fake();

    $user = Pengguna::factory()->create([
        'email' => 'registered@example.com',
    ]);

    $this->post(route('password.email'), [
        'email' => 'registered@example.com',
    ])
        ->assertRedirect()
        ->assertSessionHas('status', 'Jika email terdaftar, tautan reset kata sandi akan dikirimkan.');
});

test('guest can view reset password page with token', function () {
    $user = Pengguna::factory()->create([
        'email' => 'registered@example.com',
    ]);

    $token = Password::createToken($user);

    $this->get(route('password.reset', ['token' => $token, 'email' => 'registered@example.com']))
        ->assertSuccessful()
        ->assertSee('Buat Kata Sandi Baru')
        ->assertSee('Reset Kata Sandi');
});

test('user can reset password with valid token', function () {
    $user = Pengguna::factory()->create([
        'email' => 'registered@example.com',
        'password' => Hash::make('OldPassword123'),
    ]);

    $token = Password::createToken($user);

    $this->post(route('password.update'), [
        'token' => $token,
        'email' => 'registered@example.com',
        'password' => 'NewPassword123',
        'password_confirmation' => 'NewPassword123',
    ])
        ->assertRedirect(route('login'))
        ->assertSessionHas('success', 'Kata sandi berhasil direset. Silakan masuk.');

    $user->refresh();
    expect(Hash::check('NewPassword123', $user->password))->toBeTrue();
});
