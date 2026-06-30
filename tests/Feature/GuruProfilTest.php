<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('registered teacher can view and update own profile', function () {
    $teacher = Pengguna::factory()->homeroom()->create([
        'email' => 'guru.lama@example.com',
        'password' => Hash::make('password'),
        'status' => 'registered',
    ]);

    $teacher->profilGuru()->create([
        'nip' => '198765432109876543',
        'telepon' => '081234567890',
        'tipe_guru' => 'homeroom',
        'tingkat' => null,
    ]);

    $this->actingAs($teacher)
        ->get(route('wali-kelas.profil'))
        ->assertSuccessful()
        ->assertSee('Profil Saya')
        ->assertSee('198765432109876543')
        ->assertSee('Wali Kelas');

    $this->actingAs($teacher)
        ->put(route('wali-kelas.profil.update'), [
            'nama' => 'Guru Diperbarui',
            'email' => $teacher->email,
            'telepon' => '081298765432',
        ])
        ->assertRedirect(route('wali-kelas.profil'));

    $teacher->refresh();

    expect($teacher->nama)->toBe('Guru Diperbarui');

    $this->assertDatabaseHas('profil_guru', [
        'pengguna_id' => $teacher->id,
        'telepon' => '081298765432',
        'nip' => '198765432109876543',
        'tipe_guru' => 'homeroom',
    ]);
});

test('guru dashboard shows shortcuts for available features', function () {
    $teacher = Pengguna::factory()->homeroom()->create([
        'status' => 'registered',
    ]);

    $teacher->profilGuru()->create([
        'nip' => '198765432109876544',
        'telepon' => '081234567891',
        'tipe_guru' => 'homeroom',
        'tingkat' => null,
    ]);

    $this->actingAs($teacher)
        ->get(route('wali-kelas.dashboard'))
        ->assertSuccessful()
        ->assertSee('Kelas Saya')
        ->assertSee('Rekap Absensi')
        ->assertSee('Profil Saya');
});

test('registered student cannot access guru profile', function () {
    $student = Pengguna::factory()->student()->create([
        'status' => 'registered',
    ]);

    $this->actingAs($student)
        ->get(route('wali-kelas.profil'))
        ->assertForbidden();
});
