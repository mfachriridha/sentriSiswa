<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Hapus Sebagian Data Guru (Admin) — State Transition Testing
|--------------------------------------------------------------------------
|
| Pengujian black box: admin masuk lewat halaman masuk, lalu menghapus guru
| berdasarkan kriteria yang dicentang (role, status pendaftaran, tingkat
| khusus BK) - bukan menghapus literal semua guru sekaligus.
|
*/

function guruUntukHapusSebagian(string $nama, string $peran, string $status, ?string $tingkat = null): Pengguna
{
    $guru = Pengguna::factory()->create([
        'nama' => $nama,
        'peran' => $peran,
        'status' => $status,
        'email' => $status === 'registered' ? strtolower(str_replace(' ', '.', $nama)).'@sentrisiswa.test' : null,
    ]);

    $guru->profilGuru()->create([
        'nip' => fake()->unique()->numerify('19##########'),
        'tipe_guru' => $peran,
        'tingkat' => $tingkat,
    ]);

    return $guru;
}

// TS.GUR.016 / TC.GUR.016.001 — Positive
test('admin menghapus guru berdasarkan role yang dicentang, role lain tetap ada', function () {
    adminDataGuru();
    guruUntukHapusSebagian('Wali Kelas Satu', 'wali_kelas', 'registered');
    guruUntukHapusSebagian('BK Satu', 'bk', 'registered', '10');

    $this->followingRedirects()
        ->delete('/admin/guru/hapus-semua', ['peran' => ['wali_kelas']])
        ->assertSee('1 guru berhasil dihapus.')
        ->assertSee('BK Satu')
        ->assertDontSee('Wali Kelas Satu');
});

// TS.GUR.017 / TC.GUR.017.001 — Positive
test('admin menghapus guru BK tingkat tertentu saja', function () {
    adminDataGuru();
    guruUntukHapusSebagian('BK Sepuluh', 'bk', 'registered', '10');
    guruUntukHapusSebagian('BK Sebelas', 'bk', 'registered', '11');

    $this->followingRedirects()
        ->delete('/admin/guru/hapus-semua', ['peran' => ['bk'], 'tingkat' => ['10']])
        ->assertSee('1 guru berhasil dihapus.')
        ->assertSee('BK Sebelas')
        ->assertDontSee('BK Sepuluh');
});

// TS.GUR.018 / TC.GUR.018.001 — Positive
test('admin menghapus guru berdasarkan status pendaftaran', function () {
    adminDataGuru();
    guruUntukHapusSebagian('Kesiswaan Belum Daftar', 'kesiswaan', 'unregistered');
    guruUntukHapusSebagian('Kesiswaan Sudah Daftar', 'kesiswaan', 'registered');

    $this->followingRedirects()
        ->delete('/admin/guru/hapus-semua', ['status' => ['unregistered']])
        ->assertSee('1 guru berhasil dihapus.')
        ->assertSee('Kesiswaan Sudah Daftar')
        ->assertDontSee('Kesiswaan Belum Daftar');
});

// TS.GUR.019 / TC.GUR.019.001 — Negative
test('admin gagal menghapus guru kalau tidak ada kriteria yang dicentang', function () {
    adminDataGuru();
    guruUntukHapusSebagian('Guru Aman', 'wali_kelas', 'registered');

    $this->from('/admin/guru')
        ->followingRedirects()
        ->delete('/admin/guru/hapus-semua', [])
        ->assertSee('Pilih minimal satu kriteria yang mau dihapus.');

    expect(Pengguna::where('nama', 'Guru Aman')->exists())->toBeTrue();
});
