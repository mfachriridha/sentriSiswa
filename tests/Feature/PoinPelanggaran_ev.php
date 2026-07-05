<?php

use App\Models\Kelas;
use App\Models\PelanggaranSiswa;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function poinPelanggaranHomeroom(string $className = '10. Poin 1'): array
{
    $teacher = Pengguna::factory()->homeroom()->create(['status' => 'registered']);
    $teacher->profilGuru()->create([
        'nip' => fake()->unique()->numerify('19################'),
        'tipe_guru' => 'homeroom',
    ]);
    $class = Kelas::create(['nama' => $className, 'tingkat' => '10', 'wali_kelas_id' => $teacher->id]);

    return [$teacher, $class];
}

function poinPelanggaranStudent(Kelas $class, string $nis): ProfilSiswa
{
    $student = Pengguna::factory()->student()->create(['status' => 'registered']);

    return ProfilSiswa::factory()->create([
        'pengguna_id' => $student->id,
        'kelas_id' => $class->id,
        'nis' => $nis,
    ]);
}

// TS.PPW.001 / TC.PPW.001.001 — index tampil pelanggaran approved kelas sendiri (positive)
test('pelanggaran index shows approved violations for own class', function () {
    [$teacher, $class] = poinPelanggaranHomeroom();
    $student = poinPelanggaranStudent($class, '50001');
    PelanggaranSiswa::factory()->create([
        'profil_siswa_id' => $student->nisn,
        'status' => 'approved',
        'nama_pelanggaran' => 'Terlambat masuk kelas',
    ]);

    $this->actingAs($teacher)->get(route('wali-kelas.pelanggaran'))
        ->assertSuccessful()
        ->assertSee('Terlambat masuk kelas');
});

// TS.PPW.002 / TC.PPW.002.001 — guru tanpa kelas wali, index tetap tampil kosong (positive)
test('pelanggaran index shows empty view when teacher has no homeroom class', function () {
    $teacher = Pengguna::factory()->homeroom()->create(['status' => 'registered']);
    $teacher->profilGuru()->create(['nip' => fake()->unique()->numerify('19################'), 'tipe_guru' => 'homeroom']);

    $this->actingAs($teacher)->get(route('wali-kelas.pelanggaran'))
        ->assertSuccessful();
});

// TS.PPW.003 / TC.PPW.003.001 — filter berdasarkan profil_siswa_id (positive)
test('pelanggaran index filters by profil_siswa_id', function () {
    [$teacher, $class] = poinPelanggaranHomeroom();
    $studentA = poinPelanggaranStudent($class, '50002');
    $studentB = poinPelanggaranStudent($class, '50003');
    PelanggaranSiswa::factory()->create(['profil_siswa_id' => $studentA->nisn, 'status' => 'approved', 'nama_pelanggaran' => 'Pelanggaran A']);
    PelanggaranSiswa::factory()->create(['profil_siswa_id' => $studentB->nisn, 'status' => 'approved', 'nama_pelanggaran' => 'Pelanggaran B']);

    $this->actingAs($teacher)->get(route('wali-kelas.pelanggaran', ['profil_siswa_id' => $studentA->nisn]))
        ->assertSuccessful()
        ->assertSee('Pelanggaran A')
        ->assertDontSee('Pelanggaran B');
});

// TS.PPW.004 / TC.PPW.004.001 — filter berdasarkan category (positive)
test('pelanggaran index filters by category', function () {
    [$teacher, $class] = poinPelanggaranHomeroom();
    $student = poinPelanggaranStudent($class, '50004');
    PelanggaranSiswa::factory()->create(['profil_siswa_id' => $student->nisn, 'status' => 'approved', 'kategori_pelanggaran' => 'light', 'nama_pelanggaran' => 'Pelanggaran Ringan']);
    PelanggaranSiswa::factory()->create(['profil_siswa_id' => $student->nisn, 'status' => 'approved', 'kategori_pelanggaran' => 'heavy', 'nama_pelanggaran' => 'Pelanggaran Berat']);

    $this->actingAs($teacher)->get(route('wali-kelas.pelanggaran', ['kategori' => 'light']))
        ->assertSuccessful()
        ->assertSee('Pelanggaran Ringan')
        ->assertDontSee('Pelanggaran Berat');
});

// TS.PPW.005 / TC.PPW.005.001 — filter berdasarkan date_from/date_to (positive)
test('pelanggaran index filters by date_from and date_to', function () {
    [$teacher, $class] = poinPelanggaranHomeroom();
    $student = poinPelanggaranStudent($class, '50005');
    PelanggaranSiswa::factory()->create(['profil_siswa_id' => $student->nisn, 'status' => 'approved', 'tanggal_pelanggaran' => '2026-05-01', 'nama_pelanggaran' => 'Pelanggaran Lama']);
    PelanggaranSiswa::factory()->create(['profil_siswa_id' => $student->nisn, 'status' => 'approved', 'tanggal_pelanggaran' => '2026-06-01', 'nama_pelanggaran' => 'Pelanggaran Baru']);

    $this->actingAs($teacher)->get(route('wali-kelas.pelanggaran', [
        'date_from' => '2026-05-15',
        'date_to' => '2026-06-15',
    ]))->assertSuccessful()
        ->assertSee('Pelanggaran Baru')
        ->assertDontSee('Pelanggaran Lama');
});

// TS.PPW.006 / TC.PPW.006.001 — pelanggaran pending/rejected tidak ikut muncul (positive, dokumentasi scoping disetujui())
test('pelanggaran index only shows approved violations, not pending or rejected', function () {
    [$teacher, $class] = poinPelanggaranHomeroom();
    $student = poinPelanggaranStudent($class, '50006');
    PelanggaranSiswa::factory()->create(['profil_siswa_id' => $student->nisn, 'status' => 'approved', 'nama_pelanggaran' => 'Pelanggaran Disetujui']);
    PelanggaranSiswa::factory()->create(['profil_siswa_id' => $student->nisn, 'status' => 'pending', 'nama_pelanggaran' => 'Pelanggaran Menunggu']);
    PelanggaranSiswa::factory()->create(['profil_siswa_id' => $student->nisn, 'status' => 'rejected', 'nama_pelanggaran' => 'Pelanggaran Ditolak']);

    $this->actingAs($teacher)->get(route('wali-kelas.pelanggaran'))
        ->assertSuccessful()
        ->assertSee('Pelanggaran Disetujui')
        ->assertDontSee('Pelanggaran Menunggu')
        ->assertDontSee('Pelanggaran Ditolak');
});

// TS.PPW.007 / TC.PPW.007.001 — pelanggaran kelas lain tidak ikut muncul (positive, dokumentasi cross-class isolation)
test('pelanggaran index does not show violations from another class', function () {
    [$teacher, $class] = poinPelanggaranHomeroom();
    [, $otherClass] = poinPelanggaranHomeroom('10. Poin 2');
    $ownStudent = poinPelanggaranStudent($class, '50007');
    $otherStudent = poinPelanggaranStudent($otherClass, '50008');
    PelanggaranSiswa::factory()->create(['profil_siswa_id' => $ownStudent->nisn, 'status' => 'approved', 'nama_pelanggaran' => 'Pelanggaran Kelas Sendiri']);
    PelanggaranSiswa::factory()->create(['profil_siswa_id' => $otherStudent->nisn, 'status' => 'approved', 'nama_pelanggaran' => 'Pelanggaran Kelas Lain']);

    $this->actingAs($teacher)->get(route('wali-kelas.pelanggaran'))
        ->assertSuccessful()
        ->assertSee('Pelanggaran Kelas Sendiri')
        ->assertDontSee('Pelanggaran Kelas Lain');
});

// TS.PPW.008 / TC.PPW.008.001 — daftar pelanggaran dipaginasi 25 per halaman (positive)
test('pelanggaran index paginates at 25 per page', function () {
    [$teacher, $class] = poinPelanggaranHomeroom();
    $student = poinPelanggaranStudent($class, '50009');

    for ($i = 0; $i < 26; $i++) {
        PelanggaranSiswa::factory()->create([
            'profil_siswa_id' => $student->nisn,
            'status' => 'approved',
            'tanggal_pelanggaran' => now()->subDays($i)->toDateString(),
        ]);
    }

    $response = $this->actingAs($teacher)->get(route('wali-kelas.pelanggaran'))->assertSuccessful();
    $violations = $response->viewData('violations');

    expect($violations->count())->toBe(25);
    expect($violations->total())->toBe(26);
});
