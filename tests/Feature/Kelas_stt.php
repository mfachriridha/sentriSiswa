<?php

use App\Models\Kelas;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Data Kelas (Admin) — State Transition Testing
|--------------------------------------------------------------------------
|
| Pengujian black box: admin masuk lewat halaman masuk, lalu mengelola kelas
| seperti pengguna biasa. Hasilnya diperiksa dari apa yang muncul di layar,
| bukan dari basis data.
|
| Saat menambah kelas, admin mengisi tingkat dan nama kelas secara terpisah;
| keduanya digabung menjadi nama lengkap, misalnya tingkat 10 dan nama "IPA 1"
| menjadi "10 IPA 1". Admin juga bisa langsung memilihkan wali kelas dan
| memasukkan beberapa siswa sekaligus.
| Keadaan yang berpindah: seorang siswa hanya boleh berada di satu kelas. Siswa yang
| sudah punya kelas tidak bisa dimasukkan ke kelas lain, dan mengeluarkannya dari
| kelasnya mengembalikannya ke keadaan belum berkelas.
|
*/

// TS.KEL.006 / TC.KEL.006.001 — Negative
test('admin gagal memasukkan siswa yang sudah punya kelas lain', function () {
    adminDataKelas();

    $kelasLama = Kelas::create(['nama' => '11 IPS 1', 'tingkat' => '11']);
    $siswa = siswaTanpaKelas('Sudah Punya Kelas', '1234567892', '10003');
    $siswa->update(['kelas_id' => $kelasLama->id]);

    $this->from('/admin/kelas/create')
        ->followingRedirects()
        ->post('/admin/kelas', [
            'tingkat' => '10',
            'nama' => 'IPA 4',
            'siswa_nisn' => ['1234567892'],
        ])
        ->assertSee('Siswa yang dipilih tidak valid.')
        ->assertDontSee('Kelas berhasil ditambahkan.');
});

// TS.KEL.008 / TC.KEL.008.001 — Positive
test('admin berhasil mengeluarkan siswa dari kelas saat mengubah kelas', function () {
    adminDataKelas();

    $kelas = Kelas::create(['nama' => '10 IPA 1', 'tingkat' => '10']);
    $siswa = siswaTanpaKelas('Ahmad Fauzi', '1234567893', '10004');
    $siswa->update(['kelas_id' => $kelas->id]);

    // Menyimpan tanpa mencentang siswa manapun berarti mengeluarkan semuanya.
    $this->followingRedirects()
        ->put("/admin/kelas/{$kelas->id}", [
            'tingkat' => '10',
            'nama' => 'IPA 1',
        ])
        ->assertSee('Kelas berhasil diperbarui.');

    $this->get("/admin/kelas/{$kelas->id}")
        ->assertDontSee('Ahmad Fauzi');
});
