<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function rekapAbsensiBkBvaCounselor(): Pengguna
{
    $counselor = Pengguna::factory()->counselor()->create(['status' => 'registered']);
    $counselor->profilGuru()->create([
        'nip' => fake()->unique()->numerify('19################'),
        'tipe_guru' => 'counselor',
        'tingkat' => '10',
    ]);

    return $counselor;
}

// ── Boundary: selesai, after_or_equal:mulai ─────────────────────────────────

// TS.RAB.012 / TC.RAB.012.001 — selesai sama dengan mulai (tepat di batas, diperbolehkan)
test('bk rekap absensi accepts selesai equal to mulai', function () {
    $counselor = rekapAbsensiBkBvaCounselor();

    $this->actingAs($counselor)->get(route('bk.laporan.index', [
        'mulai' => '2026-06-15',
        'selesai' => '2026-06-15',
    ]))->assertSuccessful()->assertSessionHasNoErrors();
});

// TS.RAB.013 / TC.RAB.013.001 — selesai 1 hari sebelum mulai (di bawah batas, ditolak)
test('bk rekap absensi rejects selesai 1 day before mulai', function () {
    $counselor = rekapAbsensiBkBvaCounselor();

    $this->actingAs($counselor)->get(route('bk.laporan.index', [
        'mulai' => '2026-06-15',
        'selesai' => '2026-06-14',
    ]))->assertSessionHasErrors('selesai');
});
