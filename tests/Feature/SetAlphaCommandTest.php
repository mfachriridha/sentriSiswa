<?php

use App\Models\Pengguna;
use App\Models\Presensi;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

afterEach(function () {
    Carbon::setTestNow();
});

function setupStudentProfileForTest(string $nama = 'Budi Santoso', string $nisn = '1234567890'): ProfilSiswa
{
    $pengguna = Pengguna::factory()->student()->create([
        'nama' => $nama,
        'status' => 'registered',
    ]);

    return ProfilSiswa::factory()->create([
        'pengguna_id' => $pengguna->id,
        'nisn' => $nisn,
    ]);
}

test('command absensi:set-alpha mengubah status presensi semua siswa menjadi alpha', function () {
    Carbon::setTestNow('2026-07-06 08:00:00'); // Senin
    $student = setupStudentProfileForTest();

    $this->artisan('absensi:set-alpha')
        ->expectsOutputToContain('Berhasil mengubah status presensi 1 siswa menjadi Alpha')
        ->assertSuccessful();

    $presensi = Presensi::where('profil_siswa_id', $student->nisn)->whereDate('tanggal', '2026-07-06')->first();
    expect($presensi)->not->toBeNull()
        ->and($presensi->status)->toBe('alpha');
});

test('command absensi:set-alpha dapat memfilter siswa tertentu', function () {
    Carbon::setTestNow('2026-07-06 08:00:00');
    $student1 = setupStudentProfileForTest('Ahmad Fauzi', '1111111111');
    $student2 = setupStudentProfileForTest('Budi Santoso', '2222222222');

    $this->artisan('absensi:set-alpha', ['--siswa' => 'Ahmad'])
        ->expectsOutputToContain('Berhasil mengubah status presensi 1 siswa menjadi Alpha')
        ->assertSuccessful();

    expect(Presensi::where('profil_siswa_id', $student1->nisn)->whereDate('tanggal', '2026-07-06')->first()?->status)->toBe('alpha')
        ->and(Presensi::where('profil_siswa_id', $student2->nisn)->whereDate('tanggal', '2026-07-06')->first())->toBeNull();
});

test('command absensi:set-alpha menolak hari libur kecuali memakai option --force', function () {
    Carbon::setTestNow('2026-07-05 08:00:00'); // Minggu
    $student = setupStudentProfileForTest();

    $this->artisan('absensi:set-alpha')
        ->expectsOutputToContain('bukan hari aktif absensi')
        ->assertSuccessful();

    expect(Presensi::count())->toBe(0);

    $this->artisan('absensi:set-alpha', ['--force' => true])
        ->expectsOutputToContain('Berhasil mengubah status presensi 1 siswa menjadi Alpha')
        ->assertSuccessful();

    expect(Presensi::where('profil_siswa_id', $student->nisn)->whereDate('tanggal', '2026-07-05')->first()?->status)->toBe('alpha');
});
