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

function rekapAbsensiHomeroom(string $className = '10. Rekap 1'): array
{
    $teacher = Pengguna::factory()->homeroom()->create(['status' => 'registered']);
    $teacher->profilGuru()->create([
        'nip' => fake()->unique()->numerify('19################'),
        'tipe_guru' => 'homeroom',
    ]);
    $class = Kelas::create(['nama' => $className, 'tingkat' => '10', 'wali_kelas_id' => $teacher->id]);

    return [$teacher, $class];
}

function rekapAbsensiStudent(Kelas $class, string $nis): ProfilSiswa
{
    $student = Pengguna::factory()->student()->create(['status' => 'registered']);

    return ProfilSiswa::factory()->create([
        'pengguna_id' => $student->id,
        'kelas_id' => $class->id,
        'nis' => $nis,
    ]);
}

// TS.RekapAbsensi.001 / TC.RekapAbsensi.001.001 — index tampil rekap dengan rentang default (positive)
test('rekap absensi index shows recap with default date range', function () {
    Carbon::setTestNow('2026-06-08 08:00:00');
    [$teacher, $class] = rekapAbsensiHomeroom();
    $student = rekapAbsensiStudent($class, '40001');
    Absensi::create(['profil_siswa_id' => $student->nisn, 'tanggal' => '2026-06-01', 'status' => 'hadir']);

    $this->actingAs($teacher)->get(route('wali-kelas.absensi.index'))
        ->assertSuccessful()
        ->assertViewHas('stats', fn (array $stats) => $stats[$student->nisn]['hadir'] === 1);
});

// TS.RekapAbsensi.002 / TC.RekapAbsensi.002.001 — filter month meng-override mulai/selesai (positive)
test('rekap absensi filter by month overrides mulai and selesai', function () {
    Carbon::setTestNow('2026-06-08 08:00:00');
    [$teacher, $class] = rekapAbsensiHomeroom();
    $student = rekapAbsensiStudent($class, '40002');
    Absensi::create(['profil_siswa_id' => $student->nisn, 'tanggal' => '2026-05-15', 'status' => 'hadir']);

    $this->actingAs($teacher)->get(route('wali-kelas.absensi.index', [
        'mulai' => '2026-06-01',
        'selesai' => '2026-06-08',
        'month' => '2026-05',
    ]))->assertSuccessful()
        ->assertViewHas('stats', fn (array $stats) => $stats[$student->nisn]['hadir'] === 1);
});

// TS.RekapAbsensi.003 / TC.RekapAbsensi.003.001 — filter profil_siswa_id spesifik (positive)
test('rekap absensi filters to a specific student', function () {
    Carbon::setTestNow('2026-06-08 08:00:00');
    [$teacher, $class] = rekapAbsensiHomeroom();
    $studentA = rekapAbsensiStudent($class, '40003');
    $studentB = rekapAbsensiStudent($class, '40004');

    $this->actingAs($teacher)->get(route('wali-kelas.absensi.index', [
        'profil_siswa_id' => $studentA->nisn,
    ]))->assertSuccessful()
        ->assertViewHas('students', fn ($students) => $students->count() === 1 && $students->first()->is($studentA));
});

// TS.RekapAbsensi.004 / TC.RekapAbsensi.004.001 — filter status (positive)
test('rekap absensi filters students by status', function () {
    Carbon::setTestNow('2026-06-08 08:00:00');
    [$teacher, $class] = rekapAbsensiHomeroom();
    $studentA = rekapAbsensiStudent($class, '40005');
    $studentB = rekapAbsensiStudent($class, '40006');
    Absensi::create(['profil_siswa_id' => $studentA->nisn, 'tanggal' => '2026-06-01', 'status' => 'alpha']);

    $this->actingAs($teacher)->get(route('wali-kelas.absensi.index', ['status' => 'alpha']))
        ->assertSuccessful()
        ->assertViewHas('students', fn ($students) => $students->count() === 1 && $students->first()->is($studentA));
});

// TS.RekapAbsensi.005 / TC.RekapAbsensi.005.001 — guru tanpa kelas wali, ekspor excel ditolak (negative)
test('rekap absensi export excel is forbidden without a homeroom class', function () {
    $teacher = Pengguna::factory()->homeroom()->create(['status' => 'registered']);
    $teacher->profilGuru()->create(['nip' => fake()->unique()->numerify('19################'), 'tipe_guru' => 'homeroom']);

    $this->actingAs($teacher)->get(route('wali-kelas.absensi.ekspor-excel'))
        ->assertForbidden();
});

// TS.RekapAbsensi.006 / TC.RekapAbsensi.006.001 — guru tanpa kelas wali, ekspor pdf ditolak (negative)
test('rekap absensi export pdf is forbidden without a homeroom class', function () {
    $teacher = Pengguna::factory()->homeroom()->create(['status' => 'registered']);
    $teacher->profilGuru()->create(['nip' => fake()->unique()->numerify('19################'), 'tipe_guru' => 'homeroom']);

    $this->actingAs($teacher)->get(route('wali-kelas.absensi.ekspor-pdf'))
        ->assertForbidden();
});

// TS.RekapAbsensi.007 / TC.RekapAbsensi.007.001 — tanggal selesai sebelum mulai ditolak (negative)
test('rekap absensi rejects selesai before mulai', function () {
    [$teacher, $class] = rekapAbsensiHomeroom();

    $this->actingAs($teacher)->get(route('wali-kelas.absensi.index', [
        'mulai' => '2026-06-08',
        'selesai' => '2026-06-01',
    ]))->assertSessionHasErrors('selesai');
});

// TS.RekapAbsensi.008 / TC.RekapAbsensi.008.001 — format tanggal salah ditolak (negative)
test('rekap absensi rejects an invalid date format', function () {
    [$teacher, $class] = rekapAbsensiHomeroom();

    $this->actingAs($teacher)->get(route('wali-kelas.absensi.index', [
        'mulai' => '08-06-2026',
    ]))->assertSessionHasErrors('mulai');
});

// TS.RekapAbsensi.009 / TC.RekapAbsensi.009.001 — profil_siswa_id yang tidak ada ditolak (negative)
test('rekap absensi rejects a non-existent profil_siswa_id', function () {
    [$teacher, $class] = rekapAbsensiHomeroom();

    $this->actingAs($teacher)->get(route('wali-kelas.absensi.index', [
        'profil_siswa_id' => '9999999999',
    ]))->assertSessionHasErrors('profil_siswa_id');
});

// TS.RekapAbsensi.010 / TC.RekapAbsensi.010.001 — ekspor excel berhasil (positive)
test('rekap absensi export excel succeeds', function () {
    Carbon::setTestNow('2026-06-08 08:00:00');
    [$teacher, $class] = rekapAbsensiHomeroom();
    rekapAbsensiStudent($class, '40007');

    $this->actingAs($teacher)->get(route('wali-kelas.absensi.ekspor-excel', [
        'mulai' => '2026-06-01',
        'selesai' => '2026-06-08',
    ]))->assertDownload();
});

// TS.RekapAbsensi.011 / TC.RekapAbsensi.011.001 — ekspor pdf berhasil (positive)
test('rekap absensi export pdf succeeds', function () {
    Carbon::setTestNow('2026-06-08 08:00:00');
    [$teacher, $class] = rekapAbsensiHomeroom();
    rekapAbsensiStudent($class, '40008');

    $this->actingAs($teacher)->get(route('wali-kelas.absensi.ekspor-pdf', [
        'mulai' => '2026-06-01',
        'selesai' => '2026-06-08',
    ]))->assertDownload();
});
