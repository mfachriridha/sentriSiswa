<?php

use App\Models\Pengguna;
use App\Models\ProfilGuru;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Impor Guru (Admin) — State Transition Testing
|--------------------------------------------------------------------------
|
| Pengujian black box: admin masuk lewat halaman masuk, lalu mengimpor data
| guru seperti pengguna biasa. Hasilnya diperiksa dari apa yang muncul di
| layar, bukan dari basis data.
|
| Impor berjalan tiga tahap: admin mengunggah berkas, meninjau isinya lebih
| dulu, lalu menyetujui untuk disimpan. Baris yang datanya bermasalah dilewati,
| dan alasannya ditampilkan setelah impor selesai.
| Keadaan yang menentukan: guru yang NIP-nya sudah ada tidak dibuat ulang saat berkas
| yang sama diimpor lagi.
|
*/

// TS.IMG.006 / TC.IMG.006.001 — Positive
test('guru yang nipnya sudah ada tidak dibuat ulang', function () {
    adminImporGuru();

    $pengguna = Pengguna::factory()->homeroom()->create([
        'nama' => 'Guru Lama',
        'status' => 'registered',
    ]);
    ProfilGuru::factory()->create([
        'pengguna_id' => $pengguna->id,
        'nip' => '198501012020121006',
    ]);

    jalankanImporGuru(berkasImporGuru([
        ['nama' => 'Guru Lama', 'nip' => '198501012020121006'],
    ]))
        ->assertSee('Impor berhasil')
        ->assertSee('0 guru baru dibuat')
        ->assertSee('1 guru sudah ada');
});
