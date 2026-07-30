<?php

use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Hapus Sebagian Data Siswa (Admin) — State Transition Testing
|--------------------------------------------------------------------------
|
| Pengujian black box: admin masuk lewat halaman masuk, lalu menghapus siswa
| berdasarkan kriteria yang dicentang (status pendaftaran, tingkat kelas) -
| bukan menghapus literal semua siswa sekaligus. Kriteria kosong di satu grup
| berarti grup itu tidak membatasi; kalau kedua grup kosong, penghapusan
| ditolak supaya admin tidak menghapus semua data tanpa sadar.
|
*/

function siswaUntukHapusSebagian(string $nama, string $nisn, string $status, ?Kelas $kelas = null): ProfilSiswa
{
    $pengguna = Pengguna::factory()->student()->create([
        'nama' => $nama,
        'status' => $status,
        'email' => $status === 'registered' ? "{$nisn}@sentrisiswa.test" : null,
    ]);

    return ProfilSiswa::factory()->create([
        'pengguna_id' => $pengguna->id,
        'nisn' => $nisn,
        'nis' => $nisn,
        'kelas_id' => $kelas?->id,
    ]);
}

// TS.SIS.022 / TC.SIS.022.001 — Positive
test('pratinjau menghitung siswa yang cocok tanpa menghapus apa pun', function () {
    adminDataSiswa();
    $kelas10 = Kelas::create(['nama' => '10 IPA 1', 'tingkat' => '10']);
    siswaUntukHapusSebagian('Belum Daftar Sepuluh', '3000000001', 'unregistered', $kelas10);
    siswaUntukHapusSebagian('Sudah Daftar Sepuluh', '3000000002', 'registered', $kelas10);

    $this->postJson('/admin/siswa/hapus-semua/pratinjau', ['status' => ['unregistered']])
        ->assertJson(['count' => 1]);

    expect(Pengguna::where('peran', 'siswa')->count())->toBe(2);
});

// TS.SIS.023 / TC.SIS.023.001 — Positive
test('admin menghapus siswa berdasarkan status yang dicentang, sisanya tetap ada', function () {
    adminDataSiswa();
    siswaUntukHapusSebagian('Belum Daftar', '3000000003', 'unregistered');
    siswaUntukHapusSebagian('Sudah Daftar', '3000000004', 'registered');

    $this->followingRedirects()
        ->delete('/admin/siswa/hapus-semua', ['status' => ['unregistered']])
        ->assertSee('1 siswa berhasil dihapus.')
        ->assertSee('Sudah Daftar')
        ->assertDontSee('Belum Daftar');
});

// TS.SIS.024 / TC.SIS.024.001 — Positive
test('admin menghapus siswa berdasarkan tingkat, kelas lain tidak ikut terhapus', function () {
    adminDataSiswa();
    $kelas10 = Kelas::create(['nama' => '10 IPA 1', 'tingkat' => '10']);
    $kelas11 = Kelas::create(['nama' => '11 IPA 1', 'tingkat' => '11']);
    siswaUntukHapusSebagian('Siswa Sepuluh', '3000000005', 'registered', $kelas10);
    siswaUntukHapusSebagian('Siswa Sebelas', '3000000006', 'registered', $kelas11);

    $this->followingRedirects()
        ->delete('/admin/siswa/hapus-semua', ['tingkat' => ['10']])
        ->assertSee('1 siswa berhasil dihapus.')
        ->assertSee('Siswa Sebelas')
        ->assertDontSee('Siswa Sepuluh');
});

// TS.SIS.025 / TC.SIS.025.001 — Positive
test('kombinasi status dan tingkat digabung dengan AND, bukan OR', function () {
    adminDataSiswa();
    $kelas10 = Kelas::create(['nama' => '10 IPA 1', 'tingkat' => '10']);
    // Cocok kedua kriteria - harus terhapus.
    siswaUntukHapusSebagian('Cocok Dua Kriteria', '3000000007', 'unregistered', $kelas10);
    // Cuma cocok status, tingkat beda - harus tetap ada.
    siswaUntukHapusSebagian('Cuma Cocok Status', '3000000008', 'unregistered', null);
    // Cuma cocok tingkat, status beda - harus tetap ada.
    siswaUntukHapusSebagian('Cuma Cocok Tingkat', '3000000009', 'registered', $kelas10);

    $this->followingRedirects()
        ->delete('/admin/siswa/hapus-semua', ['status' => ['unregistered'], 'tingkat' => ['10']])
        ->assertSee('1 siswa berhasil dihapus.')
        ->assertSee('Cuma Cocok Status')
        ->assertSee('Cuma Cocok Tingkat')
        ->assertDontSee('Cocok Dua Kriteria');
});

// TS.SIS.026 / TC.SIS.026.001 — Positive
test('siswa tanpa kelas ikut terhapus kalau kriteria tanpa_kelas dicentang', function () {
    adminDataSiswa();
    siswaUntukHapusSebagian('Tanpa Kelas', '3000000010', 'registered', null);
    siswaUntukHapusSebagian('Ada Kelas', '3000000011', 'registered', Kelas::create(['nama' => '10 IPA 1', 'tingkat' => '10']));

    $this->followingRedirects()
        ->delete('/admin/siswa/hapus-semua', ['tingkat' => ['tanpa_kelas']])
        ->assertSee('1 siswa berhasil dihapus.')
        ->assertSee('Ada Kelas')
        ->assertDontSee('Tanpa Kelas');
});

// TS.SIS.027 / TC.SIS.027.001 — Negative
test('admin gagal menghapus kalau tidak ada kriteria yang dicentang', function () {
    adminDataSiswa();
    siswaUntukHapusSebagian('Siswa Aman', '3000000012', 'registered');

    $this->from('/admin/siswa')
        ->followingRedirects()
        ->delete('/admin/siswa/hapus-semua', [])
        ->assertSee('Pilih minimal satu kriteria yang mau dihapus.');

    expect(Pengguna::where('nama', 'Siswa Aman')->exists())->toBeTrue();
});
