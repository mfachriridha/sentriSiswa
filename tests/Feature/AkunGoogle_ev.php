<?php

use App\Models\Pengguna;
use App\Models\ProfilGuru;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as PenggunaGoogle;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Akun Google — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Pengujian black box: pengguna memakai Google untuk mendaftar, masuk, atau
| menghubungkan akunnya. Hasilnya diperiksa dari apa yang muncul di layar.
|
| Google adalah layanan luar, jadi jawabannya dipalsukan — sama seperti WhatsApp
| dan surel OTP di pengujian lain. Sisanya tetap ditempuh lewat halaman biasa.
|
| Aturan yang dijaga:
|
| - Masuk lewat Google hanya bisa kalau akunnya memang sudah terhubung ke Google.
|   Tidak ada penghubungan otomatis, sekalipun email Google-nya kebetulan sama
|   persis dengan email akunnya. Menghubungkan akun harus keputusan sadar
|   pemiliknya, dari halaman profilnya sendiri.
| - Email Google boleh berbeda dari email akun. Satu akun Google hanya boleh
|   menempel ke satu akun.
| - Yang mendaftar lewat Google tidak punya kata sandi sama sekali, jadi akunnya
|   tidak bisa dimasuki dengan kata sandi bawaan dari data impor.
|
*/

/** Memalsukan jawaban Google, seolah pengguna baru saja menyetujui izinnya. */
function jawabanGoogle(string $id, string $email, string $nama = 'Ahmad Fauzi'): void
{
    Socialite::fake('google', (new PenggunaGoogle)->map([
        'id' => $id,
        'name' => $nama,
        'email' => $email,
    ]));
}

/** Siswa yang datanya sudah didaftarkan admin, tetapi belum punya akun. */
function siswaBelumPunyaAkunGoogle(string $nisn = '1234567890'): ProfilSiswa
{
    $pengguna = Pengguna::factory()->student()->create([
        'nama' => 'Ahmad Fauzi',
        'email' => null,
        'status' => 'unregistered',
    ]);

    return ProfilSiswa::factory()->create([
        'pengguna_id' => $pengguna->id,
        'nisn' => $nisn,
        'nis' => '10001',
    ]);
}

/** Menempuh tahap pertama pendaftaran: pilih peran, isi nomor identitas. */
function lanjutkanPendaftaranSiswaGoogle(string $identitas = '1234567890'): void
{
    test()->post('/daftar/verifikasi', [
        'peran' => 'student',
        'identity' => $identitas,
    ]);
}

// TS.AGO.001 / TC.AGO.001.001 — Positive
test('siswa mendaftar lewat google lalu langsung masuk', function () {
    siswaBelumPunyaAkunGoogle();
    lanjutkanPendaftaranSiswaGoogle();
    jawabanGoogle('google-123', 'ahmad@gmail.com');

    $this->post('/google/daftar');

    $this->followingRedirects()
        ->get('/google/callback')
        ->assertSee('Dashboard')
        ->assertSee('Ahmad Fauzi');
});

// TS.AGO.002 / TC.AGO.002.001 — Negative
test('akun yang mendaftar lewat google tidak bisa dimasuki dengan kata sandi bawaan', function () {
    siswaBelumPunyaAkunGoogle();
    lanjutkanPendaftaranSiswaGoogle();
    jawabanGoogle('google-123', 'ahmad@gmail.com');

    $this->post('/google/daftar');
    $this->get('/google/callback');
    $this->post('/logout');

    // Akun siswa dibuat admin dengan kata sandi bawaan "password". Pendaftaran
    // lewat Google harus menghapusnya, bukan membiarkannya jadi pintu belakang.
    $this->from('/login')
        ->followingRedirects()
        ->post('/login', [
            'email' => 'ahmad@gmail.com',
            'password' => 'password',
        ])
        ->assertSee('Email atau kata sandi tidak sesuai.');
});

// TS.AGO.003 / TC.AGO.003.001 — Negative
test('guru gagal mendaftar lewat google tanpa mengisi nomor hp', function () {
    $pengguna = Pengguna::factory()->homeroom()->create([
        'nama' => 'Raka Pradipta',
        'email' => null,
        'status' => 'unregistered',
    ]);
    ProfilGuru::factory()->homeroom()->create([
        'pengguna_id' => $pengguna->id,
        'nip' => '198701012010011001',
    ]);

    $this->post('/daftar/verifikasi', [
        'peran' => 'teacher',
        'identity' => '198701012010011001',
    ]);

    jawabanGoogle('google-456', 'raka@gmail.com');

    $this->from('/daftar/lengkapi')
        ->followingRedirects()
        ->post('/google/daftar', ['telepon' => ''])
        ->assertSee('Nomor HP wajib diisi.');
});

// TS.AGO.004 / TC.AGO.004.001 — Positive
test('pengguna yang akunnya sudah terhubung bisa masuk lewat google', function () {
    Pengguna::factory()->student()->create([
        'email' => 'ahmad@sentrisiswa.test',
        'status' => 'registered',
        'id_google' => 'google-123',
    ]);

    jawabanGoogle('google-123', 'ahmad@gmail.com');

    $this->get('/google/masuk');

    $this->followingRedirects()
        ->get('/google/callback')
        ->assertSee('Dashboard');
});

// TS.AGO.005 / TC.AGO.005.001 — Negative
test('akun google yang belum dikenal diarahkan mendaftar', function () {
    jawabanGoogle('google-999', 'orang.asing@gmail.com');

    $this->get('/google/masuk');

    $this->followingRedirects()
        ->get('/google/callback')
        ->assertSee('Akun Google ini belum terdaftar di Sentri Siswa. Silakan daftar terlebih dahulu.');
});

// TS.AGO.006 / TC.AGO.006.001 — Negative
test('akun yang belum terhubung tidak bisa masuk lewat google walau emailnya sama', function () {
    Pengguna::factory()->student()->create([
        'email' => 'ahmad@gmail.com',
        'status' => 'registered',
    ]);

    // Email Google-nya sama persis, tetapi akunnya belum pernah dihubungkan.
    // Menghubungkan akun harus keputusan sadar pemiliknya, dari halaman profilnya.
    jawabanGoogle('google-123', 'ahmad@gmail.com');

    $this->get('/google/masuk');

    $this->followingRedirects()
        ->get('/google/callback')
        ->assertSee('Akun Anda belum terhubung ke Google. Masuk seperti biasa, lalu hubungkan dari halaman Profil.');
});

// TS.AGO.007 / TC.AGO.007.001 — Positive
test('pengguna menghubungkan akunnya ke google yang emailnya berbeda', function () {
    [, , $siswa] = kelasBerisiSiswa();
    $siswa->pengguna->update(['email' => 'ahmad@sentrisiswa.test']);

    masukSebagai($siswa->pengguna);

    // Email sekolahnya dan Gmail pribadinya memang berbeda; itu diperbolehkan.
    jawabanGoogle('google-123', 'ahmad.pribadi@gmail.com');

    $this->get('/google/hubungkan');

    $this->followingRedirects()
        ->get('/google/callback')
        ->assertSee('Akun berhasil dihubungkan ke Google.')
        ->assertSee('Terhubung');
});

// TS.AGO.008 / TC.AGO.008.001 — Positive
test('akun yang sudah dihubungkan bisa dipakai masuk lewat google', function () {
    [, , $siswa] = kelasBerisiSiswa();
    masukSebagai($siswa->pengguna);

    jawabanGoogle('google-123', 'ahmad.pribadi@gmail.com');

    $this->get('/google/hubungkan');
    $this->get('/google/callback');
    $this->post('/logout');

    $this->get('/google/masuk');

    $this->followingRedirects()
        ->get('/google/callback')
        ->assertSee('Dashboard');
});

// TS.AGO.009 / TC.AGO.009.001 — Negative
test('akun google yang sudah menempel di akun lain tidak bisa dihubungkan lagi', function () {
    Pengguna::factory()->student()->create([
        'email' => 'orang.lain@sentrisiswa.test',
        'status' => 'registered',
        'id_google' => 'google-123',
    ]);

    [, , $siswa] = kelasBerisiSiswa();
    masukSebagai($siswa->pengguna);

    jawabanGoogle('google-123', 'ahmad@gmail.com');

    $this->get('/google/hubungkan');

    $this->followingRedirects()
        ->get('/google/callback')
        ->assertSee('Akun Google ini sudah terhubung ke akun lain.');
});

// TS.AGO.010 / TC.AGO.010.001 — Positive
test('pengguna yang punya kata sandi bisa memutuskan akun googlenya', function () {
    [, , $siswa] = kelasBerisiSiswa();
    $siswa->pengguna->update(['id_google' => 'google-123']);

    masukSebagai($siswa->pengguna);

    $this->followingRedirects()
        ->delete('/google/putuskan')
        ->assertSee('Akun Google berhasil diputuskan.')
        ->assertSee('Belum terhubung');
});

// TS.AGO.011 / TC.AGO.011.001 — Negative
test('pengguna yang belum punya kata sandi tidak bisa memutuskan akun googlenya', function () {
    siswaBelumPunyaAkunGoogle();
    lanjutkanPendaftaranSiswaGoogle();
    jawabanGoogle('google-123', 'ahmad@gmail.com');

    $this->post('/google/daftar');
    $this->get('/google/callback');

    // Memutus Google sekarang sama saja mengunci pemiliknya di luar akunnya sendiri.
    $this->followingRedirects()
        ->delete('/google/putuskan')
        ->assertSee('Buat kata sandi dulu lewat Ganti Kata Sandi, baru akun Google bisa diputuskan.');
});
