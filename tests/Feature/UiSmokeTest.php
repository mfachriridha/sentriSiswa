<?php

use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('guest login renders compact authentication form', function () {
    $this->get(route('login'))
        ->assertSuccessful()
        ->assertSee('Sentri Siswa')
        ->assertSee('Masuk')
        ->assertSee('Daftar di sini');
});

test('admin dashboard renders responsive shell and management shortcuts', function () {
    $admin = Pengguna::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('Buka menu navigasi')
        ->assertSee('lg:ml-56')
        ->assertSee('Data Guru')
        ->assertSee('Data Siswa')
        ->assertSee('Data Kelas');
});

test('homeroom dashboard renders attendance shortcuts', function () {
    $teacher = createDashboardTeacher('wali_kelas');

    $this->actingAs($teacher)
        ->get(route('wali-kelas.dashboard'))
        ->assertSuccessful()
        ->assertSee('Kelas Saya')
        ->assertSee('Rekap Absensi')
        ->assertSee('Pengajuan Poin')
        ->assertSee('Profil Saya');
});

test('student affairs dashboard renders monitoring shortcuts', function () {
    // The production-only enum migration cannot alter SQLite's original check constraint.
    DB::statement('PRAGMA ignore_check_constraints = ON');
    $teacher = createDashboardTeacher('kesiswaan');
    DB::statement('PRAGMA ignore_check_constraints = OFF');

    $this->actingAs($teacher)
        ->get(route('kesiswaan.dashboard'))
        ->assertSuccessful()
        ->assertSee('Monitoring Siswa')
        ->assertSee('Pelanggaran Siswa')
        ->assertSee('Pengajuan Poin')
        ->assertSee('Laporan Kesiswaan')
        ->assertSee('Tata Tertib')
        ->assertSee('Profil Saya');
});

test('counselor dashboard renders bk shortcuts', function () {
    $teacher = createDashboardTeacher('bk', '10');

    $this->actingAs($teacher)
        ->get(route('bk.dashboard'))
        ->assertSuccessful()
        ->assertSee('Monitoring BK')
        ->assertDontSee('Pengajuan Pelanggaran')
        ->assertSee('Laporan BK');
});

test('student dashboard and attendance page render existing primary actions', function () {
    $student = Pengguna::factory()->student()->create([
        'status' => 'registered',
    ]);

    ProfilSiswa::factory()->create([
        'pengguna_id' => $student->id,
    ]);

    $this->actingAs($student)
        ->get(route('siswa.dashboard'))
        ->assertSuccessful()
        ->assertSee('Absensi')
        ->assertSee('Poin Saya')
        ->assertSee('Tata Tertib')
        ->assertSee('Profil Saya');

    $this->actingAs($student)
        ->get(route('siswa.absensi'))
        ->assertSuccessful()
        ->assertSee('Absensi Hari Ini')
        ->assertSee('Riwayat');
});

function createDashboardTeacher(string $teacherType, ?string $grade = null): Pengguna
{
    $factoryState = match ($teacherType) {
        'bk' => 'counselor',
        'kesiswaan' => 'studentAffairs',
        default => 'homeroom',
    };

    $teacher = Pengguna::factory()->{$factoryState}()->create([
        'status' => 'registered',
    ]);

    $teacher->profilGuru()->create([
        'nip' => fake()->unique()->numerify('19################'),
        'tipe_guru' => $teacherType,
        'tingkat' => $grade,
    ]);

    return $teacher;
}
