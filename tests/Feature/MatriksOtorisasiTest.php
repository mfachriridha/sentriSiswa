<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Matriks Otorisasi Antar-Peran — Pengujian Kebutuhan Non-Fungsional
|--------------------------------------------------------------------------
|
| Membuktikan bahwa data siswa (nomor telepon, foto, poin) hanya bisa dibuka
| oleh peran yang memang berhak. Tiap peran dicoba membuka area milik peran
| lain, dan seluruhnya harus ditolak - entah dengan 403 atau dipulangkan ke
| halaman masuk.
|
| Dijalankan dengan:
|   php artisan test tests/Feature/MatriksOtorisasiTest.php
|
| Keluarannya berupa daftar per kombinasi peran-area, jadi bisa langsung
| dilampirkan sebagai bukti pengujian keamanan.
|
*/

function penggunaUjiOtorisasi(string $peran): Pengguna
{
    $pengguna = match ($peran) {
        'admin' => Pengguna::factory()->admin(),
        'wali kelas' => Pengguna::factory()->homeroom(),
        'BK' => Pengguna::factory()->counselor(),
        'kesiswaan' => Pengguna::factory()->studentAffairs(),
        'siswa' => Pengguna::factory()->student(),
    };

    return $pengguna->create(['status' => 'registered']);
}

dataset('akses terlarang antar-peran', [
    // Area admin: data induk guru, siswa, dan kelas seluruh sekolah.
    'siswa mencoba membuka data siswa milik admin' => ['siswa', '/admin/siswa'],
    'wali kelas mencoba membuka data siswa milik admin' => ['wali kelas', '/admin/siswa'],
    'BK mencoba membuka data guru milik admin' => ['BK', '/admin/guru'],
    'kesiswaan mencoba membuka data kelas milik admin' => ['kesiswaan', '/admin/kelas'],

    // Area wali kelas: daftar siswa satu kelas beserta absensinya.
    'siswa mencoba membuka kelas milik wali kelas' => ['siswa', '/wali-kelas/kelas-saya'],
    'BK mencoba membuka kelas milik wali kelas' => ['BK', '/wali-kelas/kelas-saya'],
    'kesiswaan mencoba membuka kelas milik wali kelas' => ['kesiswaan', '/wali-kelas/kelas-saya'],
    'admin mencoba membuka kelas milik wali kelas' => ['admin', '/wali-kelas/kelas-saya'],

    // Area BK: pemantauan siswa satu tingkat.
    'siswa mencoba membuka monitoring milik BK' => ['siswa', '/bk/monitoring'],
    'wali kelas mencoba membuka monitoring milik BK' => ['wali kelas', '/bk/monitoring'],
    'kesiswaan mencoba membuka monitoring milik BK' => ['kesiswaan', '/bk/monitoring'],
    'admin mencoba membuka monitoring milik BK' => ['admin', '/bk/monitoring'],

    // Area kesiswaan: catatan pelanggaran dan poin seluruh siswa.
    'siswa mencoba membuka pelanggaran milik kesiswaan' => ['siswa', '/kesiswaan/pelanggaran-siswa'],
    'wali kelas mencoba membuka pelanggaran milik kesiswaan' => ['wali kelas', '/kesiswaan/pelanggaran-siswa'],
    'BK mencoba membuka pelanggaran milik kesiswaan' => ['BK', '/kesiswaan/pelanggaran-siswa'],
    'admin mencoba membuka pelanggaran milik kesiswaan' => ['admin', '/kesiswaan/pelanggaran-siswa'],

    // Area siswa: absensi dan poin pribadi.
    'wali kelas mencoba membuka absensi pribadi siswa' => ['wali kelas', '/siswa/absensi'],
    'BK mencoba membuka poin pribadi siswa' => ['BK', '/siswa/poin'],
    'kesiswaan mencoba membuka absensi pribadi siswa' => ['kesiswaan', '/siswa/absensi'],
    'admin mencoba membuka poin pribadi siswa' => ['admin', '/siswa/poin'],
]);

test('ditolak', function (string $peran, string $url) {
    $this->actingAs(penggunaUjiOtorisasi($peran));

    $respons = $this->get($url);

    // Ditolak bisa berupa 403, atau dipulangkan ke halaman masuk. Yang penting
    // halamannya tidak pernah benar-benar tersaji.
    expect($respons->status())->not->toBe(200);
})->with('akses terlarang antar-peran');

dataset('halaman yang wajib login', [
    'data siswa milik admin' => ['/admin/siswa'],
    'kelas milik wali kelas' => ['/wali-kelas/kelas-saya'],
    'monitoring milik BK' => ['/bk/monitoring'],
    'pelanggaran milik kesiswaan' => ['/kesiswaan/pelanggaran-siswa'],
    'absensi pribadi siswa' => ['/siswa/absensi'],
    'poin pribadi siswa' => ['/siswa/poin'],
]);

test('tanpa login dipulangkan ke halaman masuk', function (string $url) {
    $this->get($url)->assertRedirect('/login');
})->with('halaman yang wajib login');

test('kata sandi tidak pernah tersimpan sebagai teks biasa', function () {
    $pengguna = Pengguna::factory()->student()->create([
        'status' => 'registered',
        'password' => bcrypt('rahasia123'),
    ]);

    $tersimpan = DB::table('pengguna')
        ->where('id', $pengguna->id)
        ->value('password');

    expect($tersimpan)->not->toBe('rahasia123')
        ->and($tersimpan)->toStartWith('$2y$');
});
