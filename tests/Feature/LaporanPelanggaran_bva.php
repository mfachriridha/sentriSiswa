<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function laporanPelanggaranBvaActor(): Pengguna
{
    $studentAffairs = Pengguna::factory()->studentAffairs()->create(['status' => 'registered']);
    $studentAffairs->profilGuru()->create([
        'nip' => fake()->unique()->numerify('19################'),
        'tipe_guru' => 'student_affairs',
    ]);

    return $studentAffairs;
}

// ── Boundary: selesai, after_or_equal:mulai ─────────────────────────────────

// TS.LAP.012 / TC.LAP.012.001 — selesai sama dengan mulai (tepat di batas, diperbolehkan)
test('violation report accepts selesai equal to mulai', function () {
    $kesiswaan = laporanPelanggaranBvaActor();

    $this->actingAs($kesiswaan)->get(route('kesiswaan.laporan.index', [
        'mulai' => '2026-03-15',
        'selesai' => '2026-03-15',
    ]))->assertSuccessful()->assertSessionHasNoErrors();
});

// TS.LAP.013 / TC.LAP.013.001 — selesai 1 hari sebelum mulai (di bawah batas, ditolak)
test('violation report rejects selesai 1 day before mulai', function () {
    $kesiswaan = laporanPelanggaranBvaActor();

    $this->actingAs($kesiswaan)->get(route('kesiswaan.laporan.index', [
        'mulai' => '2026-03-15',
        'selesai' => '2026-03-14',
    ]))->assertSessionHasErrors('selesai');
});
