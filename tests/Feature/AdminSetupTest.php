<?php

use App\Mail\OtpMail;
use App\Models\EmailOtpToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('admin who needs setup is redirected to setup page', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => null,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertRedirect(route('admin.setup'));
})->skip('Bypassed admin setup redirection per user feedback');

test('admin setup page can be rendered', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => null,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.setup'))
        ->assertSuccessful()
        ->assertSee('Setup Akun Admin')
        ->assertSee('Email Baru');
});

test('submitting setup form stores session and sends OTP', function () {
    Mail::fake();

    $admin = User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => null,
    ]);

    $this->actingAs($admin)
        ->post(route('admin.setup.store'), [
            'email' => 'new-admin@example.com',
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ])
        ->assertRedirect(route('admin.setup.verify'));

    expect(session('otp_type'))->toBe('admin_setup');
    expect(session('otp_pending.new_email'))->toBe('new-admin@example.com');

    Mail::assertSent(OtpMail::class, function ($mail) {
        return $mail->hasTo('new-admin@example.com');
    });

    $token = EmailOtpToken::where('user_id', $admin->id)->first();
    expect($token)->not->toBeNull();
    expect($token->type)->toBe('admin_setup');
});

test('submitting correct OTP completes setup', function () {
    Mail::fake();

    $admin = User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => null,
    ]);

    // Send OTP first
    $response = $this->actingAs($admin)
        ->post(route('admin.setup.store'), [
            'email' => 'new-admin@example.com',
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ]);

    $response->assertRedirect(route('admin.setup.verify'));

    $token = EmailOtpToken::where('user_id', $admin->id)->latest()->first();

    // Verify OTP page
    $this->actingAs($admin)
        ->get(route('admin.setup.verify'))
        ->assertSuccessful()
        ->assertSee('Verifikasi Email Admin');

    // Retrieve raw OTP via test logic or decrypt it if stored (wait, we fake OTP generation by overriding or reading raw OTP)
    // Since we random_int a raw OTP in OtpService, let's mock or generate token directly for exact OTP value:
    $token->delete(); // delete automatically generated

    // Create specific token with known OTP
    $rawOtp = '123456';
    $token = EmailOtpToken::create([
        'user_id' => $admin->id,
        'otp' => Hash::make($rawOtp),
        'type' => 'admin_setup',
        'new_email' => 'new-admin@example.com',
        'new_password' => Hash::make('NewPassword123'),
        'expires_at' => now()->addMinutes(10),
    ]);

    session([
        'otp_type' => 'admin_setup',
        'otp_pending' => [
            'new_email' => 'new-admin@example.com',
            'new_password' => 'NewPassword123',
        ],
    ]);

    $this->actingAs($admin)
        ->post(route('admin.setup.verify.store'), [
            'otp' => '123456',
        ])
        ->assertRedirect(route('admin.dashboard'));

    $admin->refresh();
    expect($admin->email)->toBe('new-admin@example.com');
    expect(Hash::check('NewPassword123', $admin->password))->toBeTrue();
    expect($admin->email_verified_at)->not->toBeNull();
    expect($admin->needsAdminSetup())->toBeFalse();
});

test('admin with setup complete is not redirected to setup', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful();
});
