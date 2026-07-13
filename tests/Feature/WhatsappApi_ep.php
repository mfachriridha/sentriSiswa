<?php

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
        'api.fonnte.com/*' => Http::response(['status' => true, 'id' => ['123']], 200),
    ]);
}

// TS.WAP.001 / TC.WAP.001.001 — Positive
test('admin berhasil menyimpan token layanan pengirim whatsapp', function () {
    adminWhatsapp();

    $this->get('/admin/pengaturan/whatsapp')->assertSee('WhatsApp');

    $this->followingRedirects()
        ->put('/admin/pengaturan/whatsapp', [
            'fonnte_token' => 'token-rahasia-sekolah',
        ])
        ->assertSee('Konfigurasi WhatsApp berhasil disimpan.');
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
