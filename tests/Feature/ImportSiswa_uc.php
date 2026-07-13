<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Impor Siswa (Admin) — Use Case Testing
|--------------------------------------------------------------------------
|
| Pengujian black box: admin masuk lewat halaman masuk, lalu mengimpor data
| siswa seperti pengguna biasa. Hasilnya diperiksa dari apa yang muncul di
| layar, bukan dari basis data.
|
| Impor berjalan tiga tahap: admin mengunggah berkas, meninjau isinya lebih
| dulu, lalu menyetujui untuk disimpan. Baris yang datanya bermasalah dilewati,
| dan alasannya ditampilkan setelah impor selesai.
| Alur pemakaian tanpa isian: admin mengunduh templat berkas impor sebelum mengisinya.
|
*/

// TS.IMS.008 / TC.IMS.008.001 — Positive
test('admin mengunduh templat berkas impor siswa', function () {
    Excel::fake();
    adminImporSiswa();

    $this->get('/admin/siswa/impor/template')->assertSuccessful();

    Excel::assertDownloaded('template-impor-siswa.xlsx');
});
