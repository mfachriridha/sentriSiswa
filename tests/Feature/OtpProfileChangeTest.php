<?php

use App\Models\Pengguna;
use App\Models\ProfilGuru;
use App\Models\ProfilSiswa;
use App\Models\TokenOtp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('admin can update whatsapp number and email directly without OTP', function () {
    Mail::fake();

    $admin = Pengguna::factory()->create([
        'peran' => 'admin',
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
    expect($admin->nomor_wa)->toBe('081234567890');

    // Admin email change is direct (no OTP) so admin can fix seeder emails
    $this->actingAs($admin)
        ->put(route('admin.profil.update'), [
            'email' => 'new-admin-email@example.com',
            'whatsapp_number' => '081234567890',
        ])
        ->assertRedirect(route('admin.profil'))
        ->assertSessionHas('success', 'Profil admin berhasil diperbarui.');

    $admin->refresh();
    expect($admin->email)->toBe('new-admin-email@example.com');
});

test('teacher updating phone is immediate but email/password change generates OTP', function () {
    Mail::fake();

    $teacher = Pengguna::factory()->homeroom()->create([
        'email_verified_at' => now(),
        'email' => 'teacher@example.com',
        'status' => 'registered',
    ]);
    ProfilGuru::factory()->homeroom()->create([
        'pengguna_id' => $teacher->id,
        'nip' => '123456',
        'telepon' => '08111111111',
    ]);

    // Update Phone only
    $this->actingAs($teacher)
        ->put(route('wali-kelas.profil.update'), [
            'nama' => $teacher->nama,
            'email' => 'teacher@example.com',
            'telepon' => '08222222222',
        ])
        ->assertRedirect(route('wali-kelas.profil'))
        ->assertSessionHas('success', 'Profil berhasil diperbarui.');

    $teacher->refresh();
    expect($teacher->profilGuru->telepon)->toBe('08222222222');

    // Update password
    $this->actingAs($teacher)
        ->put(route('wali-kelas.profil.update'), [
            'nama' => $teacher->nama,
            'email' => 'teacher@example.com',
            'telepon' => '08222222222',
            'password' => 'NewPassword123',
        ])
        ->assertRedirect(route('otp.show'));

    expect(session('otp_type'))->toBe('password_change');
});

test('student updating address is immediate but email change generates OTP', function () {
    Mail::fake();

    $student = Pengguna::factory()->student()->create([
        'email_verified_at' => now(),
        'email' => 'student@example.com',
        'status' => 'registered',
    ]);
    ProfilSiswa::factory()->create([
        'pengguna_id' => $student->id,
        'nisn' => '12345678',
        'telepon' => '08333333333',
        'alamat' => 'Old Address',
    ]);

    // Update address only
    $this->actingAs($student)
        ->put(route('siswa.profil.update'), [
            'email' => 'student@example.com',
            'telepon' => '08333333333',
            'alamat' => 'New Address',
        ])
        ->assertRedirect(route('siswa.profil'))
        ->assertSessionHas('success', 'Profil berhasil diperbarui.');

    $student->refresh();
    expect($student->profilSiswa->alamat)->toBe('New Address');

    // Update email
    $this->actingAs($student)
        ->put(route('siswa.profil.update'), [
            'email' => 'new-student@example.com',
            'telepon' => '08333333333',
            'alamat' => 'New Address',
        ])
        ->assertRedirect(route('otp.show'));

    expect(session('otp_type'))->toBe('email_change');
});

test('verifying profile OTP applies changes', function () {
    $student = Pengguna::factory()->student()->create([
        'email_verified_at' => now(),
        'email' => 'student@example.com',
        'status' => 'registered',
    ]);
    ProfilSiswa::factory()->create([
        'pengguna_id' => $student->id,
    ]);

    // Manually build OTP state
    $rawOtp = '654321';
    TokenOtp::create([
        'pengguna_id' => $student->id,
        'otp' => Hash::make($rawOtp),
        'tipe' => 'email_change',
        'email_baru' => 'verified-new-email@example.com',
        'kadaluwarsa_pada' => now()->addMinutes(10),
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
