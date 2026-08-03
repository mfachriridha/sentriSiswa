<?php

use App\Models\Pengaturan;
use App\Models\Pengguna;
use App\Models\Presensi;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

afterEach(function () {
    Carbon::setTestNow();
});

function createStudentProfile(): ProfilSiswa
{
    $pengguna = Pengguna::factory()->student()->create([
        'status' => 'registered',
    ]);

    return ProfilSiswa::factory()->create([
        'pengguna_id' => $pengguna->id,
    ]);
}

test('daily attendance command creates belum absen records on weekday', function () {
    Carbon::setTestNow('2026-06-01 00:00:00');
    $student = createStudentProfile();

    $this->artisan('attendance:create-daily')
        ->expectsOutput('Created 1 attendance records for 2026-06-01.')
        ->assertSuccessful();

    $attendance = Presensi::firstOrFail();

    expect($attendance->profil_siswa_id)->toBe($student->nisn)
        ->and($attendance->tanggal->toDateString())->toBe('2026-06-01')
        ->and($attendance->status)->toBe('belum_absen');
});

test('daily attendance command does nothing on weekend', function () {
    Carbon::setTestNow('2026-06-06 00:00:00');
    createStudentProfile();

    $this->artisan('attendance:create-daily')
        ->expectsOutput('Hari ini bukan hari aktif absensi. Tidak ada record yang dibuat.')
        ->assertSuccessful();

    expect(Presensi::count())->toBe(0);
});

test('unmarked attendance command converts records to alpha after attendance time', function () {
    Carbon::setTestNow('2026-06-01 07:06:00');
    $student = createStudentProfile();
    Pengaturan::set('attendance_end_time', '07:00');
    Presensi::create([
        'profil_siswa_id' => $student->nisn,
        'tanggal' => '2026-06-01',
        'status' => 'belum_absen',
    ]);

    $this->artisan('attendance:update-unmarked')
        ->expectsOutput('Updated 1 records from belum_absen to alpha for 2026-06-01.')
        ->assertSuccessful();

    expect(Presensi::firstOrFail()->status)->toBe('alpha');
});

test('unmarked attendance command does nothing on weekend', function () {
    Carbon::setTestNow('2026-06-06 07:06:00');
    $student = createStudentProfile();
    Presensi::create([
        'profil_siswa_id' => $student->nisn,
        'tanggal' => '2026-06-06',
        'status' => 'belum_absen',
    ]);

    $this->artisan('attendance:update-unmarked')
        ->expectsOutput('Hari ini bukan hari aktif absensi. Tidak ada status yang diperbarui.')
        ->assertSuccessful();

    expect(Presensi::firstOrFail()->status)->toBe('belum_absen');
});
