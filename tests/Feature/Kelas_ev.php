<?php

use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Data Kelas (Admin) — Equivalence Partitioning
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
|
*/

function adminDataKelas(): Pengguna
{
    $admin = Pengguna::factory()->admin()->create([
        'email' => 'admin.kelas@sentrisiswa.test',
        'status' => 'registered',
    ]);

    masukSebagai($admin);

    return $admin;
}

function siswaTanpaKelas(string $nama, string $nisn, string $nis): ProfilSiswa
{
    $pengguna = Pengguna::factory()->student()->create([
        'nama' => $nama,
        'status' => 'registered',
    ]);

    return ProfilSiswa::factory()->create([
        'pengguna_id' => $pengguna->id,
        'nisn' => $nisn,
        'nis' => $nis,
        'kelas_id' => null,
    ]);
}

// TS.KEL.001 / TC.KEL.001.001 — Positive
test('admin berhasil menambah kelas baru', function () {
    adminDataKelas();

    $this->get('/admin/kelas/create')->assertSee('Tambah Kelas');

    $this->followingRedirects()
        ->post('/admin/kelas', [
            'tingkat' => '10',
            'nama' => 'IPA 1',
        ])
        ->assertSee('Kelas berhasil ditambahkan.')
        ->assertSee('10 IPA 1');
});

// TS.KEL.002 / TC.KEL.002.001 — Positive
test('admin berhasil menambah kelas sekaligus memilihkan wali kelasnya', function () {
    adminDataKelas();

    $wali = Pengguna::factory()->homeroom()->create([
        'nama' => 'Raka Pradipta',
        'status' => 'registered',
    ]);

    $this->followingRedirects()
        ->post('/admin/kelas', [
            'tingkat' => '10',
            'nama' => 'IPA 2',
            'wali_kelas_id' => (string) $wali->id,
        ])
        ->assertSee('Kelas berhasil ditambahkan.')
        ->assertSee('10 IPA 2')
        ->assertSee('Raka Pradipta');
});

// TS.KEL.003 / TC.KEL.003.001 — Positive
test('admin berhasil menambah kelas sekaligus memasukkan beberapa siswa', function () {
    adminDataKelas();

    siswaTanpaKelas('Ahmad Fauzi', '1234567890', '10001');
    siswaTanpaKelas('Siti Aminah', '1234567891', '10002');

    $this->followingRedirects()
        ->post('/admin/kelas', [
            'tingkat' => '10',
            'nama' => 'IPA 3',
            'siswa_nisn' => ['1234567890', '1234567891'],
        ])
        ->assertSee('Kelas berhasil ditambahkan.');

    $this->get('/admin/siswa?tingkat=10')
        ->assertSee('Ahmad Fauzi')
        ->assertSee('Siti Aminah');
});

// TS.KEL.004 / TC.KEL.004.001 — Negative
test('admin gagal menambah kelas karena namanya sudah ada di tingkat yang sama', function () {
    adminDataKelas();
    Kelas::create(['nama' => '10 IPA 1', 'tingkat' => '10']);

    $this->from('/admin/kelas/create')
        ->followingRedirects()
        ->post('/admin/kelas', [
            'tingkat' => '10',
            'nama' => 'IPA 1',
        ])
        ->assertSee('Kelas dengan kombinasi ini sudah ada.')
        ->assertDontSee('Kelas berhasil ditambahkan.');
});

// TS.KEL.005 / TC.KEL.005.001 — Negative
test('admin gagal menambah kelas karena tingkat yang dipilih tidak tersedia', function () {
    adminDataKelas();

    $this->from('/admin/kelas/create')
        ->followingRedirects()
        ->post('/admin/kelas', [
            'tingkat' => '13',
            'nama' => 'IPA 1',
        ])
        ->assertSee('Tingkat yang dipilih tidak valid.');
});

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

// TS.KEL.007 / TC.KEL.007.001 — Positive
test('admin berhasil mengubah nama kelas yang sudah ada', function () {
    adminDataKelas();
    $kelas = Kelas::create(['nama' => '10 IPA 1', 'tingkat' => '10']);

    $this->get("/admin/kelas/{$kelas->id}/edit")->assertSee('IPA 1');

    $this->followingRedirects()
        ->put("/admin/kelas/{$kelas->id}", [
            'tingkat' => '10',
            'nama' => 'IPA 9',
        ])
        ->assertSee('Kelas berhasil diperbarui.')
        ->assertSee('10 IPA 9')
        ->assertDontSee('10 IPA 1');
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

// TS.KEL.009 / TC.KEL.009.001 — Positive
test('admin berhasil menghapus kelas', function () {
    adminDataKelas();
    $kelas = Kelas::create(['nama' => '10 IPA 8', 'tingkat' => '10']);

    $this->followingRedirects()
        ->delete("/admin/kelas/{$kelas->id}")
        ->assertSee('Kelas berhasil dihapus.')
        ->assertDontSee('10 IPA 8');
});

// TS.KEL.010 / TC.KEL.010.001 — Positive
test('admin mencari kelas berdasarkan namanya', function () {
    adminDataKelas();
    Kelas::create(['nama' => '10 IPA 1', 'tingkat' => '10']);
    Kelas::create(['nama' => '11 IPS 1', 'tingkat' => '11']);

    $this->get('/admin/kelas?search=IPA')
        ->assertSee('10 IPA 1')
        ->assertDontSee('11 IPS 1');
});

// TS.KEL.011 / TC.KEL.011.001 — Positive
test('admin menyaring daftar kelas berdasarkan tingkat', function () {
    adminDataKelas();
    Kelas::create(['nama' => '10 IPA 1', 'tingkat' => '10']);
    Kelas::create(['nama' => '11 IPS 1', 'tingkat' => '11']);

    $this->get('/admin/kelas?tingkat=11')
        ->assertSee('11 IPS 1')
        ->assertDontSee('10 IPA 1');
});
