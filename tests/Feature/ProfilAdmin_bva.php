<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function profilAdminBvaUser(): Pengguna
{
    return Pengguna::factory()->admin()->create([
        'status' => 'registered',
        'email' => 'admin.bva@example.com',
        'password' => Hash::make('OldPassw0rd'),
    ]);
}

function profilAdminBvaImageOfSize(int $bytes): UploadedFile
{
    $image = imagecreatetruecolor(50, 50);
    imagefill($image, 0, 0, imagecolorallocate($image, 100, 150, 200));
    ob_start();
    imagejpeg($image, null, 90);
    $jpeg = ob_get_clean();
    imagedestroy($image);

    if (strlen($jpeg) < $bytes) {
        $jpeg .= str_repeat("\0", $bytes - strlen($jpeg));
    }

    $path = tempnam(sys_get_temp_dir(), 'img');
    file_put_contents($path, $jpeg);

    return new UploadedFile($path, 'foto.jpg', null, null, true);
}

// ── Boundary: whatsapp_number length, min:10 / max:20 ─────────────────────

// TS.ProfilAdmin.010 / TC.ProfilAdmin.010.001 — whatsapp_number with 9 characters (just below the minimum of 10, invalid)
test('admin cannot update profile with a 9 character whatsapp number', function () {
    $admin = profilAdminBvaUser();

    $this->actingAs($admin)->put(route('admin.profil.update'), [
        'email' => $admin->email,
        'whatsapp_number' => '081234567',
    ])->assertSessionHasErrors('whatsapp_number');
});

// TS.ProfilAdmin.011 / TC.ProfilAdmin.011.001 — whatsapp_number with exactly 10 characters (at the minimum, valid)
test('admin can update profile with a 10 character whatsapp number', function () {
    $admin = profilAdminBvaUser();

    $this->actingAs($admin)->put(route('admin.profil.update'), [
        'email' => $admin->email,
        'whatsapp_number' => '0812345678',
    ])->assertRedirect(route('admin.profil'));

    expect($admin->fresh()->nomor_wa)->toBe('0812345678');
});

// TS.ProfilAdmin.012 / TC.ProfilAdmin.012.001 — whatsapp_number with exactly 20 characters (at the maximum, valid)
test('admin can update profile with a 20 character whatsapp number', function () {
    $admin = profilAdminBvaUser();
    $nomor20 = str_repeat('0', 20);

    $this->actingAs($admin)->put(route('admin.profil.update'), [
        'email' => $admin->email,
        'whatsapp_number' => $nomor20,
    ])->assertRedirect(route('admin.profil'));

    expect($admin->fresh()->nomor_wa)->toBe($nomor20);
});

// TS.ProfilAdmin.013 / TC.ProfilAdmin.013.001 — whatsapp_number with 21 characters (just above the maximum, invalid)
test('admin cannot update profile with a 21 character whatsapp number', function () {
    $admin = profilAdminBvaUser();
    $nomor21 = str_repeat('0', 21);

    $this->actingAs($admin)->put(route('admin.profil.update'), [
        'email' => $admin->email,
        'whatsapp_number' => $nomor21,
    ])->assertSessionHasErrors('whatsapp_number');
});

// ── Boundary: set-sandi-baru password length, min:8 ───────────────────────

// TS.ProfilAdmin.014 / TC.ProfilAdmin.014.001 — new password with 7 characters (just below the minimum of 8, invalid)
test('admin cannot set a new password with 7 characters', function () {
    $admin = profilAdminBvaUser();
    session(['password_change_verified' => true]);

    $this->actingAs($admin)->post('/admin/profil/set-sandi-baru', [
        'password' => 'Passw0r',
        'password_confirmation' => 'Passw0r',
    ])->assertSessionHasErrors('password');
});

// TS.ProfilAdmin.015 / TC.ProfilAdmin.015.001 — new password with exactly 8 characters (at the minimum, valid)
test('admin can set a new password with exactly 8 characters', function () {
    $admin = profilAdminBvaUser();
    session(['password_change_verified' => true]);

    $this->actingAs($admin)->post('/admin/profil/set-sandi-baru', [
        'password' => 'Passw0rd',
        'password_confirmation' => 'Passw0rd',
    ])->assertRedirect(route('admin.profil'));

    expect(Hash::check('Passw0rd', $admin->fresh()->password))->toBeTrue();
});

// ── Boundary: photo size, max:2048 KB ─────────────────────────────────────

// TS.ProfilAdmin.016 / TC.ProfilAdmin.016.001 — photo size exactly 2048 KB (at the maximum, valid)
test('admin can upload a profile photo at exactly the maximum size', function () {
    $admin = profilAdminBvaUser();

    $this->actingAs($admin)->put(route('admin.profil.update'), [
        'email' => $admin->email,
        'photo' => profilAdminBvaImageOfSize(2048 * 1024),
    ])->assertRedirect(route('admin.profil'));
});

// TS.ProfilAdmin.017 / TC.ProfilAdmin.017.001 — photo size 1 byte above the 2048 KB maximum (invalid)
test('admin cannot upload a profile photo above the maximum size', function () {
    $admin = profilAdminBvaUser();

    $this->actingAs($admin)->put(route('admin.profil.update'), [
        'email' => $admin->email,
        'photo' => profilAdminBvaImageOfSize(2048 * 1024 + 1),
    ])->assertSessionHasErrors('photo');
});
