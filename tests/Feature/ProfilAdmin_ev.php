<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function profilAdminUser(): Pengguna
{
    return Pengguna::factory()->admin()->create([
        'status' => 'registered',
        'email' => 'admin.lama@example.com',
        'password' => Hash::make('OldPassw0rd'),
    ]);
}

// TS.ProfilAdmin.001 / TC.ProfilAdmin.001.001 — update profile with valid data (positive)
test('admin can update profile with valid data', function () {
    $admin = profilAdminUser();

    $this->actingAs($admin)->put(route('admin.profil.update'), [
        'email' => 'admin.baru@example.com',
        'whatsapp_number' => '081234567890',
    ])->assertRedirect(route('admin.profil'));

    $admin->refresh();
    expect($admin->email)->toBe('admin.baru@example.com');
    expect($admin->nomor_wa)->toBe('081234567890');
});

// TS.ProfilAdmin.002 / TC.ProfilAdmin.002.001 — email already used by another account (negative)
test('admin cannot update profile with an email already used by another account', function () {
    $admin = profilAdminUser();
    Pengguna::factory()->create(['email' => 'dipakai@example.com']);

    $this->actingAs($admin)->put(route('admin.profil.update'), [
        'email' => 'dipakai@example.com',
    ])->assertSessionHasErrors('email');
});

// TS.ProfilAdmin.003 / TC.ProfilAdmin.003.001 — photo with a disallowed format (gif) is rejected (negative)
test('admin cannot update profile photo with a gif format', function () {
    $admin = profilAdminUser();

    $this->actingAs($admin)->put(route('admin.profil.update'), [
        'email' => $admin->email,
        'photo' => UploadedFile::fake()->image('foto.gif'),
    ])->assertSessionHasErrors('photo');
});

// TS.ProfilAdmin.004 / TC.ProfilAdmin.004.001 — whatsapp_number with invalid characters is rejected (negative)
test('admin cannot update profile with an invalid whatsapp number format', function () {
    $admin = profilAdminUser();

    $this->actingAs($admin)->put(route('admin.profil.update'), [
        'email' => $admin->email,
        'whatsapp_number' => 'abc-nomor-salah',
    ])->assertSessionHasErrors('whatsapp_number');
});

// TS.ProfilAdmin.005 / TC.ProfilAdmin.005.001 — submitting a password on this form has no real effect (positive, dead-validation documented)
test('submitting a password on the profile form does not change the real password', function () {
    $admin = profilAdminUser();

    $this->actingAs($admin)->put(route('admin.profil.update'), [
        'email' => $admin->email,
        'password' => 'BypassPass1',
    ])->assertRedirect(route('admin.profil'));

    expect(Hash::check('OldPassw0rd', $admin->fresh()->password))->toBeTrue();
    expect(Hash::check('BypassPass1', $admin->fresh()->password))->toBeFalse();
});

// TS.ProfilAdmin.006 / TC.ProfilAdmin.006.001 — requesting a password change redirects to the OTP screen (positive)
test('admin requesting password change is redirected to otp screen', function () {
    $admin = profilAdminUser();

    $this->actingAs($admin)->post('/admin/profil/ganti-sandi')
        ->assertRedirect(route('otp.show'));
});

// TS.ProfilAdmin.007 / TC.ProfilAdmin.007.001 — submitting a new password without a verified session is rejected (negative)
test('admin cannot set a new password without a verified otp session', function () {
    $admin = profilAdminUser();

    $this->actingAs($admin)->post('/admin/profil/set-sandi-baru', [
        'password' => 'NewPassw0rd',
        'password_confirmation' => 'NewPassw0rd',
    ])->assertRedirect(route('admin.profil'));

    expect(Hash::check('OldPassw0rd', $admin->fresh()->password))->toBeTrue();
});

// TS.ProfilAdmin.008 / TC.ProfilAdmin.008.001 — submitting a new password with a verified session succeeds (positive)
test('admin can set a new password with a verified otp session', function () {
    $admin = profilAdminUser();
    session(['password_change_verified' => true]);

    $this->actingAs($admin)->post('/admin/profil/set-sandi-baru', [
        'password' => 'NewPassw0rd',
        'password_confirmation' => 'NewPassw0rd',
    ])->assertRedirect(route('admin.profil'));

    expect(Hash::check('NewPassw0rd', $admin->fresh()->password))->toBeTrue();
});

// TS.ProfilAdmin.009 / TC.ProfilAdmin.009.001 — password confirmation mismatch is rejected (negative)
test('admin cannot set a new password when confirmation does not match', function () {
    $admin = profilAdminUser();
    session(['password_change_verified' => true]);

    $this->actingAs($admin)->post('/admin/profil/set-sandi-baru', [
        'password' => 'NewPassw0rd',
        'password_confirmation' => 'BedaSekali9',
    ])->assertSessionHasErrors('password');

    expect(Hash::check('OldPassw0rd', $admin->fresh()->password))->toBeTrue();
});
