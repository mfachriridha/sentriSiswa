<?php

use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilGuru;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('admin can create a student without a password and gets the default hashed fallback', function () {
    $admin = Pengguna::factory()->admin()->create();
    $class = Kelas::create([
        'nama' => '10. 1',
        'tingkat' => '10',
    ]);

    $response = $this->actingAs($admin)->post(route('admin.siswa.store'), [
        'nama' => 'Siswa Tes',
        'email' => 'siswa.tes@example.com',
        'nisn' => '1234567890',
        'nis' => '12345',
        'kelas_id' => (string) $class->id,
        'telepon' => '081234567890',
        'alamat' => 'Jl. Testing 1',
    ]);

    expect($response->getStatusCode())->toBe(302);
    expect($response->headers->get('Location'))->toBe(route('admin.siswa.index'));

    $student = Pengguna::where('email', 'siswa.tes@example.com')->firstOrFail();

    expect($student->peran)->toBe('siswa');
    expect($student->status)->toBe('unregistered');
    expect(Hash::check('password', $student->password))->toBeTrue();

    $this->assertDatabaseHas('profil_siswa', [
        'pengguna_id' => $student->id,
        'nisn' => '1234567890',
        'kelas_id' => (string) $class->id,
    ]);
    $this->assertDatabaseHas('pengguna', [
        'id' => $student->id,
        'peran' => 'siswa',
        'status' => 'unregistered',
    ]);

    $this->assertNotNull(ProfilSiswa::where('pengguna_id', $student->id)->first());
});

test('admin can create a teacher without a password and gets the default hashed fallback', function () {
    $admin = Pengguna::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('admin.guru.store'), [
        'nama' => 'Guru Tes',
        'email' => 'guru.tes@example.com',
        'nip' => '198765432109876543',
        'telepon' => '081298765432',
        'peran' => 'wali_kelas',
    ]);

    $response->assertRedirect(route('admin.guru.index'));

    $teacher = Pengguna::where('email', 'guru.tes@example.com')->firstOrFail();

    expect($teacher->peran)->toBe('wali_kelas');
    expect($teacher->status)->toBe('unregistered');
    expect(Hash::check('password', $teacher->password))->toBeTrue();

    $this->assertDatabaseHas('profil_guru', [
        'pengguna_id' => $teacher->id,
        'nip' => '198765432109876543',
        'tipe_guru' => 'homeroom',
    ]);
    $this->assertDatabaseHas('pengguna', [
        'id' => $teacher->id,
        'peran' => 'wali_kelas',
        'status' => 'unregistered',
    ]);

    $this->assertNotNull(ProfilGuru::where('pengguna_id', $teacher->id)->first());
});

test('admin can create a student with a manual password and still gets unregistered status', function () {
    $admin = Pengguna::factory()->admin()->create();
    $class = Kelas::create([
        'nama' => '10. 2',
        'tingkat' => '10',
    ]);

    $response = $this->actingAs($admin)->post(route('admin.siswa.store'), [
        'nama' => 'Siswa Manual',
        'email' => 'siswa.manual@example.com',
        'password' => 'Secret123',
        'nisn' => '2234567890',
        'nis' => '54321',
        'kelas_id' => (string) $class->id,
        'telepon' => '081200000000',
        'alamat' => 'Jl. Manual 2',
    ]);

    expect($response->getStatusCode())->toBe(302);
    expect($response->headers->get('Location'))->toBe(route('admin.siswa.index'));

    $student = Pengguna::where('email', 'siswa.manual@example.com')->firstOrFail();

    expect($student->peran)->toBe('siswa');
    expect($student->status)->toBe('unregistered');
    expect(Hash::check('Secret123', $student->password))->toBeTrue();
    expect(Hash::check('password', $student->password))->toBeFalse();

    $this->assertDatabaseHas('profil_siswa', [
        'pengguna_id' => $student->id,
        'nisn' => '2234567890',
        'kelas_id' => (string) $class->id,
    ]);
});

test('admin can create a teacher with a manual password and still gets unregistered status', function () {
    $admin = Pengguna::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('admin.guru.store'), [
        'nama' => 'Guru Manual',
        'email' => 'guru.manual@example.com',
        'password' => 'Secret123',
        'nip' => '198765432109876544',
        'telepon' => '081233344455',
        'peran' => 'bk',
        'tingkat' => '11',
    ]);

    expect($response->getStatusCode())->toBe(302);
    expect($response->headers->get('Location'))->toBe(route('admin.guru.index'));

    $teacher = Pengguna::where('email', 'guru.manual@example.com')->firstOrFail();

    expect($teacher->peran)->toBe('bk');
    expect($teacher->status)->toBe('unregistered');
    expect(Hash::check('Secret123', $teacher->password))->toBeTrue();
    expect(Hash::check('password', $teacher->password))->toBeFalse();

    $this->assertDatabaseHas('profil_guru', [
        'pengguna_id' => $teacher->id,
        'nip' => '198765432109876544',
        'tipe_guru' => 'counselor',
        'tingkat' => '11',
    ]);
});

test('unregistered students are blocked from siswa dashboard by the registered middleware', function () {
    $student = Pengguna::factory()->student()->create([
        'status' => 'unregistered',
        'password' => Hash::make('password'),
    ]);

    $student->profilSiswa()->create([
        'nisn' => '3234567890',
        'nis' => '65432',
        'kelas_id' => null,
        'telepon' => null,
        'alamat' => null,
    ]);

    $this->actingAs($student)
        ->get(route('siswa.dashboard'))
        ->assertRedirect(route('login'))
        ->assertSessionHas('error', 'Akun belum terdaftar. Silakan daftar terlebih dahulu.');

    $this->assertGuest();
});

test('unregistered teachers are blocked from guru dashboard by the registered middleware', function () {
    $teacher = Pengguna::factory()->homeroom()->create([
        'status' => 'unregistered',
        'password' => Hash::make('password'),
    ]);

    $teacher->profilGuru()->create([
        'nip' => '298765432109876544',
        'telepon' => '081244455566',
        'tipe_guru' => 'homeroom',
        'tingkat' => null,
    ]);

    $this->actingAs($teacher)
        ->get(route('guru.dashboard'))
        ->assertRedirect(route('login'))
        ->assertSessionHas('error', 'Akun belum terdaftar. Silakan daftar terlebih dahulu.');

    $this->assertGuest();
});

test('admin can filter students by registration status', function () {
    $admin = Pengguna::factory()->admin()->create();
    $registeredStudent = Pengguna::factory()->student()->create([
        'nama' => 'Siswa Terdaftar',
        'status' => 'registered',
    ]);
    $unregisteredStudent = Pengguna::factory()->student()->create([
        'nama' => 'Siswa Belum Daftar',
        'status' => 'unregistered',
    ]);

    ProfilSiswa::factory()->create(['pengguna_id' => $registeredStudent->id]);
    ProfilSiswa::factory()->create(['pengguna_id' => $unregisteredStudent->id]);

    $this->actingAs($admin)
        ->get(route('admin.siswa.index', ['status' => 'registered']))
        ->assertSuccessful()
        ->assertSee('Siswa Terdaftar')
        ->assertDontSee('Siswa Belum Daftar');

    $this->actingAs($admin)
        ->get(route('admin.siswa.index', ['status' => 'unregistered']))
        ->assertSuccessful()
        ->assertSee('Siswa Belum Daftar')
        ->assertDontSee('Siswa Terdaftar');
});

test('admin can filter teachers by registration status', function () {
    $admin = Pengguna::factory()->admin()->create();
    $registeredTeacher = Pengguna::factory()->homeroom()->create([
        'nama' => 'Guru Terdaftar',
        'status' => 'registered',
    ]);
    $unregisteredTeacher = Pengguna::factory()->homeroom()->create([
        'nama' => 'Guru Belum Daftar',
        'status' => 'unregistered',
    ]);

    ProfilGuru::factory()->create([
        'pengguna_id' => $registeredTeacher->id,
        'tipe_guru' => 'homeroom',
    ]);
    ProfilGuru::factory()->create([
        'pengguna_id' => $unregisteredTeacher->id,
        'tipe_guru' => 'homeroom',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.guru.index', ['status' => 'registered']))
        ->assertSuccessful()
        ->assertSee('Guru Terdaftar')
        ->assertDontSee('Guru Belum Daftar');

    $this->actingAs($admin)
        ->get(route('admin.guru.index', ['status' => 'unregistered']))
        ->assertSuccessful()
        ->assertSee('Guru Belum Daftar')
        ->assertDontSee('Guru Terdaftar');
});
