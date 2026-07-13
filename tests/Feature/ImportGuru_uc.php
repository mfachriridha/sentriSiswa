<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Maatwebsite\Excel\Facades\Excel;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Impor Guru (Admin) — Use Case Testing
|--------------------------------------------------------------------------
|
| Pengujian black box: admin masuk lewat halaman masuk, lalu mengimpor data
| guru seperti pengguna biasa. Hasilnya diperiksa dari apa yang muncul di
| layar, bukan dari basis data.
|
| Impor berjalan tiga tahap: admin mengunggah berkas, meninjau isinya lebih
| dulu, lalu menyetujui untuk disimpan. Baris yang datanya bermasalah dilewati,
| dan alasannya ditampilkan setelah impor selesai.
| Alur pemakaian tanpa isian: admin mengunduh templat berkas impor sebelum mengisinya.
|
*/

// TS.IMG.007 / TC.IMG.007.001 — Positive
test('admin mengunduh templat berkas impor guru', function () {
    Excel::fake();
    adminImporGuru();

    $this->get('/admin/guru/impor/template')->assertSuccessful();

    Excel::assertDownloaded('template-impor-guru.xlsx');
});
