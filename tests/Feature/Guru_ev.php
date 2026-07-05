<?php

use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilGuru;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

function guruAdmin(): Pengguna
{
    return Pengguna::factory()->admin()->create(['status' => 'registered']);
}

// TS.GUR.001 / TC.GUR.001.001 — create teacher with valid data, password always defaults to "password" (positive)
test('admin can create a teacher with valid data', function () {
    $admin = guruAdmin();

    $this->actingAs($admin)->post(route('admin.guru.store'), [
        'nama' => 'Guru Baru',
        'nip' => '198501012020121001',
        'telepon' => '081234567890',
        'peran' => 'wali_kelas',
    ])->assertRedirect(route('admin.guru.index'));

    $teacher = Pengguna::where('nama', 'Guru Baru')->firstOrFail();
    expect($teacher->peran)->toBe('wali_kelas');
    expect($teacher->status)->toBe('unregistered');
    expect(Hash::check('password', $teacher->password))->toBeTrue();
    $this->assertDatabaseHas('profil_guru', [
        'pengguna_id' => $teacher->id,
        'nip' => '198501012020121001',
        'tipe_guru' => 'wali_kelas',
    ]);
});

// TS.GUR.002 / TC.GUR.002.001 — create teacher with a duplicate nip (negative)
test('admin cannot create a teacher with duplicate nip', function () {
    $admin = guruAdmin();
    $existing = Pengguna::factory()->homeroom()->create();
    ProfilGuru::factory()->create(['pengguna_id' => $existing->id, 'nip' => '198501012020121002']);

    $this->actingAs($admin)->post(route('admin.guru.store'), [
        'nama' => 'Guru Duplikat',
        'nip' => '198501012020121002',
        'peran' => 'wali_kelas',
    ])->assertSessionHasErrors('nip');
});

// TS.GUR.003 / TC.GUR.003.001 — create teacher with nama containing digits, fails the letters-only regex (negative)
test('admin cannot create a teacher with a name containing digits', function () {
    $admin = guruAdmin();

    $this->actingAs($admin)->post(route('admin.guru.store'), [
        'nama' => 'Guru123',
        'nip' => '198501012020121003',
        'peran' => 'wali_kelas',
    ])->assertSessionHasErrors('nama');
});

// TS.GUR.004 / TC.GUR.004.001 — create teacher with an invalid peran value (negative)
test('admin cannot create a teacher with an invalid peran', function () {
    $admin = guruAdmin();

    $this->actingAs($admin)->post(route('admin.guru.store'), [
        'nama' => 'Guru Peran Salah',
        'nip' => '198501012020121004',
        'peran' => 'admin',
    ])->assertSessionHasErrors('peran');
});

// TS.GUR.005 / TC.GUR.005.001 — tingkat submitted while peran is not bk is forced to null, not an error (positive, business rule)
test('tingkat is forced to null when creating a teacher whose peran is not bk', function () {
    $admin = guruAdmin();

    $this->actingAs($admin)->post(route('admin.guru.store'), [
        'nama' => 'Guru Wali Kelas',
        'nip' => '198501012020121005',
        'peran' => 'wali_kelas',
        'tingkat' => '11',
    ])->assertRedirect(route('admin.guru.index'));

    $teacher = Pengguna::where('nama', 'Guru Wali Kelas')->firstOrFail();
    expect($teacher->profilGuru->tingkat)->toBeNull();
});

// TS.GUR.006 / TC.GUR.006.001 — submitting a password field directly is ignored, saved password stays the default (positive, dead-field robustness)
test('admin submitted password field is ignored when creating a teacher', function () {
    $admin = guruAdmin();

    $this->actingAs($admin)->post(route('admin.guru.store'), [
        'nama' => 'Guru Bypass',
        'nip' => '198501012020121006',
        'peran' => 'wali_kelas',
        'password' => 'Secret123',
    ])->assertRedirect(route('admin.guru.index'));

    $teacher = Pengguna::where('nama', 'Guru Bypass')->firstOrFail();
    expect(Hash::check('password', $teacher->password))->toBeTrue();
    expect(Hash::check('Secret123', $teacher->password))->toBeFalse();
});

// TS.GUR.007 / TC.GUR.007.001 — update teacher succeeds (positive)
test('admin can update a teacher', function () {
    $admin = guruAdmin();
    $teacher = Pengguna::factory()->homeroom()->create(['nama' => 'Nama Lama']);
    ProfilGuru::factory()->create(['pengguna_id' => $teacher->id, 'nip' => '198501012020121007']);

    $this->actingAs($admin)->put(route('admin.guru.update', $teacher), [
        'nama' => 'Nama Baru',
        'nip' => '198501012020121007',
        'peran' => 'wali_kelas',
        'telepon' => '081200000007',
    ])->assertRedirect(route('admin.guru.index'));

    expect($teacher->fresh()->nama)->toBe('Nama Baru');
    expect($teacher->fresh()->profilGuru->telepon)->toBe('081200000007');
});

// TS.GUR.008 / TC.GUR.008.001 — delete teacher succeeds (positive)
test('admin can delete a teacher', function () {
    $admin = guruAdmin();
    $teacher = Pengguna::factory()->homeroom()->create();
    ProfilGuru::factory()->create(['pengguna_id' => $teacher->id]);

    $this->actingAs($admin)->delete(route('admin.guru.destroy', $teacher))
        ->assertRedirect(route('admin.guru.index'));

    $this->assertDatabaseMissing('pengguna', ['id' => $teacher->id]);
});

// TS.GUR.009 / TC.GUR.009.001 — unregistered teacher is blocked from the guru dashboard (negative)
test('unregistered teacher is blocked from guru dashboard', function () {
    $teacher = Pengguna::factory()->homeroom()->create([
        'status' => 'unregistered',
        'password' => Hash::make('password'),
    ]);
    ProfilGuru::factory()->create(['pengguna_id' => $teacher->id]);

    $this->actingAs($teacher)
        ->get(route('wali-kelas.dashboard'))
        ->assertRedirect(route('login'))
        ->assertSessionHas('error', 'Akun belum terdaftar. Silakan daftar terlebih dahulu.');

    $this->assertGuest();
});

// TS.GUR.010 / TC.GUR.010.001 — admin filters teacher index by registration status (positive)
test('admin can filter teachers by registration status', function () {
    $admin = guruAdmin();
    $registered = Pengguna::factory()->homeroom()->create(['nama' => 'Guru Terdaftar', 'status' => 'registered']);
    $unregistered = Pengguna::factory()->homeroom()->create(['nama' => 'Guru Belum Daftar', 'status' => 'unregistered']);
    ProfilGuru::factory()->create(['pengguna_id' => $registered->id]);
    ProfilGuru::factory()->create(['pengguna_id' => $unregistered->id]);

    $this->actingAs($admin)
        ->get(route('admin.guru.index', ['status' => 'registered']))
        ->assertSuccessful()
        ->assertSee('Guru Terdaftar')
        ->assertDontSee('Guru Belum Daftar');
});

// TS.GUR.011 / TC.GUR.011.001 — admin assigns a kelas directly while creating a wali_kelas teacher (positive)
test('admin can assign a kelas while creating a wali_kelas teacher', function () {
    $admin = guruAdmin();
    $kelas = Kelas::create(['nama' => '10 IPA', 'tingkat' => '10']);

    $this->actingAs($admin)->post(route('admin.guru.store'), [
        'nama' => 'Guru Wali Baru',
        'nip' => '198501012020121011',
        'peran' => 'wali_kelas',
        'kelas_id' => $kelas->id,
    ])->assertRedirect(route('admin.guru.index'));

    $teacher = Pengguna::where('nama', 'Guru Wali Baru')->firstOrFail();
    expect($kelas->fresh()->wali_kelas_id)->toBe($teacher->id);
});

// TS.GUR.012 / TC.GUR.012.001 — admin cannot assign a kelas that already has a wali kelas (negative)
test('admin cannot assign a kelas that already has a wali kelas', function () {
    $admin = guruAdmin();
    $existingWali = Pengguna::factory()->homeroom()->create();
    $kelas = Kelas::create(['nama' => '10 IPS', 'tingkat' => '10', 'wali_kelas_id' => $existingWali->id]);

    $this->actingAs($admin)->post(route('admin.guru.store'), [
        'nama' => 'Guru Rebutan Kelas',
        'nip' => '198501012020121012',
        'peran' => 'wali_kelas',
        'kelas_id' => $kelas->id,
    ])->assertSessionHasErrors('kelas_id');

    expect($kelas->fresh()->wali_kelas_id)->toBe($existingWali->id);
});

// TS.GUR.013 / TC.GUR.013.001 — updating a teacher's kelas releases the old one and assigns the new one (positive)
test('admin can change a wali_kelas teacher kelas assignment on update', function () {
    $admin = guruAdmin();
    $teacher = Pengguna::factory()->homeroom()->create();
    ProfilGuru::factory()->create(['pengguna_id' => $teacher->id, 'nip' => '198501012020121013']);
    $kelasLama = Kelas::create(['nama' => '11 IPA', 'tingkat' => '11', 'wali_kelas_id' => $teacher->id]);
    $kelasBaru = Kelas::create(['nama' => '12 IPA', 'tingkat' => '12']);

    $this->actingAs($admin)->put(route('admin.guru.update', $teacher), [
        'nama' => $teacher->nama,
        'nip' => '198501012020121013',
        'peran' => 'wali_kelas',
        'kelas_id' => $kelasBaru->id,
    ])->assertRedirect(route('admin.guru.index'));

    expect($kelasLama->fresh()->wali_kelas_id)->toBeNull();
    expect($kelasBaru->fresh()->wali_kelas_id)->toBe($teacher->id);
});
