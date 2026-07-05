<?php

use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function monitoringAbsensiBkCounselor(string $tingkat): Pengguna
{
    $counselor = Pengguna::factory()->counselor()->create(['status' => 'registered']);
    $counselor->profilGuru()->create([
        'nip' => fake()->unique()->numerify('19################'),
        'tipe_guru' => 'bk',
        'tingkat' => $tingkat,
    ]);

    return $counselor;
}

function monitoringAbsensiBkStudent(string $tingkat, string $className, string $nama, string $nisn): ProfilSiswa
{
    $class = Kelas::create(['nama' => $className, 'tingkat' => $tingkat]);
    $studentUser = Pengguna::factory()->student()->create(['status' => 'registered', 'nama' => $nama]);

    return ProfilSiswa::factory()->create([
        'pengguna_id' => $studentUser->id,
        'kelas_id' => $class->id,
        'nisn' => $nisn,
    ]);
}

// TS.MAB.001 / TC.MAB.001.001 — index nampilin siswa tingkat sendiri (positive)
test('bk monitoring index shows a student from the counselor own grade level', function () {
    $counselor = monitoringAbsensiBkCounselor('10');
    monitoringAbsensiBkStudent('10', '10. Monitoring Bk 1', 'Indra Wijaya', '70001');

    $this->actingAs($counselor)->get(route('bk.monitoring.index'))
        ->assertSuccessful()
        ->assertSee('Indra Wijaya');
});

// TS.MAB.002 / TC.MAB.002.001 — dua siswa dari tingkat berbeda sudah terdaftar, BK buka halaman monitoring, siswa tingkat lain tidak ikut tampil (negative)
test('bk monitoring index does not show a student from another grade level', function () {
    $counselor = monitoringAbsensiBkCounselor('10');
    monitoringAbsensiBkStudent('10', '10. Monitoring Bk 2', 'Joko Santoso', '70002');
    monitoringAbsensiBkStudent('11', '11. Monitoring Bk 1', 'Kartika Sari', '70003');

    $this->actingAs($counselor)->get(route('bk.monitoring.index'))
        ->assertSuccessful()
        ->assertSee('Joko Santoso')
        ->assertDontSee('Kartika Sari');
});

// TS.MAB.003 / TC.MAB.003.001 — dua siswa satu tingkat sudah terdaftar, BK cari nama salah satu siswa lewat kolom pencarian (positive)
test('bk monitoring index filters by student name search', function () {
    $counselor = monitoringAbsensiBkCounselor('10');
    monitoringAbsensiBkStudent('10', '10. Monitoring Bk 3', 'Lestari Handayani', '70004');
    monitoringAbsensiBkStudent('10', '10. Monitoring Bk 4', 'Muhammad Fikri', '70005');

    $this->actingAs($counselor)->get(route('bk.monitoring.index', ['search' => 'Lestari']))
        ->assertSuccessful()
        ->assertSee('Lestari Handayani')
        ->assertDontSee('Muhammad Fikri');
});

// TS.MAB.004 / TC.MAB.004.001 — dua siswa dari kelas berbeda dalam satu tingkat sudah terdaftar, BK pilih salah satu kelas lewat filter kelas (positive)
test('bk monitoring index filters by a class within the counselor own grade level', function () {
    $counselor = monitoringAbsensiBkCounselor('10');
    $studentA = monitoringAbsensiBkStudent('10', '10. Monitoring Bk 5', 'Nadia Putri', '70006');
    monitoringAbsensiBkStudent('10', '10. Monitoring Bk 6', 'Oscar Pratama', '70007');

    $this->actingAs($counselor)->get(route('bk.monitoring.index', ['kelas_id' => $studentA->kelas_id]))
        ->assertSuccessful()
        ->assertSee('Nadia Putri')
        ->assertDontSee('Oscar Pratama');
});

// TS.MAB.005 / TC.MAB.005.001 — siswa tingkat sendiri sudah absen hadir hari ini, BK buka detail siswa tersebut (positive)
test('bk monitoring show displays attendance status and percentage for a student in own grade level', function () {
    $counselor = monitoringAbsensiBkCounselor('10');
    $student = monitoringAbsensiBkStudent('10', '10. Monitoring Bk 7', 'Putri Ramadhani', '70008');
    Absensi::create(['profil_siswa_id' => $student->nisn, 'tanggal' => now()->toDateString(), 'status' => 'hadir']);

    $this->actingAs($counselor)->get(route('bk.monitoring.show', $student))
        ->assertSuccessful()
        ->assertSee('Putri Ramadhani');
});

// TS.MAB.006 / TC.MAB.006.001 — siswa dari tingkat lain sudah terdaftar, BK coba buka detail siswa tingkat lain tersebut (negative)
test('bk monitoring show is forbidden for a student in another grade level', function () {
    $counselor = monitoringAbsensiBkCounselor('10');
    $otherStudent = monitoringAbsensiBkStudent('11', '11. Monitoring Bk 2', 'Rangga Saputra', '70009');

    $this->actingAs($counselor)->get(route('bk.monitoring.show', $otherStudent))
        ->assertForbidden();
});

// TS.MAB.007 / TC.MAB.007.001 — BK belum dikaitkan ke tingkat manapun oleh admin, BK buka halaman monitoring (negative, edge case)
test('bk monitoring index shows an empty list when the counselor has no grade level assigned', function () {
    $counselor = Pengguna::factory()->counselor()->create(['status' => 'registered']);
    $counselor->profilGuru()->create([
        'nip' => fake()->unique()->numerify('19################'),
        'tipe_guru' => 'bk',
        'tingkat' => null,
    ]);
    monitoringAbsensiBkStudent('10', '10. Monitoring Bk 8', 'Sartika Dewi', '70010');

    $this->actingAs($counselor)->get(route('bk.monitoring.index'))
        ->assertSuccessful()
        ->assertDontSee('Sartika Dewi');
});
