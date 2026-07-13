<?php

use App\Models\Kelas;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Data Guru (Admin) — State Transition Testing
|--------------------------------------------------------------------------
|
| Pengujian black box: admin masuk lewat halaman masuk, lalu mengelola data
| guru seperti pengguna biasa. Hasilnya diperiksa dari apa yang muncul di
| layar, bukan dari basis data.
|
| Guru punya tiga peran: wali kelas, BK, dan kesiswaan. Guru BK wajib memilih
| tingkat yang dipegangnya; wali kelas boleh langsung dipilihkan kelasnya.
| Keadaan yang menentukan: kelas yang sudah punya wali kelas tidak bisa dipasangi wali
| kedua. Yang menolak bukan nilai yang diketik, melainkan keadaan kelas itu sendiri.
|
*/

// TS.GUR.008 / TC.GUR.008.001 — Negative
test('admin gagal memilihkan kelas yang sudah punya wali kelas', function () {
    adminDataGuru();

    $waliLama = guruTercatat('Wali Kelas Lama', '198501012020121008');
    $kelas = Kelas::create([
        'nama' => '10 IPA 1',
        'tingkat' => '10',
        'wali_kelas_id' => $waliLama->pengguna_id,
    ]);

    $this->from('/admin/guru/create')
        ->followingRedirects()
        ->post('/admin/guru', [
            'nama' => 'Wali Kelas Baru',
            'nip' => '198501012020121009',
            'peran' => 'wali_kelas',
            'kelas_id' => (string) $kelas->id,
        ])
        ->assertSee('Kelas yang dipilih tidak valid.')
        ->assertDontSee('Guru berhasil ditambahkan.');
});
