<?php

use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

afterEach(function () {
    Carbon::setTestNow();
});

function riwayatAbsensiSiswaBvaStudent(): Pengguna
{
    $student = Pengguna::factory()->student()->create(['status' => 'registered']);
    ProfilSiswa::factory()->create([
        'pengguna_id' => $student->id,
        'nis' => fake()->unique()->numerify('#####'),
    ]);

    return $student;
}

// ── Boundary: format bulan ^\d{4}-(0[1-9]|1[0-2])$ ──────────────────────────

// TS.RAS.008 / TC.RAS.008.001 — bulan 2026-01 (batas bawah angka bulan, diperbolehkan)
test('attendance history accepts month 2026-01, the lower boundary', function () {
    Carbon::setTestNow('2026-06-15 08:00:00');
    $student = riwayatAbsensiSiswaBvaStudent();

    $this->actingAs($student)->get(route('siswa.absensi.riwayat', ['month' => '2026-01']))
        ->assertSuccessful()
        ->assertSee('Januari 2026');
});

// TS.RAS.009 / TC.RAS.009.001 — bulan 2026-00 (di bawah batas, invalid, fallback ke bulan berjalan)
test('attendance history rejects month 2026-00, 1 below the lower boundary', function () {
    Carbon::setTestNow('2026-06-15 08:00:00');
    $student = riwayatAbsensiSiswaBvaStudent();

    $this->actingAs($student)->get(route('siswa.absensi.riwayat', ['month' => '2026-00']))
        ->assertSuccessful()
        ->assertSee('Juni 2026');
});

// TS.RAS.010 / TC.RAS.010.001 — bulan 2026-12 (batas atas, diperbolehkan)
test('attendance history accepts month 2026-12, the upper boundary', function () {
    Carbon::setTestNow('2026-06-15 08:00:00');
    $student = riwayatAbsensiSiswaBvaStudent();

    $this->actingAs($student)->get(route('siswa.absensi.riwayat', ['month' => '2026-12']))
        ->assertSuccessful()
        ->assertSee('Desember 2026');
});

// TS.RAS.011 / TC.RAS.011.001 — bulan 2026-13 (di atas batas, invalid, fallback ke bulan berjalan)
test('attendance history rejects month 2026-13, 1 above the upper boundary', function () {
    Carbon::setTestNow('2026-06-15 08:00:00');
    $student = riwayatAbsensiSiswaBvaStudent();

    $this->actingAs($student)->get(route('siswa.absensi.riwayat', ['month' => '2026-13']))
        ->assertSuccessful()
        ->assertSee('Juni 2026');
});
