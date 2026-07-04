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

function monitorAbsensiHomeroom(string $className = '10. Monitor 1'): array
{
    $teacher = Pengguna::factory()->homeroom()->create(['status' => 'registered']);
    $teacher->profilGuru()->create([
        'nip' => fake()->unique()->numerify('19################'),
        'tipe_guru' => 'homeroom',
    ]);
    $class = Kelas::create(['nama' => $className, 'tingkat' => '10', 'wali_kelas_id' => $teacher->id]);

    return [$teacher, $class];
}

function monitorAbsensiStudent(Kelas $class, string $name, string $nis): ProfilSiswa
{
    $student = Pengguna::factory()->student()->create(['nama' => $name, 'status' => 'registered']);

    return ProfilSiswa::factory()->create([
        'pengguna_id' => $student->id,
        'kelas_id' => $class->id,
        'nis' => $nis,
    ]);
}

// TS.MonitorAbsensi.001 / TC.MonitorAbsensi.001.001 — index tampil status + daftar siswa kelas sendiri hari ini (positive)
test('kelas saya index shows status and student list for own class today', function () {
    Carbon::setTestNow('2026-06-01 08:00:00');
    [$teacher, $class] = monitorAbsensiHomeroom();
    $student = monitorAbsensiStudent($class, 'Ayu', '20001');
    Absensi::create(['profil_siswa_id' => $student->nisn, 'tanggal' => now()->toDateString(), 'status' => 'hadir']);

    $this->actingAs($teacher)->get(route('wali-kelas.kelas-saya'))
        ->assertSuccessful()
        ->assertSee('Ayu')
        ->assertViewHas('stats', fn (array $stats): bool => $stats['hadir'] === 1);
});

// TS.MonitorAbsensi.002 / TC.MonitorAbsensi.002.001 — guru tanpa kelas wali, index tetap tampil kosong (positive)
test('kelas saya index shows empty view when teacher has no homeroom class', function () {
    $teacher = Pengguna::factory()->homeroom()->create(['status' => 'registered']);
    $teacher->profilGuru()->create(['nip' => fake()->unique()->numerify('19################'), 'tipe_guru' => 'homeroom']);

    $this->actingAs($teacher)->get(route('wali-kelas.kelas-saya'))
        ->assertSuccessful();
});

// TS.MonitorAbsensi.003 / TC.MonitorAbsensi.003.001 — update status siswa sendiri berhasil membuat record baru (positive)
test('homeroom teacher can create a new attendance record for own student', function () {
    Carbon::setTestNow('2026-06-01 08:00:00');
    [$teacher, $class] = monitorAbsensiHomeroom();
    $student = monitorAbsensiStudent($class, 'Ayu', '20002');

    $this->actingAs($teacher)->put(route('wali-kelas.kelas-saya.absensi.update', $student), [
        'status' => 'hadir',
    ])->assertRedirect(route('wali-kelas.kelas-saya'));

    expect(Absensi::where('profil_siswa_id', $student->nisn)->first()->status)->toBe('hadir');
});

// TS.MonitorAbsensi.004 / TC.MonitorAbsensi.004.001 — update ulang siswa yang record hari ini sudah ada, replace bukan duplikat (positive)
test('homeroom teacher updating an existing attendance record replaces it instead of duplicating', function () {
    Carbon::setTestNow('2026-06-01 08:00:00');
    [$teacher, $class] = monitorAbsensiHomeroom();
    $student = monitorAbsensiStudent($class, 'Ayu', '20003');
    Absensi::create(['profil_siswa_id' => $student->nisn, 'tanggal' => now()->toDateString(), 'status' => 'hadir']);

    $this->actingAs($teacher)->put(route('wali-kelas.kelas-saya.absensi.update', $student), [
        'status' => 'izin',
    ])->assertRedirect(route('wali-kelas.kelas-saya'));

    expect(Absensi::where('profil_siswa_id', $student->nisn)->count())->toBe(1);
    expect(Absensi::where('profil_siswa_id', $student->nisn)->first()->status)->toBe('izin');
});

// TS.MonitorAbsensi.005 / TC.MonitorAbsensi.005.001 — update siswa dari kelas lain ditolak (negative)
test('homeroom teacher cannot update attendance for a student outside own class', function () {
    Carbon::setTestNow('2026-06-01 08:00:00');
    [$teacher] = monitorAbsensiHomeroom();
    [, $otherClass] = monitorAbsensiHomeroom('10. Monitor 2');
    $otherStudent = monitorAbsensiStudent($otherClass, 'Citra', '20004');

    $this->actingAs($teacher)->put(route('wali-kelas.kelas-saya.absensi.update', $otherStudent), [
        'status' => 'hadir',
    ])->assertForbidden();
});

// TS.MonitorAbsensi.006 / TC.MonitorAbsensi.006.001 — update pas hari weekend ditolak dengan flash error (negative)
test('homeroom teacher cannot update attendance on a weekend day', function () {
    Carbon::setTestNow('2026-06-06 08:00:00');
    [$teacher, $class] = monitorAbsensiHomeroom();
    $student = monitorAbsensiStudent($class, 'Ayu', '20005');

    $this->actingAs($teacher)->put(route('wali-kelas.kelas-saya.absensi.update', $student), [
        'status' => 'hadir',
    ])->assertRedirect(route('wali-kelas.kelas-saya'))
        ->assertSessionHas('error');

    expect(Absensi::where('profil_siswa_id', $student->nisn)->exists())->toBeFalse();
});

// TS.MonitorAbsensi.007 / TC.MonitorAbsensi.007.001 — status yang bukan salah satu dari 5 opsi ditolak (negative)
test('homeroom teacher cannot set an invalid attendance status', function () {
    Carbon::setTestNow('2026-06-01 08:00:00');
    [$teacher, $class] = monitorAbsensiHomeroom();
    $student = monitorAbsensiStudent($class, 'Ayu', '20006');

    $this->actingAs($teacher)->put(route('wali-kelas.kelas-saya.absensi.update', $student), [
        'status' => 'libur',
    ])->assertSessionHasErrors('status');
});

// TS.MonitorAbsensi.008 / TC.MonitorAbsensi.008.001 — lihat detail siswa sendiri berhasil (positive)
test('homeroom teacher can view own student detail', function () {
    [$teacher, $class] = monitorAbsensiHomeroom();
    $student = monitorAbsensiStudent($class, 'Ayu', '20007');

    $this->actingAs($teacher)->get(route('wali-kelas.kelas-saya.show', $student))
        ->assertSuccessful();
});

// TS.MonitorAbsensi.009 / TC.MonitorAbsensi.009.001 — lihat detail siswa kelas lain ditolak (negative)
test('homeroom teacher cannot view another class student detail', function () {
    [$teacher] = monitorAbsensiHomeroom();
    [, $otherClass] = monitorAbsensiHomeroom('10. Monitor 3');
    $otherStudent = monitorAbsensiStudent($otherClass, 'Citra', '20008');

    $this->actingAs($teacher)->get(route('wali-kelas.kelas-saya.show', $otherStudent))
        ->assertForbidden();
});

// TS.MonitorAbsensi.010 / TC.MonitorAbsensi.010.001 — endpoint status-absensi (JSON) mengembalikan data yang benar (positive)
test('status absensi endpoint returns correct json stats and rows', function () {
    Carbon::setTestNow('2026-06-01 08:00:00');
    [$teacher, $class] = monitorAbsensiHomeroom();
    $student = monitorAbsensiStudent($class, 'Ayu', '20009');
    Absensi::create(['profil_siswa_id' => $student->nisn, 'tanggal' => now()->toDateString(), 'status' => 'hadir']);

    $this->actingAs($teacher)->get(route('wali-kelas.kelas-saya.status-absensi'))
        ->assertSuccessful()
        ->assertJsonPath('stats.hadir', 1)
        ->assertJsonFragment(['id' => $student->nisn, 'status' => 'hadir']);
});
