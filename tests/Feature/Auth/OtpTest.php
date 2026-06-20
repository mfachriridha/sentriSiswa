<?php

use App\Models\User;
use App\Services\OtpGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('otp can be sent to authenticated user email', function () {
    Mail::fake();

    $user = User::factory()->create([
        'peran' => User::PERAN_ADMIN,
        'status' => User::STATUS_TERDAFTAR,
    ]);

    $response = $this->actingAs($user)->post(route('otp.kirim'), [
        'tujuan' => 'ganti_email',
    ]);

    $response->assertRedirect(route('otp.verifikasi'));
    Mail::assertSentCount(1);
});

test('otp verification screen can be rendered', function () {
    $user = User::factory()->create([
        'peran' => User::PERAN_ADMIN,
        'status' => User::STATUS_TERDAFTAR,
    ]);

    session(['otp_tujuan' => 'ganti_email']);

    $response = $this->actingAs($user)->get(route('otp.verifikasi'));

    $response->assertOk();
});

test('otp can be verified with correct code', function () {
    $user = User::factory()->create([
        'peran' => User::PERAN_ADMIN,
        'status' => User::STATUS_TERDAFTAR,
    ]);

    $otpService = app(OtpGenerator::class);
    $otp = $otpService->generate($user->id);

    session(['otp_tujuan' => 'ganti_email']);

    $response = $this->actingAs($user)->post(route('otp.verifikasi.proses'), [
        'otp' => $otp,
    ]);

    $response->assertRedirect(route('otp.ganti-email'));
});

test('otp cannot be verified with incorrect code', function () {
    $user = User::factory()->create([
        'peran' => User::PERAN_ADMIN,
        'status' => User::STATUS_TERDAFTAR,
    ]);

    $otpService = app(OtpGenerator::class);
    $otpService->generate($user->id);

    session(['otp_tujuan' => 'ganti_email']);

    $response = $this->actingAs($user)->post(route('otp.verifikasi.proses'), [
        'otp' => '9999',
    ]);

    $response->assertSessionHasErrors('otp');
});

test('otp is locked out after too many attempts', function () {
    $user = User::factory()->create([
        'peran' => User::PERAN_ADMIN,
        'status' => User::STATUS_TERDAFTAR,
    ]);

    $otpService = app(OtpGenerator::class);
    $otpService->generate($user->id);

    session(['otp_tujuan' => 'ganti_email']);

    // Attempt 3 times with wrong code
    for ($i = 0; $i < 3; $i++) {
        $this->actingAs($user)->post(route('otp.verifikasi.proses'), [
            'otp' => '9999',
        ]);
    }

    // 4th attempt should be locked out
    $response = $this->actingAs($user)->post(route('otp.verifikasi.proses'), [
        'otp' => '9999',
    ]);

    $response->assertSessionHasErrors('otp');
});

test('email can be changed after otp verification', function () {
    $user = User::factory()->create([
        'peran' => User::PERAN_ADMIN,
        'status' => User::STATUS_TERDAFTAR,
    ]);

    session([
        'otp_tujuan' => 'ganti_email',
        'otp_verified' => true,
    ]);

    $response = $this->actingAs($user)->put(route('otp.ganti-email.simpan'), [
        'email' => 'newemail@example.com',
    ]);

    $response->assertRedirect(route('dashboard'));
    expect($user->fresh()->email)->toBe('newemail@example.com');
});

test('password can be changed after otp verification', function () {
    $user = User::factory()->create([
        'peran' => User::PERAN_ADMIN,
        'status' => User::STATUS_TERDAFTAR,
    ]);

    session([
        'otp_tujuan' => 'ganti_password',
        'otp_verified' => true,
    ]);

    $response = $this->actingAs($user)->put(route('otp.ganti-password.simpan'), [
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertRedirect(route('dashboard'));
});
