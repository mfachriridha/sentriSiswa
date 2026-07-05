<?php

use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

afterEach(function () {
    Carbon::setTestNow();
});

function createHomeroomTeacherWithClass(string $className = 'X IPA 1'): array
{
    $teacher = Pengguna::factory()->homeroom()->create([
        'status' => 'registered',
    ]);

    $teacher->profilGuru()->create([
        'nip' => fake()->unique()->numerify('19################'),
        'tipe_guru' => 'wali_kelas',
    ]);

    $class = Kelas::create([
        'nama' => $className,
        'tingkat' => '10',
        'wali_kelas_id' => $teacher->id,
    ]);

    return [$teacher, $class];
}

function createStudentInClass(Kelas $class, string $name, string $nis): ProfilSiswa
{
    $student = Pengguna::factory()->student()->create([
        'nama' => $name,
        'status' => 'registered',
    ]);

    return ProfilSiswa::factory()->create([
        'pengguna_id' => $student->id,
        'kelas_id' => $class->id,
        'nis' => $nis,
    ]);
}

test('kelas saya shows today attendance summary and all students without pagination', function () {
    Carbon::setTestNow('2026-06-01 08:00:00');

    [$teacher, $class] = createHomeroomTeacherWithClass();
    $firstStudent = createStudentInClass($class, 'Ayu', '10001');
    createStudentInClass($class, 'Bima', '10002');

    Absensi::create([
        'profil_siswa_id' => $firstStudent->nisn,
        'tanggal' => now()->toDateString(),
        'status' => 'hadir',
        'waktu_masuk' => '06:40',
        'path_selfie' => 'attendance-selfies/1/selfie.jpg',
    ]);

    $this->actingAs($teacher)
        ->get(route('wali-kelas.kelas-saya'))
        ->assertSuccessful()
        ->assertSee('Pantau absensi hari ini')
        ->assertSee('10001')
        ->assertSee('Ayu')
        ->assertSee('10002')
        ->assertSee('Bima')
        ->assertSee('Selfie Ayu')
        ->assertViewHas('stats', fn (array $stats): bool => $stats['hadir'] === 1 && $stats['belum_absen'] === 1);
});

test('homeroom teacher can set manual attendance without fake check in data', function () {
    Carbon::setTestNow('2026-06-01 08:00:00');

    [$teacher, $class] = createHomeroomTeacherWithClass();
    $student = createStudentInClass($class, 'Ayu', '10001');

    $this->actingAs($teacher)
        ->put(route('wali-kelas.kelas-saya.absensi.update', $student), [
            'status' => 'hadir',
        ])
        ->assertRedirect(route('wali-kelas.kelas-saya'));

    $attendance = Absensi::firstOrFail();

    expect($attendance->status)->toBe('hadir')
        ->and($attendance->waktu_masuk)->toBeNull()
        ->and($attendance->path_selfie)->toBeNull();
});

test('homeroom teacher cannot update student from another class', function () {
    Carbon::setTestNow('2026-06-01 08:00:00');

    [$teacher] = createHomeroomTeacherWithClass();
    [, $otherClass] = createHomeroomTeacherWithClass('X IPA 2');
    $student = createStudentInClass($otherClass, 'Citra', '10003');

    $this->actingAs($teacher)
        ->put(route('wali-kelas.kelas-saya.absensi.update', $student), [
            'status' => 'hadir',
        ])
        ->assertForbidden();
});

test('homeroom teacher can view own student detail but not another class detail', function () {
    [$teacher, $class] = createHomeroomTeacherWithClass();
    $student = createStudentInClass($class, 'Ayu', '10001')->load('pengguna');
    [, $otherClass] = createHomeroomTeacherWithClass('X IPA 2');
    $otherStudent = createStudentInClass($otherClass, 'Citra', '10003');

    $this->actingAs($teacher)
        ->get(route('wali-kelas.kelas-saya.show', $student))
        ->assertSuccessful()
        ->assertSee('Detail Siswa')
        ->assertSee($student->pengguna->nama);

    $this->actingAs($teacher)
        ->get(route('wali-kelas.kelas-saya.show', $otherStudent))
        ->assertForbidden();
});

test('recap summarizes final weekday records and ignores weekend and belum absen records', function () {
    Carbon::setTestNow('2026-06-08 08:00:00');

    [$teacher, $class] = createHomeroomTeacherWithClass();
    $student = createStudentInClass($class, 'Ayu', '10001');

    foreach ([
        ['2026-06-01', 'hadir'],
        ['2026-06-02', 'terlambat'],
        ['2026-06-03', 'izin'],
        ['2026-06-06', 'alpha'],
        ['2026-06-08', 'belum_absen'],
    ] as [$date, $status]) {
        Absensi::create([
            'profil_siswa_id' => $student->nisn,
            'tanggal' => $date,
            'status' => $status,
        ]);
    }

    $this->actingAs($teacher)
        ->get(route('wali-kelas.absensi.index', [
            'mulai' => '2026-06-01',
            'selesai' => '2026-06-08',
        ]))
        ->assertSuccessful()
        ->assertViewHas('stats', function (array $stats) use ($student): bool {
            return $stats[$student->nisn] === [
                'hadir' => 1,
                'terlambat' => 1,
                'izin' => 1,
                'sakit' => 0,
                'alpha' => 0,
                'percentage' => 66.7,
            ];
        });
});

test('homeroom teacher can download excel recap for selected range', function () {
    Carbon::setTestNow('2026-06-08 08:00:00');

    [$teacher, $class] = createHomeroomTeacherWithClass();
    createStudentInClass($class, 'Ayu', '10001');

    $this->actingAs($teacher)
        ->get(route('wali-kelas.absensi.ekspor-excel', [
            'mulai' => '2026-06-01',
            'selesai' => '2026-06-08',
        ]))
        ->assertDownload('rekap-absensi-X IPA 1-2026-06-01-sampai-2026-06-08.xlsx');
});
