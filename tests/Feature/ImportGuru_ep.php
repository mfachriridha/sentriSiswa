<?php

use App\Models\Kelas;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\TestResponse;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Impor Guru (Admin) — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Pengujian black box: admin masuk lewat halaman masuk, lalu mengimpor data
| guru seperti pengguna biasa. Hasilnya diperiksa dari apa yang muncul di
| layar, bukan dari basis data.
|
| Impor berjalan tiga tahap: admin mengunggah berkas, meninjau isinya lebih
| dulu, lalu menyetujui untuk disimpan. Baris yang datanya bermasalah dilewati,
| dan alasannya ditampilkan setelah impor selesai.
| Yang diuji di berkas ini adalah isi berkasnya: jenis berkas, dan tiap baris di dalamnya
| (nama, NIP, peran).
|
*/

function adminImporGuru(): Pengguna
{
    $admin = Pengguna::factory()->admin()->create([
        'email' => 'admin.impor.guru@sentrisiswa.test',
        'status' => 'registered',
    ]);

    masukSebagai($admin);

    return $admin;
}

/**
 * Berkas impor guru, sebagaimana yang disusun admin dari templat yang diunduh.
 *
 * @param  list<array{nama: string, nip: string, tipe?: string, kelas?: string}>  $barisGuru
 */
function berkasImporGuru(array $barisGuru): UploadedFile
{
    $baris = [['nama', 'nip', 'tipe', 'kelas']];

    foreach ($barisGuru as $guru) {
        $baris[] = [
            $guru['nama'],
            $guru['nip'],
            $guru['tipe'] ?? 'wali_kelas',
            $guru['kelas'] ?? '',
        ];
    }

    return berkasExcel('impor-guru.xlsx', $baris);
}

/** Menempuh tahap unggah dan tinjau, lalu menyetujui impor. */
function jalankanImporGuru(UploadedFile $berkas): TestResponse
{
    test()->post('/admin/guru/impor/unggah', ['file' => $berkas]);

    $tinjauan = test()->get('/admin/guru/impor/pratinjau');
    $jalurBerkas = $tinjauan->viewData('filePath');

    return test()->followingRedirects()
        ->post('/admin/guru/impor', ['file_path' => $jalurBerkas]);
}

// TS.IMG.001 / TC.IMG.001.001 — Positive
test('admin berhasil mengimpor guru dari berkas yang benar', function () {
    adminImporGuru();

    $this->get('/admin/guru/impor')->assertSee('Impor');

    $this->post('/admin/guru/impor/unggah', [
        'file' => berkasImporGuru([
            ['nama' => 'Raka Pradipta', 'nip' => '198501012020121001'],
            ['nama' => 'Dimas Mahendra', 'nip' => '198501012020121002'],
        ]),
    ])->assertRedirect('/admin/guru/impor/pratinjau');

    $this->get('/admin/guru/impor/pratinjau')
        ->assertSee('Raka Pradipta')
        ->assertSee('Dimas Mahendra');

    jalankanImporGuru(berkasImporGuru([
        ['nama' => 'Raka Pradipta', 'nip' => '198501012020121001'],
        ['nama' => 'Dimas Mahendra', 'nip' => '198501012020121002'],
    ]))
        ->assertSee('Impor berhasil')
        ->assertSee('2 guru baru dibuat')
        ->assertSee('Raka Pradipta');
});

// TS.IMG.002 / TC.IMG.002.001 — Positive
test('guru yang diimpor sebagai wali kelas langsung dipasang ke kelasnya', function () {
    adminImporGuru();
    Kelas::create(['nama' => '10 IPA 1', 'tingkat' => '10']);

    jalankanImporGuru(berkasImporGuru([
        ['nama' => 'Raka Pradipta', 'nip' => '198501012020121003', 'tipe' => 'wali_kelas', 'kelas' => '10 IPA 1'],
    ]))->assertSee('Impor berhasil');

    $this->get('/admin/kelas')
        ->assertSee('10 IPA 1')
        ->assertSee('Raka Pradipta');
});

// TS.IMG.003 / TC.IMG.003.001 — Negative
test('admin gagal mengunggah berkas impor guru yang bukan berkas excel', function () {
    adminImporGuru();

    $this->from('/admin/guru/impor')
        ->followingRedirects()
        ->post('/admin/guru/impor/unggah', [
            'file' => UploadedFile::fake()->create('catatan.txt', 10, 'text/plain'),
        ])
        ->assertSee('File harus bertipe: xlsx, xls.');
});

// TS.IMG.004 / TC.IMG.004.001 — Positive
test('baris guru tanpa nama dilewati dan alasannya ditampilkan', function () {
    adminImporGuru();

    jalankanImporGuru(berkasImporGuru([
        ['nama' => '', 'nip' => '198501012020121004'],
        ['nama' => 'Dimas Mahendra', 'nip' => '198501012020121005'],
    ]))
        ->assertSee('1 guru baru dibuat')
        ->assertSee('Nama kosong');
});

// TS.IMG.005 / TC.IMG.005.001 — Positive
test('baris guru tanpa nip dilewati dan alasannya ditampilkan', function () {
    adminImporGuru();

    jalankanImporGuru(berkasImporGuru([
        ['nama' => 'Raka Pradipta', 'nip' => ''],
    ]))
        ->assertSee('NIP kosong, guru harus didaftarkan manual oleh admin');
});

// TS.IMG.008 / TC.IMG.008.001 — Positive
test('nama guru dari berkas dirapikan tanpa merusak penulisan gelarnya', function () {
    adminImporGuru();

    // Berkas sekolah menulis nama dengan HURUF BESAR SEMUA, tetapi gelarnya
    // sudah ditulis benar. Gelar punya kapitalisasi yang tidak mengikuti pola
    // kata biasa, jadi tidak boleh ikut diseragamkan.
    jalankanImporGuru(berkasImporGuru([
        ['nama' => 'SRI RAHAYU, S.Pd', 'nip' => '198501012020121006'],
        ['nama' => 'FARHAH SYARIFAH, S.PdI', 'nip' => '198501012020121007'],
        ['nama' => 'NURWANTI PUJI L , ST., S.Pd', 'nip' => '198501012020121008'],
    ]))->assertSee('3 guru baru dibuat');

    $this->get('/admin/guru')
        ->assertSee('Sri Rahayu, S.Pd')
        ->assertSee('Farhah Syarifah, S.PdI')
        ->assertSee('Nurwanti Puji L, ST., S.Pd');
});
