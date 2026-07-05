<?php

use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function siswaAdmin(): Pengguna
{
    return Pengguna::factory()->admin()->create(['status' => 'registered']);
}

// TS.SIS.001 / TC.SIS.001.001 — create student with valid data, password always defaults to "password" (positive)
test('admin can create a student with valid data', function () {
    $admin = siswaAdmin();
    $class = Kelas::create(['nama' => '10. 1', 'tingkat' => '10']);

    $this->actingAs($admin)->post(route('admin.siswa.store'), [
        'nama' => 'Siswa Baru',
        'nisn' => '1234567890',
        'nis' => '10001',
        'kelas_id' => (string) $class->id,
        'telepon' => '081234567890',
        'alamat' => 'Jl. Testing 1',
    ])->assertRedirect(route('admin.siswa.index'));

    $student = Pengguna::where('nama', 'Siswa Baru')->firstOrFail();
    expect($student->peran)->toBe('siswa');
    expect($student->status)->toBe('unregistered');
    expect(Hash::check('password', $student->password))->toBeTrue();
    $this->assertDatabaseHas('profil_siswa', [
        'pengguna_id' => $student->id,
        'nisn' => '1234567890',
        'kelas_id' => (string) $class->id,
    ]);
});

// TS.SIS.002 / TC.SIS.002.001 — create student with a duplicate nisn (negative)
test('admin cannot create a student with duplicate nisn', function () {
    $admin = siswaAdmin();
    $existing = Pengguna::factory()->student()->create();
    ProfilSiswa::factory()->create(['pengguna_id' => $existing->id, 'nisn' => '2234567890']);

    $this->actingAs($admin)->post(route('admin.siswa.store'), [
        'nama' => 'Siswa Duplikat',
        'nisn' => '2234567890',
        'nis' => '10002',
    ])->assertSessionHasErrors('nisn');
});

// TS.SIS.003 / TC.SIS.003.001 — create student with a duplicate nis (negative)
test('admin cannot create a student with duplicate nis', function () {
    $admin = siswaAdmin();
    $existing = Pengguna::factory()->student()->create();
    ProfilSiswa::factory()->create(['pengguna_id' => $existing->id, 'nis' => '20002']);

    $this->actingAs($admin)->post(route('admin.siswa.store'), [
        'nama' => 'Siswa Duplikat Nis',
        'nisn' => '3234567890',
        'nis' => '20002',
    ])->assertSessionHasErrors('nis');
});

// TS.SIS.004 / TC.SIS.004.001 — create student with nama containing digits, fails the letters-only regex (negative)
test('admin cannot create a student with a name containing digits', function () {
    $admin = siswaAdmin();

    $this->actingAs($admin)->post(route('admin.siswa.store'), [
        'nama' => 'Siswa123',
        'nisn' => '4234567890',
        'nis' => '10004',
    ])->assertSessionHasErrors('nama');
});

// TS.SIS.005 / TC.SIS.005.001 — create student with a kelas_id that does not exist (negative)
test('admin cannot create a student with a non-existent kelas_id', function () {
    $admin = siswaAdmin();

    $this->actingAs($admin)->post(route('admin.siswa.store'), [
        'nama' => 'Siswa Kelas Salah',
        'nisn' => '5234567890',
        'nis' => '10005',
        'kelas_id' => 'kelas-tidak-ada',
    ])->assertSessionHasErrors('kelas_id');
});

// TS.SIS.006 / TC.SIS.006.001 — submitting a password field directly is ignored, saved password stays the default (positive, dead-field robustness)
test('admin submitted password field is ignored when creating a student', function () {
    $admin = siswaAdmin();

    $this->actingAs($admin)->post(route('admin.siswa.store'), [
        'nama' => 'Siswa Bypass',
        'nisn' => '6234567890',
        'nis' => '10006',
        'password' => 'Secret123',
    ])->assertRedirect(route('admin.siswa.index'));

    $student = Pengguna::where('nama', 'Siswa Bypass')->firstOrFail();
    expect(Hash::check('password', $student->password))->toBeTrue();
    expect(Hash::check('Secret123', $student->password))->toBeFalse();
});

// TS.SIS.007 / TC.SIS.007.001 — update student succeeds (positive)
test('admin can update a student', function () {
    $admin = siswaAdmin();
    $student = Pengguna::factory()->student()->create(['nama' => 'Nama Lama']);
    ProfilSiswa::factory()->create(['pengguna_id' => $student->id, 'nisn' => '7234567890', 'nis' => '10007']);

    $this->actingAs($admin)->put(route('admin.siswa.update', $student), [
        'nama' => 'Nama Baru',
        'nisn' => '7234567890',
        'nis' => '10007',
        'telepon' => '081200000007',
    ])->assertRedirect(route('admin.siswa.index'));

    expect($student->fresh()->nama)->toBe('Nama Baru');
    expect($student->fresh()->profilSiswa->telepon)->toBe('081200000007');
});

// TS.SIS.008 / TC.SIS.008.001 — delete student succeeds (positive)
test('admin can delete a student', function () {
    $admin = siswaAdmin();
    $student = Pengguna::factory()->student()->create();
    ProfilSiswa::factory()->create(['pengguna_id' => $student->id]);

    $this->actingAs($admin)->delete(route('admin.siswa.destroy', $student))
        ->assertRedirect(route('admin.siswa.index'));

    $this->assertDatabaseMissing('pengguna', ['id' => $student->id]);
});

// TS.SIS.009 / TC.SIS.009.001 — unregistered student is blocked from the siswa dashboard (negative)
test('unregistered student is blocked from siswa dashboard', function () {
    $student = Pengguna::factory()->student()->create([
        'status' => 'unregistered',
        'password' => Hash::make('password'),
    ]);
    ProfilSiswa::factory()->create(['pengguna_id' => $student->id]);

    $this->actingAs($student)
        ->get(route('siswa.dashboard'))
        ->assertRedirect(route('login'))
        ->assertSessionHas('error', 'Akun belum terdaftar. Silakan daftar terlebih dahulu.');

    $this->assertGuest();
});

// TS.SIS.010 / TC.SIS.010.001 — admin filters student index by registration status (positive)
test('admin can filter students by registration status', function () {
    $admin = siswaAdmin();
    $registered = Pengguna::factory()->student()->create(['nama' => 'Siswa Terdaftar', 'status' => 'registered']);
    $unregistered = Pengguna::factory()->student()->create(['nama' => 'Siswa Belum Daftar', 'status' => 'unregistered']);
    ProfilSiswa::factory()->create(['pengguna_id' => $registered->id]);
    ProfilSiswa::factory()->create(['pengguna_id' => $unregistered->id]);

    $this->actingAs($admin)
        ->get(route('admin.siswa.index', ['status' => 'registered']))
        ->assertSuccessful()
        ->assertSee('Siswa Terdaftar')
        ->assertDontSee('Siswa Belum Daftar');
});
