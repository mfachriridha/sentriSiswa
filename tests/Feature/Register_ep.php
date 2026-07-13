<?php

use App\Models\Pengguna;
use App\Models\ProfilGuru;
use App\Models\ProfilSiswa;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur Daftar Akun — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Pendaftaran berjalan dua tahap:
|   1. Verifikasi identitas — pilih peran, isi nomor identitas.
|   2. Lengkapi pendaftaran — isi email, kata sandi, dan nomor HP untuk guru.
|
| Sekolah lebih dulu memasukkan data siswa dan guru. Pendaftaran hanya
| mengaktifkan akun yang datanya memang sudah ada, jadi setiap pengujian
| menyiapkan kondisi awal itu, lalu menempuh kedua tahap seperti pengguna.
| Hasilnya diperiksa dari apa yang muncul di layar, bukan dari basis data.
|
| Catatan mengenai kolom yang dikosongkan dan format email yang salah:
| Semua kolom pada formulir ditandai wajib, dan kolom email bertipe email.
| Peramban menahan pengiriman formulir lebih dulu untuk kasus itu, sehingga
| pengguna tidak pernah sampai melihat pesan dari sistem. Karena tidak pernah
| dialami pengguna, kasus tersebut tidak didokumentasikan.
|
*/

/** Siswa yang datanya sudah ada di sekolah tetapi akunnya belum diaktifkan. */
function siswaBelumPunyaAkun(string $nisn, string $nis): void
{
    $pengguna = Pengguna::factory()->student()->create([
        'email' => null,
        'status' => 'unregistered',
    ]);

    ProfilSiswa::factory()->create([
        'pengguna_id' => $pengguna->id,
        'nisn' => $nisn,
        'nis' => $nis,
    ]);
}

/** Guru yang datanya sudah ada di sekolah tetapi akunnya belum diaktifkan. */
function guruBelumPunyaAkun(string $nip): void
{
    $pengguna = Pengguna::factory()->homeroom()->create([
        'email' => null,
        'status' => 'unregistered',
    ]);

    ProfilGuru::factory()->create([
        'pengguna_id' => $pengguna->id,
        'nip' => $nip,
    ]);
}

// TS.REG.001 / TC.REG.001.001 — Positive
test('siswa berhasil mendaftar menggunakan nisn', function () {
    siswaBelumPunyaAkun(nisn: '1234567890', nis: '10001');

    $this->get('/daftar')->assertSee('Nomor Identitas');

    $this->followingRedirects()->post('/daftar/verifikasi', [
        'peran' => 'student',
        'identity' => '1234567890',
    ])->assertSee('Lengkapi Profil');

    $this->followingRedirects()
        ->post('/daftar/lengkapi', [
            'email' => 'siswa.baru@sentrisiswa.test',
            'password' => 'Rahasia123',
            'password_confirmation' => 'Rahasia123',
        ])
        ->assertSee('Pendaftaran berhasil. Silakan masuk dengan akun Anda.');
});

// TS.REG.002 / TC.REG.002.001 — Positive
test('siswa berhasil mendaftar menggunakan nis', function () {
    siswaBelumPunyaAkun(nisn: '1234567891', nis: '10002');

    $this->followingRedirects()->post('/daftar/verifikasi', [
        'peran' => 'student',
        'identity' => '10002',
    ])->assertSee('Lengkapi Profil');

    $this->followingRedirects()
        ->post('/daftar/lengkapi', [
            'email' => 'siswa.nis@sentrisiswa.test',
            'password' => 'Rahasia123',
            'password_confirmation' => 'Rahasia123',
        ])
        ->assertSee('Pendaftaran berhasil. Silakan masuk dengan akun Anda.');
});

// TS.REG.003 / TC.REG.003.001 — Positive
test('guru berhasil mendaftar dan diminta mengisi nomor hp', function () {
    guruBelumPunyaAkun(nip: '198501012020121001');

    $this->followingRedirects()->post('/daftar/verifikasi', [
        'peran' => 'teacher',
        'identity' => '198501012020121001',
    ])->assertSee('Nomor HP');

    $this->followingRedirects()
        ->post('/daftar/lengkapi', [
            'email' => 'guru.baru@sentrisiswa.test',
            'password' => 'Rahasia123',
            'password_confirmation' => 'Rahasia123',
            'telepon' => '081234567890',
        ])
        ->assertSee('Pendaftaran berhasil. Silakan masuk dengan akun Anda.');
});

// TS.REG.004 / TC.REG.004.001 — Positive
test('siswa yang baru mendaftar bisa langsung masuk memakai akunnya', function () {
    siswaBelumPunyaAkun(nisn: '1234567892', nis: '10003');

    $this->followingRedirects()->post('/daftar/verifikasi', [
        'peran' => 'student',
        'identity' => '1234567892',
    ])->assertSee('Lengkapi Profil');

    $this->followingRedirects()
        ->post('/daftar/lengkapi', [
            'email' => 'siswa.langsung@sentrisiswa.test',
            'password' => 'Rahasia123',
            'password_confirmation' => 'Rahasia123',
        ])
        ->assertSee('Pendaftaran berhasil. Silakan masuk dengan akun Anda.');

    $this->followingRedirects()
        ->post('/login', [
            'email' => 'siswa.langsung@sentrisiswa.test',
            'password' => 'Rahasia123',
        ])
        ->assertSee('Dashboard Siswa');
});

// TS.REG.005 / TC.REG.005.001 — Negative
test('pendaftaran ditolak karena nomor identitas siswa tidak ditemukan', function () {
    $this->from('/daftar')
        ->followingRedirects()
        ->post('/daftar/verifikasi', [
            'peran' => 'student',
            'identity' => '9999999999',
        ])
        ->assertSee('NISN/NIS tidak ditemukan.')
        ->assertDontSee('Lengkapi Profil');
});

// TS.REG.006 / TC.REG.006.001 — Negative
test('pendaftaran ditolak karena nomor identitas guru tidak ditemukan', function () {
    $this->from('/daftar')
        ->followingRedirects()
        ->post('/daftar/verifikasi', [
            'peran' => 'teacher',
            'identity' => '199999999999999999',
        ])
        ->assertSee('NIP tidak ditemukan.')
        ->assertDontSee('Lengkapi Profil');
});

// TS.REG.007 / TC.REG.007.001 — Negative
test('pendaftaran ditolak karena identitas siswa sudah pernah didaftarkan', function () {
    $pengguna = Pengguna::factory()->student()->create([
        'email' => 'sudah.punya.akun@sentrisiswa.test',
        'status' => 'registered',
    ]);

    ProfilSiswa::factory()->create([
        'pengguna_id' => $pengguna->id,
        'nisn' => '1234567893',
        'nis' => '10004',
    ]);

    $this->from('/daftar')
        ->followingRedirects()
        ->post('/daftar/verifikasi', [
            'peran' => 'student',
            'identity' => '1234567893',
        ])
        ->assertSee('NISN/NIS sudah terdaftar. Silakan masuk.');
});

// TS.REG.008 / TC.REG.008.001 — Negative
test('pendaftaran ditolak karena identitas guru sudah pernah didaftarkan', function () {
    $pengguna = Pengguna::factory()->homeroom()->create([
        'email' => 'guru.sudah.punya@sentrisiswa.test',
        'status' => 'registered',
    ]);

    ProfilGuru::factory()->create([
        'pengguna_id' => $pengguna->id,
        'nip' => '198501012020121002',
    ]);

    $this->from('/daftar')
        ->followingRedirects()
        ->post('/daftar/verifikasi', [
            'peran' => 'teacher',
            'identity' => '198501012020121002',
        ])
        ->assertSee('NIP sudah terdaftar. Silakan masuk.');
});

// TS.REG.009 / TC.REG.009.001 — Negative
test('pendaftaran ditolak karena nomor identitas berisi huruf', function () {
    $this->from('/daftar')
        ->followingRedirects()
        ->post('/daftar/verifikasi', [
            'peran' => 'student',
            'identity' => 'ABC123',
        ])
        ->assertSee('NIP, NISN, atau NIS hanya boleh berisi angka.');
});

// TS.REG.010 / TC.REG.010.001 — Negative
test('pendaftaran ditolak karena email sudah dipakai akun lain', function () {
    siswaBelumPunyaAkun(nisn: '1234567894', nis: '10005');

    Pengguna::factory()->student()->create([
        'email' => 'sudah.dipakai@sentrisiswa.test',
        'status' => 'registered',
    ]);

    $this->followingRedirects()->post('/daftar/verifikasi', [
        'peran' => 'student',
        'identity' => '1234567894',
    ])->assertSee('Lengkapi Profil');

    $this->followingRedirects()
        ->post('/daftar/lengkapi', [
            'email' => 'sudah.dipakai@sentrisiswa.test',
            'password' => 'Rahasia123',
            'password_confirmation' => 'Rahasia123',
        ])
        ->assertSee('Email sudah terdaftar.')
        // Pendaftarnya tetap di formulir langkah 2, bukan dilempar balik ke
        // langkah 1 dengan formulir kosong.
        ->assertSee('Lengkapi Profil')
        ->assertDontSee('Pendaftaran berhasil. Silakan masuk dengan akun Anda.');
});

// TS.REG.011 / TC.REG.011.001 — Negative
test('pendaftaran ditolak karena kata sandi tidak mengandung angka', function () {
    siswaBelumPunyaAkun(nisn: '1234567895', nis: '10006');

    $this->followingRedirects()->post('/daftar/verifikasi', [
        'peran' => 'student',
        'identity' => '1234567895',
    ])->assertSee('Lengkapi Profil');

    $this->followingRedirects()
        ->post('/daftar/lengkapi', [
            'email' => 'tanpa.angka@sentrisiswa.test',
            'password' => 'RahasiaSaja',
            'password_confirmation' => 'RahasiaSaja',
        ])
        ->assertSee('Kata sandi harus mengandung huruf dan angka.')
        ->assertSee('Lengkapi Profil');
});

// TS.REG.012 / TC.REG.012.001 — Negative
test('pendaftaran ditolak karena ulangi kata sandi tidak sama', function () {
    siswaBelumPunyaAkun(nisn: '1234567896', nis: '10007');

    $this->followingRedirects()->post('/daftar/verifikasi', [
        'peran' => 'student',
        'identity' => '1234567896',
    ])->assertSee('Lengkapi Profil');

    $this->followingRedirects()
        ->post('/daftar/lengkapi', [
            'email' => 'beda.sandi@sentrisiswa.test',
            'password' => 'Rahasia123',
            'password_confirmation' => 'Rahasia456',
        ])
        ->assertSee('Konfirmasi kata sandi tidak cocok.')
        ->assertSee('Lengkapi Profil');
});

// TS.REG.016 / TC.REG.016.001 — Negative
test('pendaftar yang salah mengisi kata sandi tetap di formulirnya, tidak dilempar balik', function () {
    siswaBelumPunyaAkun(nisn: '1234567897', nis: '10008');

    $this->followingRedirects()->post('/daftar/verifikasi', [
        'peran' => 'student',
        'identity' => '1234567897',
    ])->assertSee('Lengkapi Profil');

    $this->followingRedirects()
        ->post('/daftar/lengkapi', [
            'email' => 'sandi.salah@sentrisiswa.test',
            'password' => 'rahasiasaja',
            'password_confirmation' => 'rahasiasaja',
        ])
        // Alasan penolakannya terbaca, dan identitas yang tadi diverifikasi masih
        // tertera. Bukan dilempar balik ke formulir nomor identitas yang kosong.
        ->assertSee('Kata sandi harus mengandung huruf dan angka.')
        ->assertSee('Lengkapi Profil')
        ->assertSee('1234567897')
        ->assertDontSee('Verifikasi Identitas');
});

// TS.REG.017 / TC.REG.017.001 — Negative
test('guru yang salah mengetik nip dilempar balik dengan pilihan Guru yang tetap terpilih', function () {
    $this->from('/daftar')
        ->followingRedirects()
        ->post('/daftar/verifikasi', [
            'peran' => 'teacher',
            'identity' => '199999999999999999',
        ])
        ->assertSee('NIP tidak ditemukan.')
        // Pilihan perannya tidak boleh berubah sendiri jadi Siswa: kalau berubah,
        // guru yang mengetik ulang NIP-nya justru dicari di data siswa, lalu
        // diberi tahu "NISN/NIS tidak ditemukan" padahal NIP-nya benar.
        ->assertSee('<input type="radio" name="peran" value="teacher" checked', escape: false);
});
