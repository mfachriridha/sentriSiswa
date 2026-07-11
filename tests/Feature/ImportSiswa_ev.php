<?php

use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Impor Siswa (Admin) — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Pengujian black box: admin masuk lewat halaman masuk, lalu mengimpor data
| siswa seperti pengguna biasa. Hasilnya diperiksa dari apa yang muncul di
| layar, bukan dari basis data.
|
| Impor berjalan tiga tahap: admin mengunggah berkas, meninjau isinya lebih
| dulu, lalu menyetujui untuk disimpan. Baris yang datanya bermasalah dilewati,
| dan alasannya ditampilkan setelah impor selesai.
|
*/

function adminImporSiswa(): Pengguna
{
    $admin = Pengguna::factory()->admin()->create([
        'email' => 'admin.impor.siswa@sentrisiswa.test',
        'status' => 'registered',
    ]);

    masukSebagai($admin);

    return $admin;
}

/**
 * Berkas impor siswa, sebagaimana yang disusun admin dari templat yang diunduh.
 *
 * @param  list<array{nama: string, nisn: string, nis: string, kelas?: string}>  $barisSiswa
 */
function berkasImporSiswa(array $barisSiswa): UploadedFile
{
    $baris = [['nama', 'nisn', 'nis', 'kelas']];

    foreach ($barisSiswa as $siswa) {
        $baris[] = [
            $siswa['nama'],
            $siswa['nisn'],
            $siswa['nis'],
            $siswa['kelas'] ?? '',
        ];
    }

    return berkasExcel('impor-siswa.xlsx', $baris);
}

/**
 * Menempuh tahap unggah dan tinjau, lalu menyetujui impor.
 */
function jalankanImporSiswa(UploadedFile $berkas): Illuminate\Testing\TestResponse
{
    test()->post('/admin/siswa/impor/unggah', ['file' => $berkas]);

    $tinjauan = test()->get('/admin/siswa/impor/pratinjau');
    $jalurBerkas = $tinjauan->viewData('filePath');

    return test()->followingRedirects()
        ->post('/admin/siswa/impor', ['file_path' => $jalurBerkas]);
}

// TS.IMS.001 / TC.IMS.001.001 — Positive
test('admin berhasil mengimpor siswa dari berkas yang benar', function () {
    adminImporSiswa();

    $berkas = berkasImporSiswa([
        ['nama' => 'Ahmad Fauzi', 'nisn' => '1234567890', 'nis' => '10001'],
        ['nama' => 'Siti Aminah', 'nisn' => '1234567891', 'nis' => '10002'],
    ]);

    $this->get('/admin/siswa/impor')->assertSee('Impor');

    $this->post('/admin/siswa/impor/unggah', ['file' => $berkas])
        ->assertRedirect('/admin/siswa/impor/pratinjau');

    $this->get('/admin/siswa/impor/pratinjau')
        ->assertSee('Ahmad Fauzi')
        ->assertSee('Siti Aminah');

    jalankanImporSiswa(berkasImporSiswa([
        ['nama' => 'Ahmad Fauzi', 'nisn' => '1234567890', 'nis' => '10001'],
        ['nama' => 'Siti Aminah', 'nisn' => '1234567891', 'nis' => '10002'],
    ]))
        ->assertSee('Impor berhasil')
        ->assertSee('2 siswa baru dibuat')
        ->assertSee('Ahmad Fauzi');
});

// TS.IMS.002 / TC.IMS.002.001 — Positive
test('admin berhasil mengimpor siswa sekaligus menempatkannya ke kelas', function () {
    adminImporSiswa();
    Kelas::create(['nama' => '10 IPA 1', 'tingkat' => '10']);

    jalankanImporSiswa(berkasImporSiswa([
        ['nama' => 'Ahmad Fauzi', 'nisn' => '1234567892', 'nis' => '10003', 'kelas' => '10 IPA 1'],
    ]))->assertSee('Impor berhasil');

    $this->get('/admin/siswa?tingkat=10')->assertSee('Ahmad Fauzi');
});

// TS.IMS.003 / TC.IMS.003.001 — Negative
test('admin gagal mengunggah berkas impor yang bukan berkas excel', function () {
    adminImporSiswa();

    $this->from('/admin/siswa/impor')
        ->followingRedirects()
        ->post('/admin/siswa/impor/unggah', [
            'file' => UploadedFile::fake()->create('catatan.txt', 10, 'text/plain'),
        ])
        ->assertSee('File harus bertipe: xlsx, xls.');
});

// TS.IMS.004 / TC.IMS.004.001 — Positive
test('baris tanpa nama dilewati dan alasannya ditampilkan', function () {
    adminImporSiswa();

    jalankanImporSiswa(berkasImporSiswa([
        ['nama' => '', 'nisn' => '1234567893', 'nis' => '10004'],
        ['nama' => 'Siti Aminah', 'nisn' => '1234567894', 'nis' => '10005'],
    ]))
        ->assertSee('1 siswa baru dibuat')
        ->assertSee('Detail baris yang dilewati')
        ->assertSee('Nama kosong');
});

// TS.IMS.005 / TC.IMS.005.001 — Positive
test('baris tanpa nisn dilewati dan alasannya ditampilkan', function () {
    adminImporSiswa();

    jalankanImporSiswa(berkasImporSiswa([
        ['nama' => 'Ahmad Fauzi', 'nisn' => '', 'nis' => '10006'],
    ]))
        ->assertSee('Detail baris yang dilewati')
        ->assertSee('NISN kosong');
});

// TS.IMS.006 / TC.IMS.006.001 — Positive
test('baris yang nisnya mengandung huruf dilewati dan alasannya ditampilkan', function () {
    adminImporSiswa();

    jalankanImporSiswa(berkasImporSiswa([
        ['nama' => 'Ahmad Fauzi', 'nisn' => '1234567895', 'nis' => 'ABC12'],
    ]))
        ->assertSee('Detail baris yang dilewati')
        ->assertSee('NIS harus berupa angka');
});

// TS.IMS.007 / TC.IMS.007.001 — Positive
test('siswa yang nisnya sudah ada tidak dibuat ulang', function () {
    adminImporSiswa();

    $pengguna = Pengguna::factory()->student()->create([
        'nama' => 'Siswa Lama',
        'status' => 'registered',
    ]);
    ProfilSiswa::factory()->create([
        'pengguna_id' => $pengguna->id,
        'nisn' => '1234567896',
        'nis' => '10007',
    ]);

    jalankanImporSiswa(berkasImporSiswa([
        ['nama' => 'Siswa Lama', 'nisn' => '1234567896', 'nis' => '10007'],
    ]))
        ->assertSee('Impor berhasil')
        ->assertSee('0 siswa baru dibuat');
});

// TS.IMS.008 / TC.IMS.008.001 — Positive
test('admin mengunduh templat berkas impor siswa', function () {
    Excel::fake();
    adminImporSiswa();

    $this->get('/admin/siswa/impor/template')->assertSuccessful();

    Excel::assertDownloaded('template-impor-siswa.xlsx');
});
