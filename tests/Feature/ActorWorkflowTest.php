<?php

use App\Models\Kelas;
use App\Models\PelanggaranSiswa;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use App\Models\TataTertib;
use App\Models\JenisPelanggaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

test('counselor can only monitor assigned grade and cannot approve violations', function () {
    [$counselor, $gradeTenStudent, $gradeElevenStudent] = actorWorkflowUsers();

    $this->actingAs($counselor)
        ->get(route('bk.monitoring.index'))
        ->assertSuccessful()
        ->assertSee($gradeTenStudent->pengguna->nama)
        ->assertDontSee($gradeElevenStudent->pengguna->nama);

    $this->actingAs($counselor)
        ->get(route('bk.monitoring.show', $gradeTenStudent))
        ->assertSuccessful()
        ->assertSee('Detail Siswa')
        ->assertSee('Biodata Lengkap');

    $this->actingAs($counselor)
        ->get(route('bk.monitoring.show', $gradeElevenStudent))
        ->assertForbidden();

    $violation = PelanggaranSiswa::factory()->create([
        'profil_siswa_id' => $gradeTenStudent->id,
        'dicatat_oleh_id' => $counselor->id,
        'status' => 'pending',
        'disetujui_pada' => null,
    ]);

    $this->actingAs($counselor)
        ->put(route('kesiswaan.pelanggaran-siswa.approve', $violation))
        ->assertForbidden();
});

test('student affairs can view student detail across classes', function () {
    [, $gradeTenStudent, $gradeElevenStudent] = actorWorkflowUsers();
    $studentAffairs = createActorWorkflowTeacher('student_affairs');

    $this->actingAs($studentAffairs)
        ->get(route('kesiswaan.monitoring.show', $gradeTenStudent))
        ->assertSuccessful()
        ->assertSee($gradeTenStudent->pengguna->nama);

    $this->actingAs($studentAffairs)
        ->get(route('kesiswaan.monitoring.show', $gradeElevenStudent))
        ->assertSuccessful()
        ->assertSee($gradeElevenStudent->pengguna->nama);
});

test('counselor submits violation and student affairs approves it', function () {
    [$counselor, $student] = actorWorkflowUsers();
    $studentAffairs = createActorWorkflowTeacher('student_affairs');
    $jenisPelanggaran = JenisPelanggaran::factory()->create();

    $this->actingAs($counselor)
        ->post(route('bk.pelanggaran.store'), [
            'profil_siswa_id' => $student->id,
            'jenis_pelanggaran_id' => $jenisPelanggaran->id,
            'tanggal_pelanggaran' => now()->toDateString(),
            'catatan' => 'Terlambat masuk kelas.',
        ])
        ->assertRedirect(route('bk.pelanggaran.index'));

    $violation = PelanggaranSiswa::first();

    expect($violation->status)->toBe('pending');
    expect($student->fresh()->poin)->toBe(100);

    $this->actingAs($studentAffairs)
        ->put(route('kesiswaan.pelanggaran-siswa.approve', $violation))
        ->assertRedirect(route('kesiswaan.pelanggaran-siswa.show', $violation));

    expect($violation->fresh()->status)->toBe('approved');
    expect($student->fresh()->poin)->toBe(100 - $jenisPelanggaran->pengurangan_poin);
});

test('student affairs can reject violation without reducing student points', function () {
    [$counselor, $student] = actorWorkflowUsers();
    $studentAffairs = createActorWorkflowTeacher('student_affairs');
    $violation = PelanggaranSiswa::factory()->create([
        'profil_siswa_id' => $student->id,
        'dicatat_oleh_id' => $counselor->id,
        'status' => 'pending',
        'disetujui_pada' => null,
    ]);

    $this->actingAs($studentAffairs)
        ->put(route('kesiswaan.pelanggaran-siswa.reject', $violation), [
            'alasan_penolakan' => 'Bukti belum cukup.',
        ])
        ->assertRedirect(route('kesiswaan.pelanggaran-siswa.show', $violation));

    expect($violation->fresh()->status)->toBe('rejected');
    expect($student->fresh()->poin)->toBe(100);
});

test('school rule pdf can be uploaded by student affairs and viewed by student', function () {
    Storage::fake('public');

    $studentAffairs = createActorWorkflowTeacher('student_affairs');
    $studentUser = Pengguna::factory()->student()->create(['status' => 'registered']);
    ProfilSiswa::factory()->create(['pengguna_id' => $studentUser->id]);

    $this->actingAs($studentAffairs)
        ->post(route('kesiswaan.tata-tertib.store'), [
            'judul' => 'Tata Tertib 2026',
            'file_pdf' => UploadedFile::fake()->create('aturan.pdf', 64, 'application/pdf'),
            'dipublikasikan' => '1',
        ])
        ->assertRedirect(route('kesiswaan.tata-tertib.index'));

    $rule = TataTertib::first();

    expect($rule)->not->toBeNull();
    expect($rule->dipublikasikan)->toBeTrue();
    Storage::disk('public')->assertExists($rule->path_file);

    $this->actingAs($studentUser)
        ->get(route('siswa.tata-tertib.index'))
        ->assertSuccessful()
        ->assertSee('Tata Tertib 2026');
});

test('violation reports and attendance pdf routes render downloads for allowed roles', function () {
    [$counselor, $student] = actorWorkflowUsers();
    $studentAffairs = createActorWorkflowTeacher('student_affairs');
    $homeroom = createActorWorkflowTeacher('homeroom');
    $class = $student->kelas;
    $class->update(['wali_kelas_id' => $homeroom->id]);

    PelanggaranSiswa::factory()->create([
        'profil_siswa_id' => $student->id,
        'status' => 'approved',
        'disetujui_pada' => now(),
    ]);

    $this->actingAs($counselor)
        ->get(route('bk.laporan.index'))
        ->assertSuccessful()
        ->assertSee('Rekap Absensi');

    $this->actingAs($counselor)
        ->get(route('bk.laporan.export-excel'))
        ->assertSuccessful();

    $this->actingAs($studentAffairs)
        ->get(route('kesiswaan.laporan.index'))
        ->assertSuccessful()
        ->assertSee('Laporan Kesiswaan');

    $this->actingAs($studentAffairs)
        ->get(route('kesiswaan.laporan.export-excel'))
        ->assertSuccessful();

    $this->actingAs($homeroom)
        ->get(route('wali-kelas.absensi.index'))
        ->assertSuccessful();

    $this->actingAs($homeroom)
        ->get(route('wali-kelas.absensi.export-pdf'))
        ->assertSuccessful()
        ->assertHeader('content-type', 'application/pdf');
});

function actorWorkflowUsers(): array
{
    $classTen = Kelas::create(['nama' => 'X RPL 1', 'tingkat' => '10']);
    $classEleven = Kelas::create(['nama' => 'XI RPL 1', 'tingkat' => '11']);

    $counselor = createActorWorkflowTeacher('counselor', '10');
    $gradeTenStudent = createActorWorkflowStudentInClass($classTen);
    $gradeElevenStudent = createActorWorkflowStudentInClass($classEleven);

    return [$counselor, $gradeTenStudent, $gradeElevenStudent];
}

function createActorWorkflowTeacher(string $teacherType, ?string $grade = null): Pengguna
{
    if ($teacherType === 'student_affairs') {
        DB::statement('PRAGMA ignore_check_constraints = ON');
    }

    $factoryState = match ($teacherType) {
        'counselor'       => 'counselor',
        'student_affairs' => 'studentAffairs',
        default           => 'homeroom',
    };

    $teacher = Pengguna::factory()->{$factoryState}()->create(['status' => 'registered']);
    $teacher->profilGuru()->create([
        'nip'      => fake()->unique()->numerify('19############'),
        'telepon'  => fake()->numerify('08##########'),
        'tipe_guru'=> $teacherType,
        'tingkat'  => $grade,
    ]);

    if ($teacherType === 'student_affairs') {
        DB::statement('PRAGMA ignore_check_constraints = OFF');
    }

    return $teacher;
}

function createActorWorkflowStudentInClass(Kelas $class): ProfilSiswa
{
    $student = Pengguna::factory()->student()->create(['status' => 'registered']);

    return ProfilSiswa::factory()->create([
        'pengguna_id' => $student->id,
        'kelas_id' => $class->id,
        'nisn' => fake()->unique()->numerify('##########'),
        'nis' => fake()->unique()->numerify('#####'),
    ])->load(['pengguna', 'kelas']);
}
