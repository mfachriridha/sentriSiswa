<?php

use App\Models\Kelas;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Hapus Sebagian Data Kelas (Admin) — State Transition Testing
|--------------------------------------------------------------------------
|
| Pengujian black box: admin masuk lewat halaman masuk, lalu menghapus kelas
| berdasarkan kriteria yang dicentang (tingkat, ada/tidaknya wali kelas) -
| bukan menghapus literal semua kelas sekaligus.
|
*/

test('admin menghapus kelas berdasarkan tingkat, tingkat lain tetap ada', function () {
    adminDataKelas();
    Kelas::create(['nama' => '10 IPA 1', 'tingkat' => '10']);
    Kelas::create(['nama' => '11 IPA 1', 'tingkat' => '11']);

    $this->followingRedirects()
        ->delete('/admin/kelas/hapus-semua', ['tingkat' => ['10']])
        ->assertSee('1 kelas berhasil dihapus.')
        ->assertSee('11 IPA 1')
        ->assertDontSee('10 IPA 1');
});

test('admin menghapus kelas yang belum ada wali kelasnya saja', function () {
    adminDataKelas();
    $wali = Pengguna::factory()->homeroom()->create(['status' => 'registered']);
    Kelas::create(['nama' => '10 IPA 1', 'tingkat' => '10', 'wali_kelas_id' => $wali->id]);
    Kelas::create(['nama' => '10 IPA 2', 'tingkat' => '10']);

    $this->followingRedirects()
        ->delete('/admin/kelas/hapus-semua', ['wali' => ['tidak']])
        ->assertSee('1 kelas berhasil dihapus.')
        ->assertSee('10 IPA 1')
        ->assertDontSee('10 IPA 2');
});

test('admin gagal menghapus kelas kalau tidak ada kriteria yang dicentang', function () {
    adminDataKelas();
    Kelas::create(['nama' => '10 IPA 1', 'tingkat' => '10']);

    $this->from('/admin/kelas')
        ->followingRedirects()
        ->delete('/admin/kelas/hapus-semua', [])
        ->assertSee('Pilih minimal satu kriteria yang mau dihapus.');

    expect(Kelas::where('nama', '10 IPA 1')->exists())->toBeTrue();
});
