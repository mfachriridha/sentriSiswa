<?php

use App\Models\Kelas;
use App\Models\PelanggaranSiswa;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function poinPelanggaranBvaHomeroom(string $className = '10. Poin BVA 1'): array
{
    $teacher = Pengguna::factory()->homeroom()->create(['status' => 'registered']);
    $teacher->profilGuru()->create([
        'nip' => fake()->unique()->numerify('19################'),
        'tipe_guru' => 'wali_kelas',
    ]);
    $class = Kelas::create(['nama' => $className, 'tingkat' => '10', 'wali_kelas_id' => $teacher->id]);

    return [$teacher, $class];
}

function poinPelanggaranBvaStudent(Kelas $class, string $nis): ProfilSiswa
{
    $student = Pengguna::factory()->student()->create(['status' => 'registered']);

    return ProfilSiswa::factory()->create([
        'pengguna_id' => $student->id,
        'kelas_id' => $class->id,
        'nis' => $nis,
    ]);
}

// ── Boundary: date_to, after_or_equal:date_from ────────────────────────────

// TS.PPW.009 / TC.PPW.009.001 — date_to sama dengan date_from (tepat di batas, diperbolehkan)
test('pelanggaran index accepts date_to equal to date_from', function () {
    [$teacher, $class] = poinPelanggaranBvaHomeroom();
    $student = poinPelanggaranBvaStudent($class, '50010');
    PelanggaranSiswa::factory()->create([
        'profil_siswa_id' => $student->nisn,
        'status' => 'approved',
        'tanggal_pelanggaran' => '2026-06-10',
        'nama_pelanggaran' => 'Pelanggaran Batas Sama',
    ]);

    $this->actingAs($teacher)->get(route('wali-kelas.pelanggaran', [
        'date_from' => '2026-06-10',
        'date_to' => '2026-06-10',
    ]))->assertSuccessful()
        ->assertSee('Pelanggaran Batas Sama');
});

// TS.PPW.010 / TC.PPW.010.001 — date_to 1 hari sebelum date_from (di bawah batas, ditolak)
test('pelanggaran index rejects date_to 1 day before date_from', function () {
    [$teacher, $class] = poinPelanggaranBvaHomeroom();

    $this->actingAs($teacher)->get(route('wali-kelas.pelanggaran', [
        'date_from' => '2026-06-10',
        'date_to' => '2026-06-09',
    ]))->assertSessionHasErrors('date_to');
});
