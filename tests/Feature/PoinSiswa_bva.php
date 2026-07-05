<?php

use App\Models\JenisPelanggaran;
use App\Models\PelanggaranSiswa;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function poinSiswaBvaStudent(): Pengguna
{
    $student = Pengguna::factory()->student()->create(['status' => 'registered']);
    ProfilSiswa::factory()->create([
        'pengguna_id' => $student->id,
        'nis' => fake()->unique()->numerify('#####'),
    ]);

    return $student;
}

function poinSiswaBvaDeduct(Pengguna $student, int $pengurangan): void
{
    $type = JenisPelanggaran::factory()->create(['kategori' => 'medium', 'pengurangan_poin' => $pengurangan]);
    PelanggaranSiswa::factory()->create([
        'profil_siswa_id' => $student->profilSiswa->nisn,
        'jenis_pelanggaran_id' => $type->id,
        'pengurangan_poin' => $pengurangan,
        'status' => 'approved',
    ]);
}

// ── Boundary: label badge, strict > 75 dan strict > 50 ──────────────────────

// TS.POS.006 / TC.POS.006.001 — sisa poin 76 (1 di atas batas 75, label "Baik")
test('poin siswa shows label Baik at 76 points, 1 above the 75 boundary', function () {
    $student = poinSiswaBvaStudent();
    poinSiswaBvaDeduct($student, 24);

    $this->actingAs($student)->get(route('siswa.poin'))
        ->assertSuccessful()
        ->assertSeeInOrder(['76', 'Baik']);
});

// TS.POS.007 / TC.POS.007.001 — sisa poin 75 (tepat di batas, BUKAN "Baik" tapi "Cukup")
test('poin siswa shows label Cukup at exactly 75 points, the boundary', function () {
    $student = poinSiswaBvaStudent();
    poinSiswaBvaDeduct($student, 25);

    $this->actingAs($student)->get(route('siswa.poin'))
        ->assertSuccessful()
        ->assertSeeInOrder(['75', 'Cukup']);
});

// TS.POS.008 / TC.POS.008.001 — sisa poin 51 (1 di atas batas 50, label "Cukup")
test('poin siswa shows label Cukup at 51 points, 1 above the 50 boundary', function () {
    $student = poinSiswaBvaStudent();
    poinSiswaBvaDeduct($student, 49);

    $this->actingAs($student)->get(route('siswa.poin'))
        ->assertSuccessful()
        ->assertSeeInOrder(['51', 'Cukup']);
});

// TS.POS.009 / TC.POS.009.001 — sisa poin 50 (tepat di batas, BUKAN "Cukup" tapi "Perhatian")
test('poin siswa shows label Perhatian at exactly 50 points, the boundary', function () {
    $student = poinSiswaBvaStudent();
    poinSiswaBvaDeduct($student, 50);

    $this->actingAs($student)->get(route('siswa.poin'))
        ->assertSuccessful()
        ->assertSeeInOrder(['50', 'Perhatian']);
});
