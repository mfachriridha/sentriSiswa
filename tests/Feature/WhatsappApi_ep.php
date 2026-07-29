<?php

use App\Models\Pengaturan;
use App\Models\Pengguna;
use App\Models\PesanWhatsapp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Fitur WhatsApp API (Admin) — Equivalence Partitioning
|--------------------------------------------------------------------------
|
| Pengujian black box: admin masuk lewat halaman masuk, lalu mengatur
| pengiriman WhatsApp seperti pengguna biasa. Hasilnya diperiksa dari apa yang
| muncul di layar, bukan dari basis data.
|
| Sekolah memakai layanan luar untuk mengirim WhatsApp. Admin menyimpan token
| layanan itu, lalu bisa mengirim pesan uji untuk memastikan pengiriman
| berjalan. Pada pengujian ini layanan luar tersebut ditiru, sehingga tidak ada
| pesan yang benar-benar terkirim ke nomor siapa pun.
| Yang diuji di berkas ini adalah isiannya: token layanan, dan penyaring status riwayat.
|
*/

function adminWhatsapp(): Pengguna
{
    $admin = Pengguna::factory()->admin()->create([
        'email' => 'admin.whatsapp@sentrisiswa.test',
        'status' => 'registered',
    ]);

    masukSebagai($admin);

    return $admin;
}

/** Meniru layanan pengirim WhatsApp yang berhasil mengirim. */
function layananWhatsappBerhasil(): void
{
    Http::fake([
        // Profil perangkat: token berlaku dan perangkatnya tersambung.
        'api.fonnte.com/device' => Http::response([
            'status' => true,
            'device' => '6281234567890',
            'device_status' => 'connect',
            'name' => 'Perangkat Sekolah',
            'package' => 'Reguler',
            'quota' => '100',
            'expired' => '18 November 2029',
        ], 200),
        'api.fonnte.com/*' => Http::response(['status' => true, 'id' => ['123']], 200),
    ]);
}

/** Meniru layanan pengirim WhatsApp yang menolak tokennya. */
function layananWhatsappMenolakToken(): void
{
    Http::fake([
        'api.fonnte.com/*' => Http::response(['status' => false, 'reason' => 'token invalid'], 200),
    ]);
}

// TS.WAP.001 / TC.WAP.001.001 — Positive
test('admin berhasil menyimpan token layanan pengirim whatsapp', function () {
    adminWhatsapp();
    layananWhatsappBerhasil();

    $this->get('/admin/pengaturan/whatsapp')->assertSee('WhatsApp');

    // Token diuji dulu ke layanannya sebelum disimpan, karena token tidak punya
    // pola yang bisa diperiksa sendiri.
    $this->followingRedirects()
        ->put('/admin/pengaturan/whatsapp', [
            'fonnte_token' => 'token-rahasia-sekolah',
        ])
        ->assertSee('Token Fonnte berhasil disimpan dan sudah diverifikasi.');
});

// TS.WAP.001 / TC.WAP.001.002 — Negative
test('token yang ditolak layanan pengirim tidak ikut tersimpan', function () {
    adminWhatsapp();
    Pengaturan::set('fonnte_token', 'token-lama-yang-masih-benar');
    layananWhatsappMenolakToken();

    $this->from('/admin/pengaturan/whatsapp')
        ->followingRedirects()
        ->put('/admin/pengaturan/whatsapp', [
            'fonnte_token' => 'token-ngawur',
        ])
        ->assertSee('Token ditolak Fonnte');

    // Token lama tidak ikut tergusur oleh token yang ternyata salah.
    expect(Pengaturan::get('fonnte_token'))->toBe('token-lama-yang-masih-benar');
});

// TS.WAP.008 / TC.WAP.008.001 — Positive
test('admin menyaring riwayat pesan berdasarkan status pengiriman', function () {
    adminWhatsapp();

    PesanWhatsapp::create([
        'telepon_penerima' => '081234567890',
        'nama_penerima' => 'Penerima Berhasil',
        'tipe_pesan' => 'attendance_report',
        'isi_pesan' => 'Laporan absensi harian.',
        'status' => 'sent',
        'dikirim_pada' => now(),
    ]);

    PesanWhatsapp::create([
        'telepon_penerima' => '081234567891',
        'nama_penerima' => 'Penerima Gagal',
        'tipe_pesan' => 'attendance_report',
        'isi_pesan' => 'Laporan absensi harian.',
        'status' => 'failed',
    ]);

    $this->get('/admin/pengaturan/whatsapp/riwayat?status=failed')
        ->assertSee('Penerima Gagal')
        ->assertDontSee('Penerima Berhasil');
});
