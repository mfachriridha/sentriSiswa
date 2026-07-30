<?php

use App\Models\Pengaturan;
use App\Models\PesanWhatsapp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Laporan Absensi Harian Otomatis
|--------------------------------------------------------------------------
|
| Laporan dikirim ke wali kelas lewat WhatsApp dalam jendela sempit sesudah jam
| absen ditutup: mulai lima menit sesudahnya, sampai paling lama tiga puluh menit
| sesudahnya. Jendela itu tidak boleh menyeberang tengah malam, karena isi
| laporannya adalah absensi hari ini - kalau tanggalnya sudah berganti, yang
| dilaporkan jadi hari yang salah.
|
*/

afterEach(function () {
    Carbon::setTestNow();
});

/** Sekolah dengan satu kelas berwali yang nomor HP-nya sudah terisi. */
function sekolahSiapKirimLaporan(string $jamSelesai = '07:00'): void
{
    Pengaturan::set('attendance_end_time', $jamSelesai);
    Pengaturan::set('fonnte_token', 'token-yang-sah');
    kelasBerisiSiswa();

    Http::fake([
        'api.fonnte.com/*' => Http::response(['status' => true, 'id' => ['123']], 200),
    ]);
}

test('laporan terkirim di dalam jendela sesudah jam absen ditutup', function () {
    Carbon::setTestNow('2026-07-30 07:10:00'); // Kamis, 10 menit sesudah 07:00.
    sekolahSiapKirimLaporan();

    $this->artisan('app:send-attendance-report')->assertSuccessful();

    expect(PesanWhatsapp::where('tipe_pesan', 'attendance_report')->count())->toBe(1);
});

test('laporan belum dikirim sebelum masa tenggang lima menit terlewati', function () {
    Carbon::setTestNow('2026-07-30 07:02:00');
    sekolahSiapKirimLaporan();

    $this->artisan('app:send-attendance-report')
        ->expectsOutputToContain('Belum waktunya kirim laporan')
        ->assertSuccessful();

    expect(PesanWhatsapp::count())->toBe(0);
});

test('laporan tidak dikirim lagi setelah jendela tiga puluh menit berakhir', function () {
    Carbon::setTestNow('2026-07-30 07:45:00');
    sekolahSiapKirimLaporan();

    $this->artisan('app:send-attendance-report')
        ->expectsOutputToContain('Lewat batas waktu pengiriman')
        ->assertSuccessful();

    expect(PesanWhatsapp::count())->toBe(0);
});

test('jam absen yang ditutup terlalu malam diberi tahu, bukan diam-diam tidak mengirim', function () {
    // Jendela kirim untuk 23:59 jatuh pada 00:04 hari berikutnya. Tanggal now()
    // ikut berganti setiap kali diperiksa, jadi jendelanya selalu kabur ke esok
    // hari dan tidak pernah tercapai. Dulu keadaan ini cuma menghasilkan pesan
    // "belum waktunya" berulang tanpa ada yang tahu laporannya mati.
    Carbon::setTestNow('2026-07-30 23:59:00');
    sekolahSiapKirimLaporan('23:59');

    $this->artisan('app:send-attendance-report')
        ->expectsOutputToContain('terlalu malam')
        ->assertSuccessful();

    expect(PesanWhatsapp::count())->toBe(0);
});

test('jendela kirim dipotong di penghujung hari, tidak menyeberang tengah malam', function () {
    // Jam selesai 23:40 membuat batas atas alami jatuh pada 00:10 esok hari.
    // Batas itu dipotong ke 23:59:59 supaya laporan tidak terkirim dengan tanggal
    // yang sudah berganti.
    Carbon::setTestNow('2026-07-30 23:50:00');
    sekolahSiapKirimLaporan('23:40');

    $this->artisan('app:send-attendance-report')->assertSuccessful();

    expect(PesanWhatsapp::where('tipe_pesan', 'attendance_report')->count())->toBe(1);
});

test('laporan tidak dikirim di hari yang bukan hari absensi', function () {
    Carbon::setTestNow('2026-08-01 07:10:00'); // Sabtu.
    sekolahSiapKirimLaporan();

    $this->artisan('app:send-attendance-report')
        ->expectsOutputToContain('bukan hari aktif absensi')
        ->assertSuccessful();

    expect(PesanWhatsapp::count())->toBe(0);
});

test('opsi force mengabaikan jendela waktu', function () {
    Carbon::setTestNow('2026-07-30 12:00:00'); // Jauh di luar jendela.
    sekolahSiapKirimLaporan();
    Queue::fake();

    $this->artisan('app:send-attendance-report --force')->assertSuccessful();

    expect(PesanWhatsapp::where('tipe_pesan', 'attendance_report')->count())->toBe(1);
});
