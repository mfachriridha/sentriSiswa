<?php

use App\Models\Kelas;
use App\Models\PengajuanPoin;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function pengajuanPoinWaliHomeroom(string $className = '10. Pengajuan 1'): array
{
    $teacher = Pengguna::factory()->homeroom()->create(['status' => 'registered']);
    $teacher->profilGuru()->create([
        'nip' => fake()->unique()->numerify('19################'),
        'tipe_guru' => 'homeroom',
    ]);
    $class = Kelas::create(['nama' => $className, 'tingkat' => '10', 'wali_kelas_id' => $teacher->id]);

    return [$teacher, $class];
}

function pengajuanPoinWaliStudent(Kelas $class, string $nis): ProfilSiswa
{
    $student = Pengguna::factory()->student()->create(['status' => 'registered']);

    return ProfilSiswa::factory()->create([
        'pengguna_id' => $student->id,
        'kelas_id' => $class->id,
        'nis' => $nis,
    ]);
}

// TS.PGP.001 / TC.PGP.001.001 — ajukan penambahan poin untuk siswa kelas sendiri berhasil (positive)
test('homeroom teacher can submit a point-addition request for own class student', function () {
    [$teacher, $class] = pengajuanPoinWaliHomeroom();
    $student = pengajuanPoinWaliStudent($class, '60001');

    $this->actingAs($teacher)->post(route('wali-kelas.pengajuan-poin.store'), [
        'profil_siswa_id' => $student->nisn,
        'alasan' => 'Juara 1 lomba debat tingkat provinsi.',
    ])->assertRedirect(route('wali-kelas.pengajuan-poin.index'));

    $pengajuan = PengajuanPoin::where('profil_siswa_id', $student->nisn)->firstOrFail();
    expect($pengajuan->status)->toBe('pending');
    expect($pengajuan->jumlah_poin)->toBeNull();
    expect($student->fresh()->poin)->toBe(100);
});

// TS.PGP.002 / TC.PGP.002.001 — ajukan untuk siswa kelas lain ditolak (negative)
test('homeroom teacher cannot submit a point-addition request for another class student', function () {
    [$teacher] = pengajuanPoinWaliHomeroom();
    [, $otherClass] = pengajuanPoinWaliHomeroom('10. Pengajuan 2');
    $otherStudent = pengajuanPoinWaliStudent($otherClass, '60002');

    $this->actingAs($teacher)->post(route('wali-kelas.pengajuan-poin.store'), [
        'profil_siswa_id' => $otherStudent->nisn,
        'alasan' => 'Alasan apapun.',
    ])->assertForbidden();
});

// TS.PGP.003 / TC.PGP.003.001 — alasan kosong ditolak (negative)
test('homeroom teacher cannot submit a point-addition request with an empty alasan', function () {
    [$teacher, $class] = pengajuanPoinWaliHomeroom();
    $student = pengajuanPoinWaliStudent($class, '60003');

    $this->actingAs($teacher)->post(route('wali-kelas.pengajuan-poin.store'), [
        'profil_siswa_id' => $student->nisn,
        'alasan' => '',
    ])->assertSessionHasErrors('alasan');
});

// TS.PGP.004 / TC.PGP.004.001 — profil_siswa_id (nisn) yang tidak ada ditolak (negative)
test('homeroom teacher cannot submit a point-addition request for a non-existent nisn', function () {
    [$teacher, $class] = pengajuanPoinWaliHomeroom();

    $this->actingAs($teacher)->post(route('wali-kelas.pengajuan-poin.store'), [
        'profil_siswa_id' => '9999999999',
        'alasan' => 'Alasan apapun.',
    ])->assertSessionHasErrors('profil_siswa_id');
});

// TS.PGP.005 / TC.PGP.005.001 — profil_siswa_id kosong ditolak (negative)
test('homeroom teacher cannot submit a point-addition request without a profil_siswa_id', function () {
    [$teacher, $class] = pengajuanPoinWaliHomeroom();

    $this->actingAs($teacher)->post(route('wali-kelas.pengajuan-poin.store'), [
        'profil_siswa_id' => '',
        'alasan' => 'Alasan apapun.',
    ])->assertSessionHasErrors('profil_siswa_id');
});

// TS.PGP.006 / TC.PGP.006.001 — index cuma nampilin pengajuan milik sendiri (positive)
test('pengajuan poin index only shows submissions made by the logged in teacher', function () {
    [$teacherA, $classA] = pengajuanPoinWaliHomeroom();
    [$teacherB, $classB] = pengajuanPoinWaliHomeroom('10. Pengajuan 3');
    $studentA = pengajuanPoinWaliStudent($classA, '60004');
    $studentB = pengajuanPoinWaliStudent($classB, '60005');

    PengajuanPoin::factory()->create(['profil_siswa_id' => $studentA->nisn, 'diajukan_oleh_id' => $teacherA->id, 'status' => 'pending', 'alasan' => 'Pengajuan A']);
    PengajuanPoin::factory()->create(['profil_siswa_id' => $studentB->nisn, 'diajukan_oleh_id' => $teacherB->id, 'status' => 'pending', 'alasan' => 'Pengajuan B']);

    $this->actingAs($teacherA)->get(route('wali-kelas.pengajuan-poin.index'))
        ->assertSuccessful()
        ->assertSee('Pengajuan A')
        ->assertDontSee('Pengajuan B');
});

// TS.PGP.007 / TC.PGP.007.001 — index filter berdasarkan status (positive)
test('pengajuan poin index filters by status', function () {
    [$teacher, $class] = pengajuanPoinWaliHomeroom();
    $student = pengajuanPoinWaliStudent($class, '60006');

    PengajuanPoin::factory()->create(['profil_siswa_id' => $student->nisn, 'diajukan_oleh_id' => $teacher->id, 'status' => 'pending', 'alasan' => 'Masih menunggu']);
    PengajuanPoin::factory()->create(['profil_siswa_id' => $student->nisn, 'diajukan_oleh_id' => $teacher->id, 'status' => 'approved', 'jumlah_poin' => 5, 'alasan' => 'Sudah disetujui']);

    $this->actingAs($teacher)->get(route('wali-kelas.pengajuan-poin.index', ['status' => 'pending']))
        ->assertSuccessful()
        ->assertSee('Masih menunggu')
        ->assertDontSee('Sudah disetujui');
});

// TS.PGP.008 / TC.PGP.008.001 — guru tanpa kelas wali, daftar siswa di create() kosong (positive)
test('create form shows an empty student list when teacher has no homeroom class', function () {
    $teacher = Pengguna::factory()->homeroom()->create(['status' => 'registered']);
    $teacher->profilGuru()->create(['nip' => fake()->unique()->numerify('19################'), 'tipe_guru' => 'homeroom']);

    $this->actingAs($teacher)->get(route('wali-kelas.pengajuan-poin.create'))
        ->assertSuccessful()
        ->assertViewHas('students', fn ($students) => $students->isEmpty());
});

// TS.PGP.009 / TC.PGP.009.001 — submit tanpa mengisi jumlah_poin (memang tidak ada field-nya) tetap tersimpan null (positive, dokumentasi penambahan bukan pengurangan)
test('submitting a jumlah_poin field directly is ignored since the form has none', function () {
    [$teacher, $class] = pengajuanPoinWaliHomeroom();
    $student = pengajuanPoinWaliStudent($class, '60007');

    $this->actingAs($teacher)->post(route('wali-kelas.pengajuan-poin.store'), [
        'profil_siswa_id' => $student->nisn,
        'alasan' => 'Ikut serta lomba robotik nasional.',
        'jumlah_poin' => 999,
    ])->assertRedirect(route('wali-kelas.pengajuan-poin.index'));

    $pengajuan = PengajuanPoin::where('profil_siswa_id', $student->nisn)->firstOrFail();
    expect($pengajuan->jumlah_poin)->toBeNull();
    expect($pengajuan->status)->toBe('pending');
});
