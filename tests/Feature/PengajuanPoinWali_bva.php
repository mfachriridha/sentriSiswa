<?php

use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function pengajuanPoinWaliBvaHomeroom(string $className = '10. Pengajuan BVA 1'): array
{
    $teacher = Pengguna::factory()->homeroom()->create(['status' => 'registered']);
    $teacher->profilGuru()->create([
        'nip' => fake()->unique()->numerify('19################'),
        'tipe_guru' => 'wali_kelas',
    ]);
    $class = Kelas::create(['nama' => $className, 'tingkat' => '10', 'wali_kelas_id' => $teacher->id]);

    return [$teacher, $class];
}

function pengajuanPoinWaliBvaStudent(Kelas $class, string $nis): ProfilSiswa
{
    $student = Pengguna::factory()->student()->create(['status' => 'registered']);

    return ProfilSiswa::factory()->create([
        'pengguna_id' => $student->id,
        'kelas_id' => $class->id,
        'nis' => $nis,
    ]);
}

// ── Boundary: alasan length, max:1000 ─────────────────────────────────────

// TS.PGP.010 / TC.PGP.010.001 — alasan 1001 karakter (tepat di atas batas maksimum, ditolak)
test('homeroom teacher cannot submit a point-addition request with 1001 character alasan', function () {
    [$teacher, $class] = pengajuanPoinWaliBvaHomeroom();
    $student = pengajuanPoinWaliBvaStudent($class, '70001');
    $alasan1001 = str_repeat('a', 1001);

    $this->actingAs($teacher)->post(route('wali-kelas.pengajuan-poin.store'), [
        'profil_siswa_id' => $student->nisn,
        'alasan' => $alasan1001,
    ])->assertSessionHasErrors('alasan');
});

// TS.PGP.011 / TC.PGP.011.001 — alasan tepat 1000 karakter (tepat di batas maksimum, diperbolehkan)
test('homeroom teacher can submit a point-addition request with exactly 1000 character alasan', function () {
    [$teacher, $class] = pengajuanPoinWaliBvaHomeroom();
    $student = pengajuanPoinWaliBvaStudent($class, '70002');
    $alasan1000 = str_repeat('a', 1000);

    $this->actingAs($teacher)->post(route('wali-kelas.pengajuan-poin.store'), [
        'profil_siswa_id' => $student->nisn,
        'alasan' => $alasan1000,
    ])->assertRedirect(route('wali-kelas.pengajuan-poin.index'));
});
