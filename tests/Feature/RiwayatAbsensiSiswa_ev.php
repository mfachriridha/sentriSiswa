<?php

use App\Models\Absensi;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

afterEach(function () {
    Carbon::setTestNow();
});

function riwayatAbsensiSiswaStudent(): Pengguna
{
    $student = Pengguna::factory()->student()->create(['status' => 'registered']);
    ProfilSiswa::factory()->create([
        'pengguna_id' => $student->id,
        'nis' => fake()->unique()->numerify('#####'),
    ]);

    return $student;
}

// TS.RAS.001 / TC.RAS.001.001 — tanpa parameter bulan nampilin catatan bulan berjalan (positive)
test('attendance history shows the current month when no month parameter is given', function () {
    Carbon::setTestNow('2026-06-15 08:00:00');
    $student = riwayatAbsensiSiswaStudent();
    Absensi::create(['profil_siswa_id' => $student->profilSiswa->nisn, 'tanggal' => '2026-06-10', 'status' => 'hadir']);

    $this->actingAs($student)->get(route('siswa.absensi.riwayat'))
        ->assertSuccessful()
        ->assertSee('Juni 2026');
});

// TS.RAS.002 / TC.RAS.002.001 — parameter bulan valid nampilin catatan bulan tersebut (positive)
test('attendance history filters by a valid month parameter', function () {
    Carbon::setTestNow('2026-06-15 08:00:00');
    $student = riwayatAbsensiSiswaStudent();
    Absensi::create(['profil_siswa_id' => $student->profilSiswa->nisn, 'tanggal' => '2026-04-10', 'status' => 'hadir']);
    Absensi::create(['profil_siswa_id' => $student->profilSiswa->nisn, 'tanggal' => '2026-06-10', 'status' => 'izin']);

    $response = $this->actingAs($student)->get(route('siswa.absensi.riwayat', ['month' => '2026-04']))
        ->assertSuccessful()
        ->assertSee('April 2026');
    $attendances = $response->viewData('attendances');

    expect($attendances->pluck('tanggal')->map->toDateString()->all())->toBe(['2026-04-10']);
});

// TS.RAS.003 / TC.RAS.003.001 — parameter bulan invalid fallback ke bulan berjalan tanpa error (positive, dokumentasi graceful fallback)
test('attendance history falls back to the current month when the month parameter is invalid', function () {
    Carbon::setTestNow('2026-06-15 08:00:00');
    $student = riwayatAbsensiSiswaStudent();

    $this->actingAs($student)->get(route('siswa.absensi.riwayat', ['month' => 'not-a-month']))
        ->assertSuccessful()
        ->assertSee('Juni 2026');
});

// TS.RAS.004 / TC.RAS.004.001 — belum ada catatan absensi nampilin pesan kosong (positive)
test('attendance history shows an empty message when there are no records for the month', function () {
    Carbon::setTestNow('2026-06-15 08:00:00');
    $student = riwayatAbsensiSiswaStudent();

    $this->actingAs($student)->get(route('siswa.absensi.riwayat'))
        ->assertSuccessful()
        ->assertSee('Belum ada catatan absensi pada bulan ini.');
});

// TS.RAS.005 / TC.RAS.005.001 — catatan diurutkan dari tanggal terbaru (positive)
test('attendance history orders records from the most recent date', function () {
    Carbon::setTestNow('2026-06-15 08:00:00');
    $student = riwayatAbsensiSiswaStudent();
    Absensi::create(['profil_siswa_id' => $student->profilSiswa->nisn, 'tanggal' => '2026-06-02', 'status' => 'hadir']);
    Absensi::create(['profil_siswa_id' => $student->profilSiswa->nisn, 'tanggal' => '2026-06-10', 'status' => 'izin']);

    $response = $this->actingAs($student)->get(route('siswa.absensi.riwayat'))
        ->assertSuccessful();
    $attendances = $response->viewData('attendances');

    expect($attendances->pluck('tanggal')->map->toDateString()->all())->toBe(['2026-06-10', '2026-06-02']);
});

// TS.RAS.006 / TC.RAS.006.001 — siswa cuma lihat riwayat absensi miliknya sendiri, bukan siswa lain (negative, dokumentasi keamanan)
test('attendance history only shows the logged in student own records', function () {
    Carbon::setTestNow('2026-06-15 08:00:00');
    $studentA = riwayatAbsensiSiswaStudent();
    $studentB = riwayatAbsensiSiswaStudent();
    Absensi::create(['profil_siswa_id' => $studentA->profilSiswa->nisn, 'tanggal' => '2026-06-10', 'status' => 'hadir']);
    Absensi::create(['profil_siswa_id' => $studentB->profilSiswa->nisn, 'tanggal' => '2026-06-11', 'status' => 'izin']);

    $response = $this->actingAs($studentA)->get(route('siswa.absensi.riwayat'))
        ->assertSuccessful();
    $attendances = $response->viewData('attendances');

    expect($attendances->pluck('tanggal')->map->toDateString()->all())->toBe(['2026-06-10']);
});

// TS.RAS.007 / TC.RAS.007.001 — catatan yang tanggalnya persis di hari terakhir bulan tetap ikut tampil (positive, regresi whereDate)
test('attendance history includes a record dated exactly on the last day of the month', function () {
    Carbon::setTestNow('2026-06-15 08:00:00');
    $student = riwayatAbsensiSiswaStudent();
    Absensi::create(['profil_siswa_id' => $student->profilSiswa->nisn, 'tanggal' => '2026-06-30', 'status' => 'hadir']);

    $response = $this->actingAs($student)->get(route('siswa.absensi.riwayat'))
        ->assertSuccessful();
    $attendances = $response->viewData('attendances');

    expect($attendances->pluck('tanggal')->map->toDateString()->all())->toBe(['2026-06-30']);
});
