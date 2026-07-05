<?php

use App\Models\JenisPelanggaran;
use App\Models\PelanggaranSiswa;
use App\Models\PengajuanPoin;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function poinSiswaStudent(): Pengguna
{
    $student = Pengguna::factory()->student()->create(['status' => 'registered']);
    ProfilSiswa::factory()->create([
        'pengguna_id' => $student->id,
        'nis' => fake()->unique()->numerify('#####'),
    ]);

    return $student;
}

// TS.POS.001 / TC.POS.001.001 — index cuma nampilin pelanggaran yang sudah disetujui (positive)
test('poin siswa index shows only approved violations', function () {
    $student = poinSiswaStudent();
    $type = JenisPelanggaran::factory()->create(['kategori' => 'light', 'pengurangan_poin' => 10]);
    PelanggaranSiswa::factory()->create([
        'profil_siswa_id' => $student->profilSiswa->nisn,
        'jenis_pelanggaran_id' => $type->id,
        'nama_pelanggaran' => 'Terlambat Disetujui',
        'status' => 'approved',
    ]);

    $this->actingAs($student)->get(route('siswa.poin'))
        ->assertSuccessful()
        ->assertSee('Terlambat Disetujui');
});

// TS.POS.002 / TC.POS.002.001 — pelanggaran pending/ditolak tidak ikut tampil (negative, dokumentasi scoping)
test('poin siswa index does not show pending or rejected violations', function () {
    $student = poinSiswaStudent();
    $typePending = JenisPelanggaran::factory()->create(['kategori' => 'light', 'pengurangan_poin' => 10]);
    $typeRejected = JenisPelanggaran::factory()->create(['kategori' => 'light', 'pengurangan_poin' => 10]);
    PelanggaranSiswa::factory()->create([
        'profil_siswa_id' => $student->profilSiswa->nisn,
        'jenis_pelanggaran_id' => $typePending->id,
        'nama_pelanggaran' => 'Masih Menunggu',
        'status' => 'pending',
    ]);
    PelanggaranSiswa::factory()->create([
        'profil_siswa_id' => $student->profilSiswa->nisn,
        'jenis_pelanggaran_id' => $typeRejected->id,
        'nama_pelanggaran' => 'Sudah Ditolak',
        'status' => 'rejected',
    ]);

    $this->actingAs($student)->get(route('siswa.poin'))
        ->assertSuccessful()
        ->assertDontSee('Masih Menunggu')
        ->assertDontSee('Sudah Ditolak');
});

// TS.POS.003 / TC.POS.003.001 — sisa poin dihitung dari pelanggaran disetujui dikurangi ditambah pengajuan poin disetujui (positive)
test('poin siswa index computes remaining points from approved violations and point additions', function () {
    $student = poinSiswaStudent();
    $type = JenisPelanggaran::factory()->create(['kategori' => 'medium', 'pengurangan_poin' => 30]);
    PelanggaranSiswa::factory()->create([
        'profil_siswa_id' => $student->profilSiswa->nisn,
        'jenis_pelanggaran_id' => $type->id,
        'pengurangan_poin' => 30,
        'status' => 'approved',
    ]);
    PengajuanPoin::factory()->create([
        'profil_siswa_id' => $student->profilSiswa->nisn,
        'status' => 'approved',
        'jumlah_poin' => 10,
    ]);

    $this->actingAs($student)->get(route('siswa.poin'))
        ->assertSuccessful()
        ->assertSeeInOrder(['Sisa Poin Disiplin', '80']);
});

// TS.POS.004 / TC.POS.004.001 — belum pernah ada pelanggaran nampilin sisa poin 100 dan pesan kosong (positive)
test('poin siswa index shows default 100 points and an empty message when there are no violations', function () {
    $student = poinSiswaStudent();

    $this->actingAs($student)->get(route('siswa.poin'))
        ->assertSuccessful()
        ->assertSeeInOrder(['Sisa Poin Disiplin', '100'])
        ->assertSee('Belum ada catatan pelanggaran untuk Anda.');
});

// TS.POS.005 / TC.POS.005.001 — siswa cuma lihat pelanggaran miliknya sendiri, bukan siswa lain (negative, dokumentasi keamanan)
test('poin siswa index only shows the logged in student own violations', function () {
    $studentA = poinSiswaStudent();
    $studentB = poinSiswaStudent();
    $type = JenisPelanggaran::factory()->create(['kategori' => 'light', 'pengurangan_poin' => 10]);
    PelanggaranSiswa::factory()->create([
        'profil_siswa_id' => $studentB->profilSiswa->nisn,
        'jenis_pelanggaran_id' => $type->id,
        'nama_pelanggaran' => 'Pelanggaran Siswa Lain',
        'status' => 'approved',
    ]);

    $this->actingAs($studentA)->get(route('siswa.poin'))
        ->assertSuccessful()
        ->assertDontSee('Pelanggaran Siswa Lain');
});
