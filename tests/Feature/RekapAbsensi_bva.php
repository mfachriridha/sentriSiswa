<?php

use App\Models\Kelas;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function rekapAbsensiBvaHomeroom(string $className = '10. Rekap BVA 1'): array
{
    $teacher = Pengguna::factory()->homeroom()->create(['status' => 'registered']);
    $teacher->profilGuru()->create([
        'nip' => fake()->unique()->numerify('19################'),
        'tipe_guru' => 'homeroom',
    ]);
    $class = Kelas::create(['nama' => $className, 'tingkat' => '10', 'wali_kelas_id' => $teacher->id]);

    return [$teacher, $class];
}

// ── Boundary: selesai vs mulai, rule after_or_equal:mulai ─────────────────

// TS.REA.012 / TC.REA.012.001 — selesai sama dengan mulai (tepat di batas, diperbolehkan)
test('rekap absensi accepts selesai equal to mulai', function () {
    [$teacher, $class] = rekapAbsensiBvaHomeroom();

    $this->actingAs($teacher)->get(route('wali-kelas.absensi.index', [
        'mulai' => '2026-06-01',
        'selesai' => '2026-06-01',
    ]))->assertSuccessful()
        ->assertSessionDoesntHaveErrors('selesai');
});

// TS.REA.013 / TC.REA.013.001 — selesai 1 hari sebelum mulai (tepat di bawah batas, ditolak)
test('rekap absensi rejects selesai one day before mulai', function () {
    [$teacher, $class] = rekapAbsensiBvaHomeroom();

    $this->actingAs($teacher)->get(route('wali-kelas.absensi.index', [
        'mulai' => '2026-06-02',
        'selesai' => '2026-06-01',
    ]))->assertSessionHasErrors('selesai');
});
