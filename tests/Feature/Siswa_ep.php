<?php

use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Data Siswa (Admin) — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Pengujian black box: admin masuk lewat halaman masuk, lalu mengelola data
| siswa seperti pengguna biasa. Hasilnya diperiksa dari apa yang muncul di
| layar, bukan dari basis data.
| Yang diuji di berkas ini adalah isiannya: nama, NISN, NIS, jenis kelamin, nomor HP,
| kelas, berikut pencarian dan penyaringnya.
|
*/

function adminDataSiswa(): Pengguna
{
    $admin = Pengguna::factory()->admin()->create([
        'email' => 'admin.siswa@sentrisiswa.test',
        'status' => 'registered',
    ]);

    masukSebagai($admin);

    return $admin;
}

function siswaTercatat(string $nama, string $nisn, string $nis, ?Kelas $kelas = null): ProfilSiswa
{
    $pengguna = Pengguna::factory()->student()->create([
        'nama' => $nama,
        'status' => 'unregistered',
        'email' => null,
    ]);

    return ProfilSiswa::factory()->create([
        'pengguna_id' => $pengguna->id,
        'nisn' => $nisn,
        'nis' => $nis,
        'kelas_id' => $kelas?->id,
    ]);
}

// TS.SIS.001 / TC.SIS.001.001 — Positive
test('admin berhasil menambah siswa dengan data yang lengkap dan benar', function () {
    adminDataSiswa();
    $kelas = Kelas::create(['nama' => '10 IPA 1', 'tingkat' => '10']);

    $this->get('/admin/siswa/create')->assertSee('Tambah Siswa');

    $this->followingRedirects()
        ->post('/admin/siswa', [
            'nama' => 'Ahmad Fauzi',
            'nisn' => '1234567890',
            'nis' => '10001',
            'jenis_kelamin' => 'L',
            'kelas_id' => (string) $kelas->id,
            'telepon' => '081234567890',
            'alamat' => 'Jalan Melati Nomor 10',
        ])
        ->assertSee('Siswa berhasil ditambahkan.')
        ->assertSee('Ahmad Fauzi');
});

// TS.SIS.002 / TC.SIS.002.001 — Positive
test('admin berhasil menambah siswa tanpa mengisi kelas', function () {
    adminDataSiswa();

    $this->followingRedirects()
        ->post('/admin/siswa', [
            'nama' => 'Siti Aminah',
            'nisn' => '1234567891',
            'nis' => '10002',
            'jenis_kelamin' => 'L',
        ])
        ->assertSee('Siswa berhasil ditambahkan.')
        ->assertSee('Siti Aminah');
});

// TS.SIS.003 / TC.SIS.003.001 — Negative
test('admin gagal menambah siswa karena nisn sudah dipakai siswa lain', function () {
    adminDataSiswa();
    siswaTercatat('Budi Santoso', '1234567892', '10003');

    $this->from('/admin/siswa/create')
        ->followingRedirects()
        ->post('/admin/siswa', [
            'nama' => 'Siswa Kembar',
            'nisn' => '1234567892',
            'nis' => '10004',
            'jenis_kelamin' => 'L',
        ])
        ->assertSee('NISN sudah digunakan.')
        ->assertDontSee('Siswa berhasil ditambahkan.');
});

// TS.SIS.004 / TC.SIS.004.001 — Negative
test('admin gagal menambah siswa karena nis sudah dipakai siswa lain', function () {
    adminDataSiswa();
    siswaTercatat('Budi Santoso', '1234567893', '10005');

    $this->from('/admin/siswa/create')
        ->followingRedirects()
        ->post('/admin/siswa', [
            'nama' => 'Siswa Kembar',
            'nisn' => '1234567894',
            'nis' => '10005',
            'jenis_kelamin' => 'L',
        ])
        ->assertSee('NIS sudah digunakan.');
});

// TS.SIS.005 / TC.SIS.005.001 — Negative
test('admin gagal menambah siswa karena nama mengandung angka', function () {
    adminDataSiswa();

    $this->from('/admin/siswa/create')
        ->followingRedirects()
        ->post('/admin/siswa', [
            'nama' => 'Siswa 123',
            'nisn' => '1234567895',
            'nis' => '10006',
            'jenis_kelamin' => 'L',
        ])
        ->assertSee('Format Nama tidak valid.');
});

// TS.SIS.006 / TC.SIS.006.001 — Negative
test('admin gagal menambah siswa karena nis mengandung huruf', function () {
    adminDataSiswa();

    $this->from('/admin/siswa/create')
        ->followingRedirects()
        ->post('/admin/siswa', [
            'nama' => 'Rina Marlina',
            'nisn' => '1234567896',
            'nis' => 'ABC12',
            'jenis_kelamin' => 'L',
        ])
        ->assertSee('Format NIS tidak valid.');
});

// TS.SIS.007 / TC.SIS.007.001 — Negative
test('admin gagal menambah siswa karena nomor hp mengandung huruf', function () {
    adminDataSiswa();

    $this->from('/admin/siswa/create')
        ->followingRedirects()
        ->post('/admin/siswa', [
            'nama' => 'Rina Marlina',
            'nisn' => '1234567897',
            'nis' => '10007',
            'jenis_kelamin' => 'L',
            'telepon' => 'nomor-saya',
        ])
        ->assertSee('Format Nomor HP tidak valid.');
});

// TS.SIS.008 / TC.SIS.008.001 — Positive
test('admin berhasil mengubah data siswa yang sudah ada', function () {
    adminDataSiswa();
    $siswa = siswaTercatat('Nama Lama', '1234567898', '10008');

    $this->get("/admin/siswa/{$siswa->pengguna_id}/edit")->assertSee('Nama Lama');

    $this->followingRedirects()
        ->put("/admin/siswa/{$siswa->pengguna_id}", [
            'nama' => 'Nama Baru',
            'nisn' => '1234567898',
            'nis' => '10008',
            'jenis_kelamin' => 'L',
        ])
        ->assertSee('Siswa berhasil diperbarui.')
        ->assertSee('Nama Baru')
        ->assertDontSee('Nama Lama');
});

// TS.SIS.010 / TC.SIS.010.001 — Positive
test('admin mencari siswa berdasarkan namanya', function () {
    adminDataSiswa();
    siswaTercatat('Dewi Lestari', '1234567800', '10010');
    siswaTercatat('Bagus Wibowo', '1234567801', '10011');

    $this->get('/admin/siswa?search=Dewi')
        ->assertSee('Dewi Lestari')
        ->assertDontSee('Bagus Wibowo');
});

// TS.SIS.011 / TC.SIS.011.001 — Positive
test('admin menyaring daftar siswa berdasarkan tingkat kelas', function () {
    adminDataSiswa();
    $kelasSepuluh = Kelas::create(['nama' => '10 IPA 1', 'tingkat' => '10']);
    $kelasSebelas = Kelas::create(['nama' => '11 IPS 1', 'tingkat' => '11']);

    siswaTercatat('Siswa Kelas Sepuluh', '1234567802', '10012', $kelasSepuluh);
    siswaTercatat('Siswa Kelas Sebelas', '1234567803', '10013', $kelasSebelas);

    $this->get('/admin/siswa?tingkat=10')
        ->assertSee('Siswa Kelas Sepuluh')
        ->assertDontSee('Siswa Kelas Sebelas');
});

// TS.SIS.012 / TC.SIS.012.001 — Positive
test('admin menyaring daftar siswa yang belum mendaftar akun', function () {
    adminDataSiswa();
    siswaTercatat('Siswa Belum Daftar', '1234567804', '10014');

    $sudahDaftar = Pengguna::factory()->student()->create([
        'nama' => 'Siswa Sudah Daftar',
        'status' => 'registered',
    ]);
    ProfilSiswa::factory()->create([
        'pengguna_id' => $sudahDaftar->id,
        'nisn' => '1234567805',
        'nis' => '10015',
        'jenis_kelamin' => 'L',
    ]);

    $this->get('/admin/siswa?status=unregistered')
        ->assertSee('Siswa Belum Daftar')
        ->assertDontSee('Siswa Sudah Daftar');
});

// TS.SIS.013 / TC.SIS.013.001 — Positive
test('jenis kelamin siswa tampil di halaman rinciannya', function () {
    adminDataSiswa();

    $this->followingRedirects()
        ->post('/admin/siswa', [
            'nama' => 'Siti Aminah',
            'nisn' => '1234567806',
            'nis' => '10016',
            'jenis_kelamin' => 'P',
        ])
        ->assertSee('Siswa berhasil ditambahkan.');

    $siswa = Pengguna::where('nama', 'Siti Aminah')->first();

    $this->get("/admin/siswa/{$siswa->id}")
        ->assertSee('Jenis Kelamin')
        ->assertSee('Perempuan');
});

// TS.SIS.014 / TC.SIS.014.001 — Negative
test('siswa ditolak ketika jenis kelaminnya belum dipilih', function () {
    adminDataSiswa();

    $this->from('/admin/siswa/create')
        ->followingRedirects()
        ->post('/admin/siswa', [
            'nama' => 'Ahmad Fauzi',
            'nisn' => '1234567807',
            'nis' => '10017',
            'jenis_kelamin' => '',
        ])
        ->assertSee('Jenis kelamin wajib dipilih.');
});
