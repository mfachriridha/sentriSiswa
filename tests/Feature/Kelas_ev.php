<?php

use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function kelasAdmin(): Pengguna
{
    return Pengguna::factory()->admin()->create(['status' => 'registered']);
}

// TS.KEL.001 / TC.KEL.001.001 — create class with a wali kelas assigned (positive)
test('admin can create a class with a homeroom teacher', function () {
    $admin = kelasAdmin();
    $wali = Pengguna::factory()->homeroom()->create();

    $this->actingAs($admin)->post(route('admin.kelas.store'), [
        'nama' => '1',
        'tingkat' => '10',
        'wali_kelas_id' => (string) $wali->id,
    ])->assertRedirect(route('admin.kelas.index'));

    $this->assertDatabaseHas('kelas', [
        'nama' => '10. 1',
        'tingkat' => '10',
        'wali_kelas_id' => (string) $wali->id,
    ]);
});

// TS.KEL.002 / TC.KEL.002.001 — create class with a duplicate nama+tingkat combination (negative)
test('admin cannot create a class with a duplicate nama and tingkat combination', function () {
    $admin = kelasAdmin();
    Kelas::create(['nama' => '10. 2', 'tingkat' => '10']);

    $this->actingAs($admin)->post(route('admin.kelas.store'), [
        'nama' => '2',
        'tingkat' => '10',
    ])->assertSessionHasErrors('nama');

    expect(Kelas::where('nama', '10. 2')->where('tingkat', '10')->count())->toBe(1);
});

// TS.KEL.003 / TC.KEL.003.001 — create class with an invalid tingkat (negative)
test('admin cannot create a class with an invalid tingkat', function () {
    $admin = kelasAdmin();

    $this->actingAs($admin)->post(route('admin.kelas.store'), [
        'nama' => '3',
        'tingkat' => '13',
    ])->assertSessionHasErrors('tingkat');
});

// TS.KEL.004 / TC.KEL.004.001 — create class with a wali_kelas_id that does not exist (negative)
test('admin cannot create a class with a non-existent wali kelas', function () {
    $admin = kelasAdmin();

    $this->actingAs($admin)->post(route('admin.kelas.store'), [
        'nama' => '4',
        'tingkat' => '10',
        'wali_kelas_id' => 'guru-tidak-ada',
    ])->assertSessionHasErrors('wali_kelas_id');
});

// TS.KEL.005 / TC.KEL.005.001 — create class without a wali kelas, saved as null (positive, nullable)
test('admin can create a class without a homeroom teacher', function () {
    $admin = kelasAdmin();

    $this->actingAs($admin)->post(route('admin.kelas.store'), [
        'nama' => '5',
        'tingkat' => '10',
    ])->assertRedirect(route('admin.kelas.index'));

    $this->assertDatabaseHas('kelas', [
        'nama' => '10. 5',
        'tingkat' => '10',
        'wali_kelas_id' => null,
    ]);
});

// TS.KEL.006 / TC.KEL.006.001 — update class succeeds (positive)
test('admin can update a class', function () {
    $admin = kelasAdmin();
    $class = Kelas::create(['nama' => '10. 6', 'tingkat' => '10']);
    $wali = Pengguna::factory()->homeroom()->create();

    $this->actingAs($admin)->put(route('admin.kelas.update', $class), [
        'nama' => '6B',
        'tingkat' => '11',
        'wali_kelas_id' => (string) $wali->id,
    ])->assertRedirect(route('admin.kelas.index'));

    expect($class->fresh()->nama)->toBe('11 6B');
    expect($class->fresh()->tingkat)->toBe('11');
    expect($class->fresh()->wali_kelas_id)->toEqual($wali->id);
});

// TS.KEL.007 / TC.KEL.007.001 — delete class succeeds (positive)
test('admin can delete a class', function () {
    $admin = kelasAdmin();
    $class = Kelas::create(['nama' => '10. 7', 'tingkat' => '10']);

    $this->actingAs($admin)->delete(route('admin.kelas.destroy', $class))
        ->assertRedirect(route('admin.kelas.index'));

    $this->assertDatabaseMissing('kelas', ['id' => $class->id]);
});

// TS.KEL.008 / TC.KEL.008.001 — admin assigns multiple unassigned students to a new class at once (positive)
test('admin can bulk-assign multiple unassigned students while creating a class', function () {
    $admin = kelasAdmin();
    $siswaA = Pengguna::factory()->student()->create();
    $profilA = ProfilSiswa::factory()->create(['pengguna_id' => $siswaA->id, 'nisn' => '1111111181', 'kelas_id' => null]);
    $siswaB = Pengguna::factory()->student()->create();
    $profilB = ProfilSiswa::factory()->create(['pengguna_id' => $siswaB->id, 'nisn' => '1111111182', 'kelas_id' => null]);

    $this->actingAs($admin)->post(route('admin.kelas.store'), [
        'nama' => '8',
        'tingkat' => '10',
        'siswa_nisn' => [$profilA->nisn, $profilB->nisn],
    ])->assertRedirect(route('admin.kelas.index'));

    $kelas = Kelas::where('nama', '10. 8')->firstOrFail();
    expect($profilA->fresh()->kelas_id)->toBe($kelas->id);
    expect($profilB->fresh()->kelas_id)->toBe($kelas->id);
});

// TS.KEL.009 / TC.KEL.009.001 — admin cannot bulk-assign a student who already belongs to another class (negative)
test('admin cannot bulk-assign a student who already has a class', function () {
    $admin = kelasAdmin();
    $existingKelas = Kelas::create(['nama' => '10. 9a', 'tingkat' => '10']);
    $siswa = Pengguna::factory()->student()->create();
    $profil = ProfilSiswa::factory()->create(['pengguna_id' => $siswa->id, 'nisn' => '1111111183', 'kelas_id' => $existingKelas->id]);

    $this->actingAs($admin)->post(route('admin.kelas.store'), [
        'nama' => '9b',
        'tingkat' => '10',
        'siswa_nisn' => [$profil->nisn],
    ])->assertSessionHasErrors('siswa_nisn.0');

    expect($profil->fresh()->kelas_id)->toBe($existingKelas->id);
});

// TS.KEL.010 / TC.KEL.010.001 — updating a class releases unchecked students and keeps only the checked ones (positive)
test('admin can add and remove students when updating a class', function () {
    $admin = kelasAdmin();
    $kelas = Kelas::create(['nama' => '10. 10', 'tingkat' => '10']);

    $siswaStay = Pengguna::factory()->student()->create();
    $profilStay = ProfilSiswa::factory()->create(['pengguna_id' => $siswaStay->id, 'nisn' => '1111111184', 'kelas_id' => $kelas->id]);
    $siswaRemoved = Pengguna::factory()->student()->create();
    $profilRemoved = ProfilSiswa::factory()->create(['pengguna_id' => $siswaRemoved->id, 'nisn' => '1111111185', 'kelas_id' => $kelas->id]);
    $siswaAdded = Pengguna::factory()->student()->create();
    $profilAdded = ProfilSiswa::factory()->create(['pengguna_id' => $siswaAdded->id, 'nisn' => '1111111186', 'kelas_id' => null]);

    $this->actingAs($admin)->put(route('admin.kelas.update', $kelas), [
        'nama' => '10',
        'tingkat' => '10',
        'siswa_nisn' => [$profilStay->nisn, $profilAdded->nisn],
    ])->assertRedirect(route('admin.kelas.index'));

    expect($profilStay->fresh()->kelas_id)->toBe($kelas->id);
    expect($profilAdded->fresh()->kelas_id)->toBe($kelas->id);
    expect($profilRemoved->fresh()->kelas_id)->toBeNull();
});
