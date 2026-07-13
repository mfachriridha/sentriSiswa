<?php

use App\Models\Kelas;
use App\Models\Pengguna;
use App\Models\ProfilGuru;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Data Guru (Admin) — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Pengujian black box: admin masuk lewat halaman masuk, lalu mengelola data
| guru seperti pengguna biasa. Hasilnya diperiksa dari apa yang muncul di
| layar, bukan dari basis data.
|
| Guru punya tiga peran: wali kelas, BK, dan kesiswaan. Guru BK wajib memilih
| tingkat yang dipegangnya; wali kelas boleh langsung dipilihkan kelasnya.
|
*/

function adminDataGuru(): Pengguna
{
    $admin = Pengguna::factory()->admin()->create([
        'email' => 'admin.guru@sentrisiswa.test',
        'status' => 'registered',
    ]);

    masukSebagai($admin);

    return $admin;
}

function guruTercatat(string $nama, string $nip, string $peran = 'wali_kelas', ?string $tingkat = null): ProfilGuru
{
    $pengguna = Pengguna::factory()->create([
        'nama' => $nama,
        'peran' => $peran,
        'status' => 'unregistered',
        'email' => null,
    ]);

    return ProfilGuru::factory()->create([
        'pengguna_id' => $pengguna->id,
        'nip' => $nip,
        'tipe_guru' => $peran,
        'tingkat' => $tingkat,
    ]);
}

// TS.GUR.001 / TC.GUR.001.001 — Positive
test('admin berhasil menambah guru wali kelas dengan data yang benar', function () {
    adminDataGuru();

    $this->get('/admin/guru/create')->assertSee('Tambah Guru');

    $this->followingRedirects()
        ->post('/admin/guru', [
            'nama' => 'Raka Pradipta',
            'nip' => '198501012020121001',
            'peran' => 'wali_kelas',
        ])
        ->assertSee('Guru berhasil ditambahkan.')
        ->assertSee('Raka Pradipta');
});

// TS.GUR.002 / TC.GUR.002.001 — Positive
test('admin berhasil menambah guru bk beserta tingkat yang dipegang', function () {
    adminDataGuru();

    $this->followingRedirects()
        ->post('/admin/guru', [
            'nama' => 'Arif Nugroho',
            'nip' => '198501012020121002',
            'peran' => 'bk',
            'tingkat' => '10',
        ])
        ->assertSee('Guru berhasil ditambahkan.')
        ->assertSee('Arif Nugroho');
});

// TS.GUR.003 / TC.GUR.003.001 — Positive
test('admin berhasil menambah guru kesiswaan', function () {
    adminDataGuru();

    $this->followingRedirects()
        ->post('/admin/guru', [
            'nama' => 'Hendra Saputra',
            'nip' => '198501012020121003',
            'peran' => 'kesiswaan',
        ])
        ->assertSee('Guru berhasil ditambahkan.')
        ->assertSee('Hendra Saputra');
});

// TS.GUR.004 / TC.GUR.004.001 — Positive
test('admin berhasil menambah wali kelas sekaligus memilihkan kelasnya', function () {
    adminDataGuru();
    $kelas = Kelas::create(['nama' => '10 IPA 1', 'tingkat' => '10']);

    $this->followingRedirects()
        ->post('/admin/guru', [
            'nama' => 'Dimas Mahendra',
            'nip' => '198501012020121004',
            'peran' => 'wali_kelas',
            'kelas_id' => (string) $kelas->id,
        ])
        ->assertSee('Guru berhasil ditambahkan.');

    $this->get('/admin/kelas')
        ->assertSee('10 IPA 1')
        ->assertSee('Dimas Mahendra');
});

// TS.GUR.005 / TC.GUR.005.001 — Negative
test('admin gagal menambah guru karena guru bk tidak memilih tingkat', function () {
    adminDataGuru();

    $this->from('/admin/guru/create')
        ->followingRedirects()
        ->post('/admin/guru', [
            'nama' => 'Ratna Wulandari',
            'nip' => '198501012020121005',
            'peran' => 'bk',
        ])
        ->assertSee('Tingkat wajib diisi')
        ->assertDontSee('Guru berhasil ditambahkan.');
});

// TS.GUR.006 / TC.GUR.006.001 — Negative
test('admin gagal menambah guru karena nip sudah dipakai guru lain', function () {
    adminDataGuru();
    guruTercatat('Guru Lama', '198501012020121006');

    $this->from('/admin/guru/create')
        ->followingRedirects()
        ->post('/admin/guru', [
            'nama' => 'Guru Baru',
            'nip' => '198501012020121006',
            'peran' => 'wali_kelas',
        ])
        ->assertSee('NIP sudah digunakan.');
});

// TS.GUR.007 / TC.GUR.007.001 — Negative
test('admin gagal menambah guru karena nama mengandung angka', function () {
    adminDataGuru();

    $this->from('/admin/guru/create')
        ->followingRedirects()
        ->post('/admin/guru', [
            'nama' => 'Guru 123',
            'nip' => '198501012020121007',
            'peran' => 'wali_kelas',
        ])
        ->assertSee('Format Nama tidak valid.');
});

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

// TS.GUR.009 / TC.GUR.009.001 — Positive
test('admin berhasil mengubah data guru yang sudah ada', function () {
    adminDataGuru();
    $guru = guruTercatat('Nama Lama', '198501012020121010');

    $this->get("/admin/guru/{$guru->pengguna_id}/edit")->assertSee('Nama Lama');

    $this->followingRedirects()
        ->put("/admin/guru/{$guru->pengguna_id}", [
            'nama' => 'Nama Baru',
            'nip' => '198501012020121010',
            'peran' => 'wali_kelas',
        ])
        ->assertSee('Guru berhasil diperbarui.')
        ->assertSee('Nama Baru')
        ->assertDontSee('Nama Lama');
});

// TS.GUR.010 / TC.GUR.010.001 — Positive
test('admin berhasil menghapus data guru', function () {
    adminDataGuru();
    $guru = guruTercatat('Guru Dihapus', '198501012020121011');

    $this->followingRedirects()
        ->delete("/admin/guru/{$guru->pengguna_id}")
        ->assertSee('Guru berhasil dihapus.')
        ->assertDontSee('Guru Dihapus');
});

// TS.GUR.011 / TC.GUR.011.001 — Positive
test('admin mencari guru berdasarkan namanya', function () {
    adminDataGuru();
    guruTercatat('Ratna Wulandari', '198501012020121012');
    guruTercatat('Yusuf Firmansyah', '198501012020121013');

    $this->get('/admin/guru?search=Ratna')
        ->assertSee('Ratna Wulandari')
        ->assertDontSee('Yusuf Firmansyah');
});

// TS.GUR.012 / TC.GUR.012.001 — Positive
test('admin menyaring daftar guru berdasarkan perannya', function () {
    adminDataGuru();
    guruTercatat('Guru Wali Kelas', '198501012020121014', 'wali_kelas');
    guruTercatat('Guru Bimbingan', '198501012020121015', 'bk', '10');

    $this->get('/admin/guru?peran=bk')
        ->assertSee('Guru Bimbingan')
        ->assertDontSee('Guru Wali Kelas');
});
