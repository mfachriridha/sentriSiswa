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
        ->get(route('guru.profil'))
        ->assertSuccessful()
        ->assertSee('Profil Saya')
        ->assertSee('198765432109876543')
        ->assertSee('Wali Kelas');

    $this->actingAs($teacher)
        ->put(route('guru.profil.update'), [
            'nama' => $teacher->nama,
            'email' => 'guru.baru@example.com',
            'telepon' => '081298765432',
            'password' => 'Secret123',
        ])
        ->assertRedirect(route('guru.profil'));

    $teacher->refresh();

    expect($teacher->email)->toBe('guru.baru@example.com');
    expect(Hash::check('Secret123', $teacher->password))->toBeTrue();

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
        ->get(route('guru.dashboard'))
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
        ->get(route('guru.profil'))
        ->assertForbidden();
});
