<?php

use App\Models\JenisPelanggaran;
use App\Models\Kelas;
use App\Models\PelanggaranSiswa;
use App\Models\PengajuanPoin;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function monitoringPoinBkCounselor(string $tingkat): Pengguna
{
    $counselor = Pengguna::factory()->counselor()->create(['status' => 'registered']);
    $counselor->profilGuru()->create([
        'nip' => fake()->unique()->numerify('19################'),
        'tipe_guru' => 'counselor',
        'tingkat' => $tingkat,
    ]);

    return $counselor;
}

function monitoringPoinBkStudent(string $tingkat, string $className, string $nama, string $nisn): ProfilSiswa
{
    $class = Kelas::create(['nama' => $className, 'tingkat' => $tingkat]);
    $studentUser = Pengguna::factory()->student()->create(['status' => 'registered', 'nama' => $nama]);

    return ProfilSiswa::factory()->create([
        'pengguna_id' => $studentUser->id,
        'kelas_id' => $class->id,
        'nisn' => $nisn,
    ]);
}

// TS.MPB.001 / TC.MPB.001.001 — index nampilin sisa poin siswa tingkat sendiri yang belum pernah dapat pelanggaran (positive)
test('bk monitoring index shows the default remaining points for a student with no violations', function () {
    $counselor = monitoringPoinBkCounselor('10');
    monitoringPoinBkStudent('10', '10. Monitoring Poin Bk 1', 'Fauzan Akbar', '71001');

    $this->actingAs($counselor)->get(route('bk.monitoring.index'))
        ->assertSuccessful()
        ->assertSee('Fauzan Akbar')
        ->assertSeeInOrder(['Fauzan Akbar', '100']);
});

// TS.MPB.002 / TC.MPB.002.001 — sisa poin dihitung dari total pelanggaran disetujui dikurangi ditambah pengajuan poin disetujui (positive)
test('bk monitoring index computes remaining points from approved violations and point additions', function () {
    $counselor = monitoringPoinBkCounselor('10');
    $student = monitoringPoinBkStudent('10', '10. Monitoring Poin Bk 2', 'Gita Purnama', '71002');
    $type = JenisPelanggaran::factory()->create(['kategori' => 'sedang', 'pengurangan_poin' => 30]);
    PelanggaranSiswa::factory()->create([
        'profil_siswa_id' => $student->nisn,
        'jenis_pelanggaran_id' => $type->id,
        'pengurangan_poin' => 30,
        'status' => 'approved',
    ]);
    PengajuanPoin::factory()->create([
        'profil_siswa_id' => $student->nisn,
        'status' => 'approved',
        'jumlah_poin' => 10,
    ]);

    $this->actingAs($counselor)->get(route('bk.monitoring.index'))
        ->assertSuccessful()
        ->assertSeeInOrder(['Gita Purnama', '80']);
});

// TS.MPB.003 / TC.MPB.003.001 — show detail nampilin agregat poin siswa tingkat sendiri (positive)
test('bk monitoring show displays the aggregated point information for a student in own grade level', function () {
    $counselor = monitoringPoinBkCounselor('10');
    $student = monitoringPoinBkStudent('10', '10. Monitoring Poin Bk 3', 'Hasan Nur', '71003');
    $type = JenisPelanggaran::factory()->create(['kategori' => 'ringan', 'pengurangan_poin' => 15, 'nama' => 'Terlambat Poin Bk']);
    PelanggaranSiswa::factory()->create([
        'profil_siswa_id' => $student->nisn,
        'jenis_pelanggaran_id' => $type->id,
        'nama_pelanggaran' => 'Terlambat Poin Bk',
        'pengurangan_poin' => 15,
        'status' => 'approved',
    ]);

    $this->actingAs($counselor)->get(route('bk.monitoring.show', $student))
        ->assertSuccessful()
        ->assertSee('Hasan Nur')
        ->assertSee('Terlambat Poin Bk');
});

// TS.MPB.004 / TC.MPB.004.001 — show detail BK tidak sedia tautan catat pelanggaran baru, beda dengan kesiswaan (negative, dokumentasi batas kewenangan)
test('bk monitoring show does not provide a link to record a new violation', function () {
    $counselor = monitoringPoinBkCounselor('10');
    $student = monitoringPoinBkStudent('10', '10. Monitoring Poin Bk 4', 'Indah Permata', '71004');

    $this->actingAs($counselor)->get(route('bk.monitoring.show', $student))
        ->assertSuccessful()
        ->assertViewHas('createViolationRoute', null)
        ->assertDontSee('Catat Pelanggaran');
});
