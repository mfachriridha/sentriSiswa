<?php

use App\Models\JenisPelanggaran;
use App\Models\Kelas;
use App\Models\PelanggaranSiswa;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function laporanPelanggaranActor(): Pengguna
{
    $studentAffairs = Pengguna::factory()->studentAffairs()->create(['status' => 'registered']);
    $studentAffairs->profilGuru()->create([
        'nip' => fake()->unique()->numerify('19################'),
        'tipe_guru' => 'student_affairs',
    ]);

    return $studentAffairs;
}

function laporanPelanggaranStudent(string $tingkat, string $className, string $nisn): ProfilSiswa
{
    $class = Kelas::create(['nama' => $className, 'tingkat' => $tingkat]);
    $studentUser = Pengguna::factory()->student()->create(['status' => 'registered']);

    return ProfilSiswa::factory()->create([
        'pengguna_id' => $studentUser->id,
        'kelas_id' => $class->id,
        'nisn' => $nisn,
    ]);
}

function laporanPelanggaranRecord(ProfilSiswa $student, string $namaPelanggaran, string $kategori, int $poin, string $tanggal): PelanggaranSiswa
{
    $type = JenisPelanggaran::factory()->create(['nama' => $namaPelanggaran, 'kategori' => $kategori, 'pengurangan_poin' => $poin]);

    return PelanggaranSiswa::factory()->create([
        'profil_siswa_id' => $student->nisn,
        'jenis_pelanggaran_id' => $type->id,
        'nama_pelanggaran' => $namaPelanggaran,
        'kategori_pelanggaran' => $kategori,
        'pengurangan_poin' => $poin,
        'tanggal_pelanggaran' => $tanggal,
        'status' => 'approved',
    ]);
}

// TS.LAP.001 / TC.LAP.001.001 — index nampilin semua pelanggaran seluruh sekolah tanpa batas tingkat (positive)
test('violation report index shows violations from every grade level school-wide', function () {
    $kesiswaan = laporanPelanggaranActor();
    $studentGradeTen = laporanPelanggaranStudent('10', '10. Laporan 1', '95001');
    $studentGradeTwelve = laporanPelanggaranStudent('12', '12. Laporan 1', '95002');
    laporanPelanggaranRecord($studentGradeTen, 'Terlambat Laporan', 'light', 10, '2026-03-01');
    laporanPelanggaranRecord($studentGradeTwelve, 'Berkelahi Laporan', 'heavy', 60, '2026-03-02');

    $this->actingAs($kesiswaan)->get(route('kesiswaan.laporan.index'))
        ->assertSuccessful()
        ->assertSee('Terlambat Laporan')
        ->assertSee('Berkelahi Laporan');
});

// TS.LAP.002 / TC.LAP.002.001 — filter tanggal mulai/selesai (positive)
test('violation report filters by mulai and selesai date range', function () {
    $kesiswaan = laporanPelanggaranActor();
    $student = laporanPelanggaranStudent('10', '10. Laporan 2', '95003');
    laporanPelanggaranRecord($student, 'Dalam Rentang', 'light', 10, '2026-04-10');
    laporanPelanggaranRecord($student, 'Luar Rentang', 'light', 10, '2026-05-20');

    $this->actingAs($kesiswaan)->get(route('kesiswaan.laporan.index', ['mulai' => '2026-04-01', 'selesai' => '2026-04-30']))
        ->assertSuccessful()
        ->assertSee('Dalam Rentang')
        ->assertDontSee('Luar Rentang');
});

// TS.LAP.003 / TC.LAP.003.001 — filter berdasarkan kelas_id (positive)
test('violation report filters by kelas_id', function () {
    $kesiswaan = laporanPelanggaranActor();
    $studentA = laporanPelanggaranStudent('10', '10. Laporan 3', '95004');
    $studentB = laporanPelanggaranStudent('10', '10. Laporan 4', '95005');
    laporanPelanggaranRecord($studentA, 'Kelas A Laporan', 'light', 10, '2026-03-05');
    laporanPelanggaranRecord($studentB, 'Kelas B Laporan', 'light', 10, '2026-03-05');

    $this->actingAs($kesiswaan)->get(route('kesiswaan.laporan.index', ['kelas_id' => $studentA->kelas_id]))
        ->assertSuccessful()
        ->assertSee('Kelas A Laporan')
        ->assertDontSee('Kelas B Laporan');
});

// TS.LAP.004 / TC.LAP.004.001 — filter berdasarkan tingkat (positive)
test('violation report filters by tingkat', function () {
    $kesiswaan = laporanPelanggaranActor();
    $studentTen = laporanPelanggaranStudent('10', '10. Laporan 5', '95006');
    $studentEleven = laporanPelanggaranStudent('11', '11. Laporan 5', '95007');
    laporanPelanggaranRecord($studentTen, 'Tingkat Sepuluh', 'light', 10, '2026-03-06');
    laporanPelanggaranRecord($studentEleven, 'Tingkat Sebelas', 'light', 10, '2026-03-06');

    $this->actingAs($kesiswaan)->get(route('kesiswaan.laporan.index', ['tingkat' => '10']))
        ->assertSuccessful()
        ->assertSee('Tingkat Sepuluh')
        ->assertDontSee('Tingkat Sebelas');
});

// TS.LAP.005 / TC.LAP.005.001 — filter berdasarkan kategori (positive)
test('violation report filters by kategori', function () {
    $kesiswaan = laporanPelanggaranActor();
    $student = laporanPelanggaranStudent('10', '10. Laporan 6', '95008');
    laporanPelanggaranRecord($student, 'Kategori Ringan Laporan', 'light', 10, '2026-03-07');
    laporanPelanggaranRecord($student, 'Kategori Berat Laporan', 'heavy', 60, '2026-03-07');

    $this->actingAs($kesiswaan)->get(route('kesiswaan.laporan.index', ['kategori' => 'heavy']))
        ->assertSuccessful()
        ->assertSee('Kategori Berat Laporan')
        ->assertDontSee('Kategori Ringan Laporan');
});

// TS.LAP.006 / TC.LAP.006.001 — filter berdasarkan status (positive)
test('violation report filters by status', function () {
    $kesiswaan = laporanPelanggaranActor();
    $student = laporanPelanggaranStudent('10', '10. Laporan 7', '95009');
    $type = JenisPelanggaran::factory()->create(['nama' => 'Status Approved Laporan', 'kategori' => 'light', 'pengurangan_poin' => 10]);
    PelanggaranSiswa::factory()->create([
        'profil_siswa_id' => $student->nisn,
        'jenis_pelanggaran_id' => $type->id,
        'nama_pelanggaran' => 'Status Approved Laporan',
        'status' => 'approved',
        'tanggal_pelanggaran' => '2026-03-08',
    ]);
    $typeRejected = JenisPelanggaran::factory()->create(['nama' => 'Status Rejected Laporan', 'kategori' => 'light', 'pengurangan_poin' => 10]);
    PelanggaranSiswa::factory()->create([
        'profil_siswa_id' => $student->nisn,
        'jenis_pelanggaran_id' => $typeRejected->id,
        'nama_pelanggaran' => 'Status Rejected Laporan',
        'status' => 'rejected',
        'tanggal_pelanggaran' => '2026-03-08',
    ]);

    $this->actingAs($kesiswaan)->get(route('kesiswaan.laporan.index', ['status' => 'approved']))
        ->assertSuccessful()
        ->assertSee('Status Approved Laporan')
        ->assertDontSee('Status Rejected Laporan');
});

// TS.LAP.007 / TC.LAP.007.001 — kelas_id yang tidak ada ditolak (negative)
test('violation report rejects a non-existent kelas_id', function () {
    $kesiswaan = laporanPelanggaranActor();

    $this->actingAs($kesiswaan)->get(route('kesiswaan.laporan.index', ['kelas_id' => 999999]))
        ->assertSessionHasErrors('kelas_id');
});

// TS.LAP.008 / TC.LAP.008.001 — tingkat tidak valid ditolak (negative)
test('violation report rejects an invalid tingkat', function () {
    $kesiswaan = laporanPelanggaranActor();

    $this->actingAs($kesiswaan)->get(route('kesiswaan.laporan.index', ['tingkat' => '13']))
        ->assertSessionHasErrors('tingkat');
});

// TS.LAP.009 / TC.LAP.009.001 — selesai sebelum mulai ditolak (negative)
test('violation report rejects selesai before mulai', function () {
    $kesiswaan = laporanPelanggaranActor();

    $this->actingAs($kesiswaan)->get(route('kesiswaan.laporan.index', [
        'mulai' => '2026-03-10',
        'selesai' => '2026-03-05',
    ]))->assertSessionHasErrors('selesai');
});

// TS.LAP.010 / TC.LAP.010.001 — ekspor excel berhasil (positive)
test('violation report excel export succeeds', function () {
    $kesiswaan = laporanPelanggaranActor();
    $student = laporanPelanggaranStudent('10', '10. Laporan 8', '95010');
    laporanPelanggaranRecord($student, 'Ekspor Excel Laporan', 'light', 10, '2026-03-09');

    $this->actingAs($kesiswaan)->get(route('kesiswaan.laporan.ekspor-excel'))
        ->assertDownload();
});

// TS.LAP.011 / TC.LAP.011.001 — ekspor pdf berhasil (positive)
test('violation report pdf export succeeds', function () {
    $kesiswaan = laporanPelanggaranActor();
    $student = laporanPelanggaranStudent('10', '10. Laporan 9', '95011');
    laporanPelanggaranRecord($student, 'Ekspor PDF Laporan', 'light', 10, '2026-03-10');

    $this->actingAs($kesiswaan)->get(route('kesiswaan.laporan.ekspor-pdf'))
        ->assertDownload();
});
