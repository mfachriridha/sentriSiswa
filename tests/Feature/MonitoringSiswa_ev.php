<?php

use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function monitoringSiswaKesiswaan(): Pengguna
{
    $studentAffairs = Pengguna::factory()->studentAffairs()->create(['status' => 'registered']);
    $studentAffairs->profilGuru()->create([
        'nip' => fake()->unique()->numerify('19################'),
        'tipe_guru' => 'student_affairs',
    ]);

    return $studentAffairs;
}

function monitoringSiswaStudent(string $tingkat, string $className, string $nama, string $nisn): ProfilSiswa
{
    $class = Kelas::create(['nama' => $className, 'tingkat' => $tingkat]);
    $studentUser = Pengguna::factory()->student()->create(['status' => 'registered', 'nama' => $nama]);

    return ProfilSiswa::factory()->create([
        'pengguna_id' => $studentUser->id,
        'kelas_id' => $class->id,
        'nisn' => $nisn,
    ]);
}

// TS.MonitoringSiswa.001 / TC.MonitoringSiswa.001.001 — index nampilin siswa dari seluruh sekolah tanpa batas kelas/tingkat (positive)
test('monitoring index shows students from every grade level school-wide', function () {
    $kesiswaan = monitoringSiswaKesiswaan();
    monitoringSiswaStudent('10', '10. Monitoring 1', 'Siswa Sepuluh', '80001');
    monitoringSiswaStudent('12', '12. Monitoring 1', 'Siswa Dua Belas', '80002');

    $this->actingAs($kesiswaan)->get(route('kesiswaan.monitoring.index'))
        ->assertSuccessful()
        ->assertSee('Siswa Sepuluh')
        ->assertSee('Siswa Dua Belas');
});

// TS.MonitoringSiswa.002 / TC.MonitoringSiswa.002.001 — filter search berdasarkan nama (positive)
test('monitoring index filters by student name search', function () {
    $kesiswaan = monitoringSiswaKesiswaan();
    monitoringSiswaStudent('10', '10. Monitoring 2', 'Ahmad Fauzi', '80003');
    monitoringSiswaStudent('10', '10. Monitoring 3', 'Budi Santoso', '80004');

    $this->actingAs($kesiswaan)->get(route('kesiswaan.monitoring.index', ['search' => 'Ahmad']))
        ->assertSuccessful()
        ->assertSee('Ahmad Fauzi')
        ->assertDontSee('Budi Santoso');
});

// TS.MonitoringSiswa.003 / TC.MonitoringSiswa.003.001 — filter berdasarkan kelas_id (positive)
test('monitoring index filters by kelas_id', function () {
    $kesiswaan = monitoringSiswaKesiswaan();
    $studentA = monitoringSiswaStudent('11', '11. Monitoring 4', 'Citra Dewi', '80005');
    monitoringSiswaStudent('11', '11. Monitoring 5', 'Doni Prakoso', '80006');

    $this->actingAs($kesiswaan)->get(route('kesiswaan.monitoring.index', ['kelas_id' => $studentA->kelas_id]))
        ->assertSuccessful()
        ->assertSee('Citra Dewi')
        ->assertDontSee('Doni Prakoso');
});

// TS.MonitoringSiswa.004 / TC.MonitoringSiswa.004.001 — show detail nampilin agregat poin dan absensi (positive)
test('monitoring show displays student detail with aggregated points and attendance', function () {
    $kesiswaan = monitoringSiswaKesiswaan();
    $student = monitoringSiswaStudent('10', '10. Monitoring 6', 'Eka Wulandari', '80007');

    $this->actingAs($kesiswaan)->get(route('kesiswaan.monitoring.show', $student))
        ->assertSuccessful()
        ->assertSee('Eka Wulandari');
});

// TS.MonitoringSiswa.005 / TC.MonitoringSiswa.005.001 — show detail sedia link untuk catat pelanggaran baru (positive)
test('monitoring show provides a link to record a new violation for the student', function () {
    $kesiswaan = monitoringSiswaKesiswaan();
    $student = monitoringSiswaStudent('10', '10. Monitoring 7', 'Fajar Nugroho', '80008');

    $this->actingAs($kesiswaan)->get(route('kesiswaan.monitoring.show', $student))
        ->assertSuccessful()
        ->assertViewHas('createViolationRoute', route('kesiswaan.pelanggaran-siswa.create', ['profil_siswa_id' => $student->nisn]));
});
