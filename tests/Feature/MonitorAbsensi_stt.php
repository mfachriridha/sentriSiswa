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

function monitorAbsensiSttHomeroom(string $className = '10. Monitor STT 1'): array
{
    $teacher = Pengguna::factory()->homeroom()->create(['status' => 'registered']);
    $teacher->profilGuru()->create([
        'nip' => fake()->unique()->numerify('19################'),
        'tipe_guru' => 'wali_kelas',
    ]);
    $class = Kelas::create(['nama' => $className, 'tingkat' => '10', 'wali_kelas_id' => $teacher->id]);

    return [$teacher, $class];
}

function monitorAbsensiSttStudent(Kelas $class, string $nis): ProfilSiswa
{
    $student = Pengguna::factory()->student()->create(['status' => 'registered']);

    return ProfilSiswa::factory()->create([
        'pengguna_id' => $student->id,
        'kelas_id' => $class->id,
        'nis' => $nis,
    ]);
}

function monitorAbsensiSttIsKosong(string $nisn): bool
{
    return ! Absensi::where('profil_siswa_id', $nisn)->whereDate('tanggal', now()->toDateString())->exists();
}

// State machine under test: RekapHariIniKosong ⇄ RekapHariIniAda (record absensi hari ini untuk 1 siswa)

// TS.MOA.011 / TC.MOA.011.001 — Kosong → update valid → jadi Ada (record baru dibuat) (positive)
test('attendance record transitions from kosong to ada after a valid update', function () {
    Carbon::setTestNow('2026-06-01 08:00:00');
    [$teacher, $class] = monitorAbsensiSttHomeroom();
    $student = monitorAbsensiSttStudent($class, '30001');
    expect(monitorAbsensiSttIsKosong($student->nisn))->toBeTrue();

    $this->actingAs($teacher)->put(route('wali-kelas.kelas-saya.absensi.update', $student), [
        'status' => 'hadir',
    ])->assertRedirect(route('wali-kelas.kelas-saya'));

    expect(monitorAbsensiSttIsKosong($student->nisn))->toBeFalse();
});

// TS.MOA.012 / TC.MOA.012.001 — Ada (status A) → update ke status B → tetap Ada, tapi status ke-replace (positive)
test('attendance record stays ada and replaces status when updated again', function () {
    Carbon::setTestNow('2026-06-01 08:00:00');
    [$teacher, $class] = monitorAbsensiSttHomeroom();
    $student = monitorAbsensiSttStudent($class, '30002');
    Absensi::create(['profil_siswa_id' => $student->nisn, 'tanggal' => now()->toDateString(), 'status' => 'hadir']);

    $this->actingAs($teacher)->put(route('wali-kelas.kelas-saya.absensi.update', $student), [
        'status' => 'sakit',
    ])->assertRedirect(route('wali-kelas.kelas-saya'));

    expect(monitorAbsensiSttIsKosong($student->nisn))->toBeFalse();
    expect(Absensi::where('profil_siswa_id', $student->nisn)->count())->toBe(1);
    expect(Absensi::where('profil_siswa_id', $student->nisn)->first()->status)->toBe('sakit');
});

// TS.MOA.013 / TC.MOA.013.001 — Ada → percobaan update saat weekend → tetap Ada, tidak berubah (positive, blocked no-op)
test('attendance record stays ada and unchanged when update is attempted on a weekend', function () {
    Carbon::setTestNow('2026-06-01 08:00:00');
    [$teacher, $class] = monitorAbsensiSttHomeroom();
    $student = monitorAbsensiSttStudent($class, '30003');
    Absensi::create(['profil_siswa_id' => $student->nisn, 'tanggal' => now()->toDateString(), 'status' => 'hadir']);

    Carbon::setTestNow('2026-06-06 08:00:00');
    $this->actingAs($teacher)->put(route('wali-kelas.kelas-saya.absensi.update', $student), [
        'status' => 'izin',
    ])->assertSessionHas('error');

    Carbon::setTestNow('2026-06-01 08:00:00');
    expect(monitorAbsensiSttIsKosong($student->nisn))->toBeFalse();
    expect(Absensi::where('profil_siswa_id', $student->nisn)->first()->status)->toBe('hadir');
});

// TS.MOA.014 / TC.MOA.014.001 — Kosong → percobaan update saat weekend → tetap Kosong, tidak berubah (positive, blocked no-op)
test('attendance record stays kosong when update is attempted on a weekend', function () {
    Carbon::setTestNow('2026-06-06 08:00:00');
    [$teacher, $class] = monitorAbsensiSttHomeroom();
    $student = monitorAbsensiSttStudent($class, '30004');
    expect(monitorAbsensiSttIsKosong($student->nisn))->toBeTrue();

    $this->actingAs($teacher)->put(route('wali-kelas.kelas-saya.absensi.update', $student), [
        'status' => 'hadir',
    ])->assertSessionHas('error');

    expect(monitorAbsensiSttIsKosong($student->nisn))->toBeTrue();
});
