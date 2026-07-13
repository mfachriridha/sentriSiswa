<?php

use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Impor Siswa (Admin) — State Transition Testing
|--------------------------------------------------------------------------
|
| Pengujian black box: admin masuk lewat halaman masuk, lalu mengimpor data
| siswa seperti pengguna biasa. Hasilnya diperiksa dari apa yang muncul di
| layar, bukan dari basis data.
|
| Impor berjalan tiga tahap: admin mengunggah berkas, meninjau isinya lebih
| dulu, lalu menyetujui untuk disimpan. Baris yang datanya bermasalah dilewati,
| dan alasannya ditampilkan setelah impor selesai.
| Keadaan yang menentukan: siswa yang NISN-nya sudah ada di sekolah tidak dibuat ulang
| saat berkas yang sama diimpor lagi. Impor boleh diulang tanpa menggandakan siapa pun.
|
*/

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
