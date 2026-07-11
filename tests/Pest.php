<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
 // ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Pembantu Pengujian Black Box
|--------------------------------------------------------------------------
|
| Pengujian di proyek ini bersifat black box: setiap pengujian menempuh alur
| yang sama seperti pengguna dan hanya memeriksa apa yang muncul di layar.
| Karena itu, masuk ke aplikasi pun harus lewat halaman masuk, bukan lewat
| jalan pintas dari kode.
|
*/

/**
 * Masuk ke aplikasi lewat halaman masuk, persis seperti pengguna biasa.
 *
 * Semua akun yang dibuat lewat pabrik data memakai kata sandi "password".
 */
function masukSebagai(App\Models\Pengguna $pengguna, string $kataSandi = 'password'): void
{
    test()->post('/login', [
        'email' => $pengguna->email,
        'password' => $kataSandi,
    ]);
}

/**
 * Membuat berkas Excel sungguhan untuk diunggah, seperti berkas yang disusun
 * admin dari templat yang diunduh.
 *
 * @param  list<list<string>>  $baris  Baris pertama adalah judul kolom.
 */
function berkasExcel(string $namaBerkas, array $baris): Illuminate\Http\UploadedFile
{
    $jalurSementara = tempnam(sys_get_temp_dir(), 'impor').'.xlsx';

    Maatwebsite\Excel\Facades\Excel::store(
        new App\Exports\ArrayExport(array_shift($baris), $baris),
        basename($jalurSementara),
        'local',
    );

    $jalurTersimpan = Illuminate\Support\Facades\Storage::disk('local')
        ->path(basename($jalurSementara));

    return new Illuminate\Http\UploadedFile(
        $jalurTersimpan,
        $namaBerkas,
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        null,
        true,
    );
}
