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
    $teacher = createDashboardTeacher('homeroom');

    $this->actingAs($teacher)
        ->get(route('guru.dashboard'))
        ->assertSuccessful()
        ->assertSee('Kelas Saya')
        ->assertSee('Rekap Absensi')
        ->assertSee('Profil Saya');
});

test('student affairs dashboard renders monitoring shortcuts', function () {
    // The production-only enum migration cannot alter SQLite's original check constraint.
    DB::statement('PRAGMA ignore_check_constraints = ON');
    $teacher = createDashboardTeacher('student_affairs');
    DB::statement('PRAGMA ignore_check_constraints = OFF');

    $this->actingAs($teacher)
        ->get(route('guru.dashboard'))
        ->assertSuccessful()
        ->assertSee('Monitoring Siswa')
        ->assertSee('Pelanggaran Siswa')
        ->assertSee('Laporan Kesiswaan')
        ->assertSee('Tata Tertib')
        ->assertSee('Profil Saya');
});

test('counselor dashboard renders bk shortcuts', function () {
    $teacher = createDashboardTeacher('counselor', '10');

    $this->actingAs($teacher)
        ->get(route('guru.dashboard'))
        ->assertSuccessful()
        ->assertSee('Monitoring BK')
        ->assertSee('Pengajuan Pelanggaran')
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
        ->assertSee('Catat kehadiran harian Anda')
        ->assertSee('Riwayat');
});

function createDashboardTeacher(string $teacherType, ?string $grade = null): Pengguna
{
    $teacher = Pengguna::factory()->homeroom()->create([
        'status' => 'registered',
    ]);

    $teacher->profilGuru()->create([
        'nip' => fake()->unique()->numerify('19################'),
        'tipe_guru' => $teacherType,
        'tingkat' => $grade,
    ]);

    return $teacher;
}
