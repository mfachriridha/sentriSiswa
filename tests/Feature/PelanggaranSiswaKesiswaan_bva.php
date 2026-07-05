<?php

use App\Models\JenisPelanggaran;
use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function pelanggaranKesiswaanBvaActor(): Pengguna
{
    $studentAffairs = Pengguna::factory()->studentAffairs()->create(['status' => 'registered']);
    $studentAffairs->profilGuru()->create([
        'nip' => fake()->unique()->numerify('19################'),
        'tipe_guru' => 'kesiswaan',
    ]);

    return $studentAffairs;
}

function pelanggaranKesiswaanBvaStudent(string $className, string $nisn): ProfilSiswa
{
    $class = Kelas::create(['nama' => $className, 'tingkat' => '10']);
    $studentUser = Pengguna::factory()->student()->create(['status' => 'registered']);

    return ProfilSiswa::factory()->create([
        'pengguna_id' => $studentUser->id,
        'kelas_id' => $class->id,
        'nisn' => $nisn,
    ]);
}

// ── Boundary: tanggal_pelanggaran, before_or_equal:today ───────────────────

// TS.PSK.010 / TC.PSK.010.001 — tanggal hari ini (tepat di batas atas, diperbolehkan)
test('violation record accepts a date exactly at today, the upper boundary', function () {
    $kesiswaan = pelanggaranKesiswaanBvaActor();
    $student = pelanggaranKesiswaanBvaStudent('10. Pelanggaran BVA 1', '91001');
    $type = JenisPelanggaran::factory()->create(['aktif' => true]);

    $this->actingAs($kesiswaan)->post(route('kesiswaan.pelanggaran-siswa.store'), [
        'profil_siswa_id' => $student->nisn,
        'jenis_pelanggaran_id' => $type->id,
        'tanggal_pelanggaran' => now()->format('Y-m-d'),
    ])->assertRedirect(route('kesiswaan.pelanggaran-siswa.index'));

    $this->assertDatabaseHas('pelanggaran_siswa', ['profil_siswa_id' => $student->nisn]);
});

// TS.PSK.011 / TC.PSK.011.001 — tanggal besok (1 hari di atas batas, ditolak)
test('violation record rejects a date 1 day above today, the upper boundary', function () {
    $kesiswaan = pelanggaranKesiswaanBvaActor();
    $student = pelanggaranKesiswaanBvaStudent('10. Pelanggaran BVA 2', '91002');
    $type = JenisPelanggaran::factory()->create(['aktif' => true]);

    $this->actingAs($kesiswaan)->post(route('kesiswaan.pelanggaran-siswa.store'), [
        'profil_siswa_id' => $student->nisn,
        'jenis_pelanggaran_id' => $type->id,
        'tanggal_pelanggaran' => now()->addDay()->format('Y-m-d'),
    ])->assertSessionHasErrors('tanggal_pelanggaran');
});
