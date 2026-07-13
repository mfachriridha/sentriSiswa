<?php

use App\Models\Kelas;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\TestResponse;
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
| Yang diuji di berkas ini adalah isi berkasnya: jenis berkas, dan tiap baris di dalamnya
| (nama, NISN, NIS, jenis kelamin) - baris yang tidak memenuhi syarat dilewati beserta
| alasannya.
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
 * @param  list<array{nama: string, nisn: string, nis: string, l_p?: string, kelas?: string}>  $barisSiswa
 */
function berkasImporSiswa(array $barisSiswa): UploadedFile
{
    $baris = [['nama', 'nisn', 'nis', 'l_p', 'kelas']];

    foreach ($barisSiswa as $siswa) {
        $baris[] = [
            $siswa['nama'],
            $siswa['nisn'],
            $siswa['nis'],
            $siswa['l_p'] ?? '',
            $siswa['kelas'] ?? '',
        ];
    }

    return berkasExcel('impor-siswa.xlsx', $baris);
}

/** Membuka halaman tinjauan impor setelah berkasnya diunggah. */
function tinjauImporSiswa(UploadedFile $berkas): TestResponse
{
    test()->post('/admin/siswa/impor/unggah', ['file' => $berkas]);

    return test()->get('/admin/siswa/impor/pratinjau');
}

/**
 * Menempuh tahap unggah dan tinjau, lalu menyetujui impor.
 */
function jalankanImporSiswa(UploadedFile $berkas): TestResponse
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

// TS.IMS.009 / TC.IMS.009.001 — Positive
test('jenis kelamin ikut terbaca dari kolom L per P', function () {
    adminImporSiswa();

    jalankanImporSiswa(berkasImporSiswa([
        ['nama' => 'Ahmad Fauzi', 'nisn' => '1234567890', 'nis' => '10001', 'l_p' => 'L'],
        ['nama' => 'Siti Aminah', 'nisn' => '1234567891', 'nis' => '10002', 'l_p' => 'P'],
    ]))->assertSee('Impor berhasil');

    $this->get('/admin/siswa?search=Ahmad')->assertSee('Ahmad Fauzi');

    $siswa = Pengguna::where('nama', 'Ahmad Fauzi')->first();
    $this->get("/admin/siswa/{$siswa->id}")
        ->assertSee('Jenis Kelamin')
        ->assertSee('Laki-laki');
});

// TS.IMS.010 / TC.IMS.010.001 — Positive
test('baris tanpa jenis kelamin tetap diimpor', function () {
    adminImporSiswa();

    // Berkas data dari sekolah kadang punya baris yang kolom L/P-nya belum terisi.
    jalankanImporSiswa(berkasImporSiswa([
        ['nama' => 'Ahmad Fauzi', 'nisn' => '1234567890', 'nis' => '10001', 'l_p' => ''],
    ]))
        ->assertSee('Impor berhasil')
        ->assertSee('1 siswa baru dibuat');
});

// TS.IMS.011 / TC.IMS.011.001 — Positive
test('NISN yang diawali tanda kutip dibersihkan dulu sebelum disimpan', function () {
    adminImporSiswa();

    // Sel Excel yang diformat sebagai teks sering menyimpan tanda kutip di depan angkanya.
    jalankanImporSiswa(berkasImporSiswa([
        ['nama' => 'Reyshandi', 'nisn' => "'0084814788", 'nis' => '232410220', 'l_p' => 'L'],
    ]))
        ->assertSee('Impor berhasil')
        ->assertSee('1 siswa baru dibuat');

    // NISN tersimpan utuh sepuluh angka, tanpa tanda kutip dan tanpa angka yang hilang.
    $this->get('/admin/siswa?search=0084814788')->assertSee('Reyshandi');
});

// TS.IMS.012 / TC.IMS.012.001 — Negative
test('baris yang NISN-nya mengandung huruf dilewati', function () {
    adminImporSiswa();

    jalankanImporSiswa(berkasImporSiswa([
        ['nama' => 'Ahmad Fauzi', 'nisn' => '12345ABCDE', 'nis' => '10001'],
    ]))
        ->assertSee('Impor berhasil')
        ->assertSee('0 siswa baru dibuat')
        ->assertSee('NISN harus berupa angka');
});

// TS.IMS.013 / TC.IMS.013.001 — Positive
test('tinjauan menandai baris bermasalah beserta alasannya sebelum impor dijalankan', function () {
    adminImporSiswa();

    $tinjauan = tinjauImporSiswa(berkasImporSiswa([
        ['nama' => 'Ahmad Fauzi', 'nisn' => '1234567890', 'nis' => '10001', 'l_p' => 'L', 'kelas' => '10 IPA 1'],
        ['nama' => 'Tanpa NISN', 'nisn' => '', 'nis' => '10002'],
        ['nama' => 'Tanpa NIS', 'nisn' => '1234567891', 'nis' => ''],
    ]));

    // Tinjauan harus memakai aturan yang sama dengan impor, supaya tidak ada baris
    // yang ditandai layak lalu diam-diam dilewati.
    $tinjauan
        ->assertSee('2 baris akan dilewati')
        ->assertSee('Dilewati: NISN kosong')
        ->assertSee('Dilewati: NIS kosong')
        ->assertSee('Valid');
});

// TS.IMS.014 / TC.IMS.014.001 — Negative
test('dua baris ber-NISN sama hanya menghasilkan satu siswa', function () {
    adminImporSiswa();

    // NISN adalah penanda siswa yang harus unik. Kalau berkasnya memuat dua siswa
    // berbeda dengan NISN sama, yang kedua tidak boleh diam-diam menimpa yang pertama.
    $berkas = [
        ['nama' => 'Ibrahim Candra', 'nisn' => '1234567890', 'nis' => '10001', 'l_p' => 'L'],
        ['nama' => 'Reihan Badillah', 'nisn' => '1234567890', 'nis' => '10002', 'l_p' => 'L'],
    ];

    tinjauImporSiswa(berkasImporSiswa($berkas))
        ->assertSee('1 baris akan dilewati')
        ->assertSee('Dilewati: NISN ganda di dalam berkas');

    jalankanImporSiswa(berkasImporSiswa($berkas))
        ->assertSee('1 siswa baru dibuat')
        ->assertSee('NISN ganda di dalam berkas');

    // Siswa pertama yang dipakai; siswa kedua dilewati, bukan menimpa.
    $this->get('/admin/siswa?search=Ibrahim')->assertSee('Ibrahim Candra');
    $this->get('/admin/siswa?search=Reihan')->assertDontSee('Reihan Badillah');
});
