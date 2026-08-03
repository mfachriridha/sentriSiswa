<?php

use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\Presensi;
use App\Models\ProfilGuru;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

afterEach(function () {
    Carbon::setTestNow();
});

function createHomeroomTeacherWithClass(): array
{
    $teacherUser = Pengguna::factory()->homeroom()->create(['status' => 'registered']);
    $teacherProfile = ProfilGuru::factory()->create(['pengguna_id' => $teacherUser->id, 'tipe_guru' => 'wali_kelas']);
    $class = Kelas::create(['nama' => '10 IPA 1', 'tingkat' => '10', 'wali_kelas_id' => $teacherUser->id]);

    $studentUser = Pengguna::factory()->student()->create(['status' => 'registered', 'nama' => 'Budi Santoso']);
    $studentProfile = ProfilSiswa::factory()->create([
        'pengguna_id' => $studentUser->id,
        'kelas_id' => $class->id,
    ]);

    return [$teacherUser, $class, $studentProfile];
}

test('wali kelas dapat melihat roster presensi tanggal lampau', function () {
    [$teacherUser, $class, $studentProfile] = createHomeroomTeacherWithClass();

    Presensi::create([
        'profil_siswa_id' => $studentProfile->nisn,
        'tanggal' => '2026-07-10',
        'status' => 'alpha',
    ]);

    $this->actingAs($teacherUser)
        ->get('/wali-kelas/kelas-saya?tanggal=2026-07-10')
        ->assertSuccessful()
        ->assertSee('10 Juli 2026')
        ->assertSee('Budi Santoso')
        ->assertSee('Alpha');
});

test('wali kelas dapat mengedit presensi tanggal lampau', function () {
    [$teacherUser, $class, $studentProfile] = createHomeroomTeacherWithClass();

    Presensi::create([
        'profil_siswa_id' => $studentProfile->nisn,
        'tanggal' => '2026-07-10',
        'status' => 'alpha',
    ]);

    $this->actingAs($teacherUser)
        ->put("/wali-kelas/kelas-saya/{$studentProfile->nisn}/absensi", [
            'status' => 'sakit',
            'tanggal' => '2026-07-10',
        ])
        ->assertRedirect('/wali-kelas/kelas-saya?tanggal=2026-07-10')
        ->assertSessionHas('success');

    expect(Presensi::where('profil_siswa_id', $studentProfile->nisn)->whereDate('tanggal', '2026-07-10')->first()->status)->toBe('sakit');
});

test('wali kelas otomatis membuat record presensi baru jika mengedit tanggal lampau yang belum ada record', function () {
    [$teacherUser, $class, $studentProfile] = createHomeroomTeacherWithClass();

    $this->actingAs($teacherUser)
        ->put("/wali-kelas/kelas-saya/{$studentProfile->nisn}/absensi", [
            'status' => 'izin',
            'tanggal' => '2026-07-07',
        ])
        ->assertRedirect('/wali-kelas/kelas-saya?tanggal=2026-07-07');

    $presensi = Presensi::where('profil_siswa_id', $studentProfile->nisn)->whereDate('tanggal', '2026-07-07')->first();
    expect($presensi)->not->toBeNull()
        ->and($presensi->status)->toBe('izin');
});

test('dashboard wali kelas mengarahkan link detail siswa alpha ke halaman detail siswa yang benar', function () {
    Carbon::setTestNow('2026-07-06 08:00:00');
    [$teacherUser, $class, $studentProfile] = createHomeroomTeacherWithClass();

    Presensi::create([
        'profil_siswa_id' => $studentProfile->nisn,
        'tanggal' => '2026-07-06',
        'status' => 'alpha',
    ]);

    $this->actingAs($teacherUser)
        ->get('/wali-kelas/dashboard')
        ->assertSuccessful()
        ->assertSee('/wali-kelas/kelas-saya/'.$studentProfile->nisn);
});

test('halaman detail siswa wali kelas menampilkan jumlah alpha siswa yang sesuai', function () {
    Carbon::setTestNow('2026-07-06 08:00:00');
    [$teacherUser, $class, $studentProfile] = createHomeroomTeacherWithClass();

    Presensi::create([
        'profil_siswa_id' => $studentProfile->nisn,
        'tanggal' => '2026-07-06',
        'status' => 'alpha',
    ]);

    $this->actingAs($teacherUser)
        ->get("/wali-kelas/kelas-saya/{$studentProfile->nisn}")
        ->assertSuccessful()
        ->assertSee('Budi Santoso')
        ->assertSeeInOrder(['1', 'kali']);
});
