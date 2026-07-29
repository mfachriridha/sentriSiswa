<?php

use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Profil Admin — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Pengujian black box: admin masuk lewat halaman masuk, lalu mengelola
| profilnya sendiri. Hasilnya diperiksa dari apa yang muncul di layar, bukan
| dari basis data.
|
| Admin boleh mengganti email dan nomor WhatsApp secara langsung. Penggantian
| kata sandi punya alurnya sendiri: sistem mengirim kode OTP ke email admin
| lebih dulu.
| Yang diuji di berkas ini adalah isiannya: nama, email, nomor WhatsApp bantuan, dan
| jenis berkas foto profil.
|
*/

function adminProfil(string $email = 'admin.profil@sentrisiswa.test'): Pengguna
{
    $admin = Pengguna::factory()->admin()->create([
        'nama' => 'Admin Sekolah',
        'email' => $email,
        'status' => 'registered',
    ]);

    masukSebagai($admin);

    return $admin;
}

// TS.PAD.002 / TC.PAD.002.001 — Positive
test('admin berhasil mengganti namanya sendiri', function () {
    adminProfil();

    $this->get('/admin/profil/edit')->assertSee('Admin Sekolah');

    $this->followingRedirects()
        ->put('/admin/profil', [
            'nama' => 'Admin Baru',
            'email' => 'admin.profil@sentrisiswa.test',
        ])
        ->assertSee('Profil admin berhasil diperbarui.')
        ->assertSee('Admin Baru')
        ->assertDontSee('Admin Sekolah');
});

// TS.PAD.003 / TC.PAD.003.001 — Positive
test('admin berhasil mengganti emailnya', function () {
    adminProfil();

    $this->followingRedirects()
        ->put('/admin/profil', [
            'nama' => 'Admin Sekolah',
            'email' => 'admin.baru@sentrisiswa.test',
        ])
        ->assertSee('Profil admin berhasil diperbarui.')
        ->assertSee('admin.baru@sentrisiswa.test');
});

// TS.PAD.004 / TC.PAD.004.001 — Positive
test('admin berhasil menyimpan nomor whatsapp', function () {
    adminProfil();

    $this->followingRedirects()
        ->put('/admin/profil', [
            'nama' => 'Admin Sekolah',
            'email' => 'admin.profil@sentrisiswa.test',
            'whatsapp_number' => '081234567890',
        ])
        ->assertSee('Profil admin berhasil diperbarui.')
        ->assertSee('081234567890');
});

// TS.PAD.005 / TC.PAD.005.001 — Negative
test('admin gagal mengganti email karena sudah dipakai akun lain', function () {
    adminProfil();

    Pengguna::factory()->student()->create([
        'email' => 'sudah.dipakai@sentrisiswa.test',
        'status' => 'registered',
    ]);

    $this->from('/admin/profil/edit')
        ->followingRedirects()
        ->put('/admin/profil', [
            'nama' => 'Admin Sekolah',
            'email' => 'sudah.dipakai@sentrisiswa.test',
        ])
        ->assertSee('Email sudah digunakan.')
        ->assertDontSee('Profil admin berhasil diperbarui.');
});

// TS.PAD.006 / TC.PAD.006.001 — Negative
test('admin gagal menyimpan nomor whatsapp yang mengandung huruf', function () {
    adminProfil();

    $this->from('/admin/profil/edit')
        ->followingRedirects()
        ->put('/admin/profil', [
            'nama' => 'Admin Sekolah',
            'email' => 'admin.profil@sentrisiswa.test',
            'whatsapp_number' => 'nomor-saya',
        ])
        ->assertSee('Format nomor WhatsApp tidak valid.');
});

// TS.PAD.007 / TC.PAD.007.001 — Positive
test('admin berhasil mengunggah foto profil bertipe yang diizinkan', function () {
    Storage::fake('public');
    adminProfil();

    $this->followingRedirects()
        ->put('/admin/profil', [
            'nama' => 'Admin Sekolah',
            'email' => 'admin.profil@sentrisiswa.test',
            'photo' => UploadedFile::fake()->image('foto.jpg'),
        ])
        ->assertSee('Profil admin berhasil diperbarui.');
});

// TS.PAD.008 / TC.PAD.008.001 — Negative
test('admin gagal mengunggah foto profil bertipe yang tidak diizinkan', function () {
    Storage::fake('public');
    adminProfil();

    $this->from('/admin/profil/edit')
        ->followingRedirects()
        ->put('/admin/profil', [
            'nama' => 'Admin Sekolah',
            'email' => 'admin.profil@sentrisiswa.test',
            'photo' => UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf'),
        ])
        ->assertSee('Foto harus berupa gambar.');
});

// TS.PAD.009 / TC.PAD.009.001 — Positive
test('admin menghapus foto profil lewat form edit, satu form sekaligus dengan data lain', function () {
    Storage::fake('public');
    adminProfil();

    $this->put('/admin/profil', [
        'nama' => 'Admin Sekolah',
        'email' => 'admin.profil@sentrisiswa.test',
        'photo' => UploadedFile::fake()->image('foto.jpg'),
    ]);

    $this->followingRedirects()
        ->put('/admin/profil', [
            'nama' => 'Admin Sekolah',
            'email' => 'admin.profil@sentrisiswa.test',
            'delete_photo' => '1',
        ])
        ->assertSee('Profil admin berhasil diperbarui.')
        ->assertDontSee('storage/photos/admins/');
});
