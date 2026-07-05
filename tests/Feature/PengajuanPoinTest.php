<?php

use App\Models\Kelas;
use App\Models\PengajuanPoin;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

function createPengajuanPoinHomeroom(string $className = 'X IPA 1'): array
{
    $teacher = Pengguna::factory()->homeroom()->create(['status' => 'registered']);

    $teacher->profilGuru()->create([
        'nip' => fake()->unique()->numerify('19################'),
        'tipe_guru' => 'wali_kelas',
    ]);

    $class = Kelas::create(['nama' => $className, 'tingkat' => '10']);
    $class->update(['wali_kelas_id' => $teacher->id]);

    return [$teacher, $class];
}

function createPengajuanPoinStudent(Kelas $class): ProfilSiswa
{
    $student = Pengguna::factory()->student()->create(['status' => 'registered']);

    return ProfilSiswa::factory()->create([
        'pengguna_id' => $student->id,
        'kelas_id' => $class->id,
    ]);
}

function createPengajuanPoinStudentAffairs(): Pengguna
{
    DB::statement('PRAGMA ignore_check_constraints = ON');

    $teacher = Pengguna::factory()->studentAffairs()->create(['status' => 'registered']);
    $teacher->profilGuru()->create([
        'nip' => fake()->unique()->numerify('19################'),
        'tipe_guru' => 'kesiswaan',
    ]);

    DB::statement('PRAGMA ignore_check_constraints = OFF');

    return $teacher;
}

test('homeroom teacher submits pengajuan poin for student in own class', function () {
    [$teacher, $class] = createPengajuanPoinHomeroom();
    $student = createPengajuanPoinStudent($class);

    $this->actingAs($teacher)
        ->post(route('wali-kelas.pengajuan-poin.store'), [
            'profil_siswa_id' => $student->nisn,
            'alasan' => 'Juara lomba olimpiade sains tingkat kota.',
        ])
        ->assertRedirect(route('wali-kelas.pengajuan-poin.index'));

    $pengajuan = PengajuanPoin::first();

    expect($pengajuan)->not->toBeNull();
    expect($pengajuan->status)->toBe('pending');
    expect($pengajuan->jumlah_poin)->toBeNull();
    expect($student->fresh()->poin)->toBe(100);
});

test('homeroom teacher cannot submit pengajuan poin for student outside own class', function () {
    [$teacher] = createPengajuanPoinHomeroom();
    [, $otherClass] = createPengajuanPoinHomeroom('X IPA 2');
    $otherStudent = createPengajuanPoinStudent($otherClass);

    $this->actingAs($teacher)
        ->post(route('wali-kelas.pengajuan-poin.store'), [
            'profil_siswa_id' => $otherStudent->nisn,
            'alasan' => 'Alasan apapun.',
        ])
        ->assertForbidden();
});

test('student affairs approves pengajuan poin with jumlah poin and student points increase', function () {
    [$teacher, $class] = createPengajuanPoinHomeroom();
    $student = createPengajuanPoinStudent($class);
    $studentAffairs = createPengajuanPoinStudentAffairs();

    $pengajuan = PengajuanPoin::factory()->create([
        'profil_siswa_id' => $student->nisn,
        'diajukan_oleh_id' => $teacher->id,
        'status' => 'pending',
    ]);

    $this->actingAs($studentAffairs)
        ->put(route('kesiswaan.pengajuan-poin.approve', $pengajuan), [
            'jumlah_poin' => 5,
        ])
        ->assertRedirect(route('kesiswaan.pengajuan-poin.persetujuan'));

    expect($pengajuan->fresh()->status)->toBe('approved');
    expect($pengajuan->fresh()->jumlah_poin)->toBe(5);
    expect($student->fresh()->poin)->toBe(100);
});

test('approved pengajuan poin is capped so student points never exceed 100', function () {
    [$teacher, $class] = createPengajuanPoinHomeroom();
    $student = createPengajuanPoinStudent($class);
    $studentAffairs = createPengajuanPoinStudentAffairs();

    $pengajuan = PengajuanPoin::factory()->create([
        'profil_siswa_id' => $student->nisn,
        'diajukan_oleh_id' => $teacher->id,
        'status' => 'pending',
    ]);

    $this->actingAs($studentAffairs)
        ->put(route('kesiswaan.pengajuan-poin.approve', $pengajuan), [
            'jumlah_poin' => 100,
        ])
        ->assertRedirect(route('kesiswaan.pengajuan-poin.persetujuan'));

    expect($student->fresh()->poin)->toBe(100);
});

test('student affairs rejects pengajuan poin without changing student points', function () {
    [$teacher, $class] = createPengajuanPoinHomeroom();
    $student = createPengajuanPoinStudent($class);
    $studentAffairs = createPengajuanPoinStudentAffairs();

    $pengajuan = PengajuanPoin::factory()->create([
        'profil_siswa_id' => $student->nisn,
        'diajukan_oleh_id' => $teacher->id,
        'status' => 'pending',
    ]);

    $this->actingAs($studentAffairs)
        ->put(route('kesiswaan.pengajuan-poin.reject', $pengajuan), [
            'alasan_penolakan' => 'Belum ada bukti pendukung.',
        ])
        ->assertRedirect(route('kesiswaan.pengajuan-poin.persetujuan'));

    expect($pengajuan->fresh()->status)->toBe('rejected');
    expect($pengajuan->fresh()->jumlah_poin)->toBeNull();
    expect($student->fresh()->poin)->toBe(100);
});
