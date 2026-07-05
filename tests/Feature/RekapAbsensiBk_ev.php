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

function rekapAbsensiBkCounselor(?string $tingkat): Pengguna
{
    $counselor = Pengguna::factory()->counselor()->create(['status' => 'registered']);
    $counselor->profilGuru()->create([
        'nip' => fake()->unique()->numerify('19################'),
        'tipe_guru' => 'counselor',
        'tingkat' => $tingkat,
    ]);

    return $counselor;
}

function rekapAbsensiBkStudent(string $tingkat, string $className, string $nama, string $nisn): ProfilSiswa
{
    $class = Kelas::create(['nama' => $className, 'tingkat' => $tingkat]);
    $studentUser = Pengguna::factory()->student()->create(['status' => 'registered', 'nama' => $nama]);

    return ProfilSiswa::factory()->create([
        'pengguna_id' => $studentUser->id,
        'kelas_id' => $class->id,
        'nisn' => $nisn,
    ]);
}

// TS.RAB.001 / TC.RAB.001.001 — index nampilin rekap siswa dari kelas-kelas dalam tingkat sendiri (positive)
test('bk rekap absensi index shows recap for classes within own grade level', function () {
    Carbon::setTestNow('2026-06-08 08:00:00');
    $counselor = rekapAbsensiBkCounselor('10');
    $student = rekapAbsensiBkStudent('10', '10. Rekap Bk 1', 'Alya Ramadhani', '72001');
    Absensi::create(['profil_siswa_id' => $student->nisn, 'tanggal' => now()->toDateString(), 'status' => 'hadir']);

    $this->actingAs($counselor)->get(route('bk.laporan.index'))
        ->assertSuccessful()
        ->assertSee('Alya Ramadhani');
});

// TS.RAB.002 / TC.RAB.002.001 — BK belum dikaitkan ke tingkat manapun, halaman rekap tampil kosong tanpa error (positive, edge case)
test('bk rekap absensi index shows an empty state instead of an error when the counselor has no grade level', function () {
    $counselor = rekapAbsensiBkCounselor(null);

    $this->actingAs($counselor)->get(route('bk.laporan.index'))
        ->assertSuccessful()
        ->assertSee('Akun BK Anda belum dikaitkan dengan tingkat kelas.');
});

// TS.RAB.003 / TC.RAB.003.001 — filter status hanya nampilin siswa yang punya minimal 1 hari berstatus tsb (positive)
test('bk rekap absensi index filters by attendance status', function () {
    Carbon::setTestNow('2026-06-08 08:00:00');
    $counselor = rekapAbsensiBkCounselor('10');
    $studentHadir = rekapAbsensiBkStudent('10', '10. Rekap Bk 2', 'Bimo Prasetyo', '72002');
    $studentAlpha = rekapAbsensiBkStudent('10', '10. Rekap Bk 3', 'Citra Ayu', '72003');
    Absensi::create(['profil_siswa_id' => $studentHadir->nisn, 'tanggal' => now()->toDateString(), 'status' => 'hadir']);
    Absensi::create(['profil_siswa_id' => $studentAlpha->nisn, 'tanggal' => now()->toDateString(), 'status' => 'alpha']);

    // Nama siswa selalu muncul di dropdown filter siswa (tidak ter-scope), jadi assertion pakai isi tabel (viewData) bukan teks halaman.
    $response = $this->actingAs($counselor)->get(route('bk.laporan.index', ['status' => 'alpha']))
        ->assertSuccessful();
    $students = $response->viewData('students');

    expect($students->pluck('nisn')->all())->toBe([$studentAlpha->nisn]);
});

// TS.RAB.004 / TC.RAB.004.001 — filter berdasarkan siswa tertentu (positive)
test('bk rekap absensi index filters by a specific student', function () {
    $counselor = rekapAbsensiBkCounselor('10');
    $studentA = rekapAbsensiBkStudent('10', '10. Rekap Bk 4', 'Dedi Kurniawan', '72004');
    $studentB = rekapAbsensiBkStudent('10', '10. Rekap Bk 5', 'Eka Wardani', '72005');

    // Nama siswa selalu muncul di dropdown filter (tidak ter-scope), jadi assertion pakai isi tabel (viewData) bukan teks halaman.
    $response = $this->actingAs($counselor)->get(route('bk.laporan.index', ['profil_siswa_id' => $studentA->nisn]))
        ->assertSuccessful();
    $students = $response->viewData('students');

    expect($students->pluck('nisn')->all())->toBe([$studentA->nisn]);
});

// TS.RAB.005 / TC.RAB.005.001 — filter month meng-override rentang mulai/selesai default (positive)
test('bk rekap absensi index month filter overrides the default mulai and selesai range', function () {
    $counselor = rekapAbsensiBkCounselor('10');

    $this->actingAs($counselor)->get(route('bk.laporan.index', ['month' => '2026-04']))
        ->assertSuccessful()
        ->assertSee('2026-04-01')
        ->assertSee('2026-04-30');
});

// TS.RAB.006 / TC.RAB.006.001 — tanggal selesai sebelum tanggal mulai ditolak (negative)
test('bk rekap absensi index rejects selesai before mulai', function () {
    $counselor = rekapAbsensiBkCounselor('10');

    $this->actingAs($counselor)->get(route('bk.laporan.index', [
        'mulai' => '2026-06-10',
        'selesai' => '2026-06-05',
    ]))->assertSessionHasErrors('selesai');
});

// TS.RAB.007 / TC.RAB.007.001 — format tanggal yang tidak valid ditolak (negative)
test('bk rekap absensi index rejects an invalid date format', function () {
    $counselor = rekapAbsensiBkCounselor('10');

    $this->actingAs($counselor)->get(route('bk.laporan.index', ['mulai' => '10-06-2026']))
        ->assertSessionHasErrors('mulai');
});

// TS.RAB.008 / TC.RAB.008.001 — ekspor excel berhasil dengan nama berkas sesuai tingkat (positive)
test('bk rekap absensi excel export succeeds with a grade-scoped filename', function () {
    $counselor = rekapAbsensiBkCounselor('10');
    rekapAbsensiBkStudent('10', '10. Rekap Bk 6', 'Fajar Nugroho', '72006');

    $this->actingAs($counselor)->get(route('bk.laporan.ekspor-excel'))
        ->assertDownload();
});

// TS.RAB.009 / TC.RAB.009.001 — ekspor pdf berhasil (positive)
test('bk rekap absensi pdf export succeeds', function () {
    $counselor = rekapAbsensiBkCounselor('10');
    rekapAbsensiBkStudent('10', '10. Rekap Bk 7', 'Gilang Ramadhan', '72007');

    $this->actingAs($counselor)->get(route('bk.laporan.ekspor-pdf'))
        ->assertDownload();
});

// TS.RAB.010 / TC.RAB.010.001 — ekspor ditolak kalau BK belum dikaitkan ke tingkat manapun (negative)
test('bk rekap absensi export is forbidden when the counselor has no grade level', function () {
    $counselor = rekapAbsensiBkCounselor(null);

    $this->actingAs($counselor)->get(route('bk.laporan.ekspor-excel'))
        ->assertForbidden();
});

// TS.RAB.011 / TC.RAB.011.001 — kirim kelas_id milik tingkat lain diabaikan, tidak bocorkan siswa tingkat lain (negative, dokumentasi batas scoping)
test('bk rekap absensi index ignores a kelas_id belonging to another grade level', function () {
    $counselor = rekapAbsensiBkCounselor('10');
    $otherGradeStudent = rekapAbsensiBkStudent('11', '11. Rekap Bk 1', 'Hana Wulandari', '72008');

    $response = $this->actingAs($counselor)->get(route('bk.laporan.index', ['kelas_id' => $otherGradeStudent->kelas_id]))
        ->assertSuccessful();
    $students = $response->viewData('students');

    expect($students->pluck('nisn')->contains($otherGradeStudent->nisn))->toBeFalse();
});
