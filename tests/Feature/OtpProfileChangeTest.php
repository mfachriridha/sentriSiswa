<?php

use App\Models\EmailOtpToken;
use App\Models\StudentProfile;
use App\Models\TeacherProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('admin can update whatsapp number without OTP but updating email redirects to OTP', function () {
    Mail::fake();

    $admin = User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
        'email' => 'admin@example.com',
    ]);

    // Update WhatsApp only
    $this->actingAs($admin)
        ->put(route('admin.profil.update'), [
            'email' => 'admin@example.com',
            'whatsapp_number' => '081234567890',
        ])
        ->assertRedirect(route('admin.profil'))
        ->assertSessionHas('success', 'Profil admin berhasil diperbarui.');

    $admin->refresh();
    expect($admin->whatsapp_number)->toBe('081234567890');

    // Update Email
    $this->actingAs($admin)
        ->put(route('admin.profil.update'), [
            'email' => 'new-admin-email@example.com',
            'whatsapp_number' => '081234567890',
        ])
        ->assertRedirect(route('otp.show'));

    expect(session('otp_type'))->toBe('email_change');
    expect(session('otp_pending.new_email'))->toBe('new-admin-email@example.com');
});

test('teacher updating phone is immediate but email/password change generates OTP', function () {
    Mail::fake();

    $teacher = User::factory()->homeroom()->create([
        'email_verified_at' => now(),
        'email' => 'teacher@example.com',
        'status' => 'registered',
    ]);
    TeacherProfile::factory()->homeroom()->create([
        'user_id' => $teacher->id,
        'nip' => '123456',
        'phone' => '08111111111',
    ]);

    // Update Phone only
    $this->actingAs($teacher)
        ->put(route('wali-kelas.profil.update'), [
            'name' => $teacher->name,
            'email' => 'teacher@example.com',
            'phone' => '08222222222',
        ])
        ->assertRedirect(route('wali-kelas.profil'))
        ->assertSessionHas('success', 'Profil berhasil diperbarui.');

    $teacher->refresh();
    expect($teacher->teacherProfile->phone)->toBe('08222222222');

    // Update password
    $this->actingAs($teacher)
        ->put(route('wali-kelas.profil.update'), [
            'name' => $teacher->name,
            'email' => 'teacher@example.com',
            'phone' => '08222222222',
            'password' => 'NewPassword123',
        ])
        ->assertRedirect(route('otp.show'));

    expect(session('otp_type'))->toBe('password_change');
});

test('student updating address is immediate but email change generates OTP', function () {
    Mail::fake();

    $student = User::factory()->student()->create([
        'email_verified_at' => now(),
        'email' => 'student@example.com',
        'status' => 'registered',
    ]);
    StudentProfile::factory()->create([
        'user_id' => $student->id,
        'nisn' => '12345678',
        'phone' => '08333333333',
        'address' => 'Old Address',
    ]);

    // Update address only
    $this->actingAs($student)
        ->put(route('siswa.profil.update'), [
            'email' => 'student@example.com',
            'phone' => '08333333333',
            'address' => 'New Address',
        ])
        ->assertRedirect(route('siswa.profil'))
        ->assertSessionHas('success', 'Profil berhasil diperbarui.');

    $student->refresh();
    expect($student->studentProfile->address)->toBe('New Address');

    // Update email
    $this->actingAs($student)
        ->put(route('siswa.profil.update'), [
            'email' => 'new-student@example.com',
            'phone' => '08333333333',
            'address' => 'New Address',
        ])
        ->assertRedirect(route('otp.show'));

    expect(session('otp_type'))->toBe('email_change');
});

test('verifying profile OTP applies changes', function () {
    $student = User::factory()->student()->create([
        'email_verified_at' => now(),
        'email' => 'student@example.com',
        'status' => 'registered',
    ]);
    StudentProfile::factory()->create([
        'user_id' => $student->id,
    ]);

    // Manually build OTP state
    $rawOtp = '654321';
    EmailOtpToken::create([
        'user_id' => $student->id,
        'otp' => Hash::make($rawOtp),
        'type' => 'email_change',
        'new_email' => 'verified-new-email@example.com',
        'expires_at' => now()->addMinutes(10),
    ]);

    session([
        'otp_type' => 'email_change',
        'otp_pending' => [
            'new_email' => 'verified-new-email@example.com',
        ],
    ]);

    $this->actingAs($student)
        ->post(route('otp.verify'), [
            'otp' => '654321',
        ])
        ->assertRedirect(route('siswa.profil'))
        ->assertSessionHas('success', 'Perubahan berhasil disimpan.');

    $student->refresh();
    expect($student->email)->toBe('verified-new-email@example.com');
});
