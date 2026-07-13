<?php

use App\Exports\ArrayExport;
use App\Mail\OtpMail;
use App\Models\Absensi;
use App\Models\JenisPelanggaran;
use App\Models\Kelas;
use App\Models\PelanggaranSiswa;
use App\Models\PengajuanPoin;
use App\Models\Pengguna;
use App\Models\ProfilGuru;
use App\Models\ProfilSiswa;
use App\Models\TataTertib;
use App\Models\TokenAksesAbsensi;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
 // ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Pembantu Pengujian Black Box
|--------------------------------------------------------------------------
|
| Pengujian di proyek ini bersifat black box: setiap pengujian menempuh alur
| yang sama seperti pengguna dan hanya memeriksa apa yang muncul di layar.
| Karena itu, masuk ke aplikasi pun harus lewat halaman masuk, bukan lewat
| jalan pintas dari kode.
|
*/

/**
 * Masuk ke aplikasi lewat halaman masuk, persis seperti pengguna biasa.
 *
 * Semua akun yang dibuat lewat pabrik data memakai kata sandi "password".
 */
function masukSebagai(Pengguna $pengguna, string $kataSandi = 'password'): void
{
    test()->post('/login', [
        'email' => $pengguna->email,
        'password' => $kataSandi,
    ]);
}

/**
 * Membuat berkas Excel sungguhan untuk diunggah, seperti berkas yang disusun
 * admin dari templat yang diunduh.
 *
 * @param  list<list<string>>  $baris  Baris pertama adalah judul kolom.
 */
function berkasExcel(string $namaBerkas, array $baris): UploadedFile
{
    $jalurSementara = tempnam(sys_get_temp_dir(), 'impor').'.xlsx';

    Excel::store(
        new ArrayExport(array_shift($baris), $baris),
        basename($jalurSementara),
        'local',
    );

    $jalurTersimpan = Storage::disk('local')
        ->path(basename($jalurSementara));

    return new UploadedFile(
        $jalurTersimpan,
        $namaBerkas,
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        null,
        true,
    );
}

/*
|--------------------------------------------------------------------------
| Kondisi Awal Kelas dan Isinya
|--------------------------------------------------------------------------
|
| Beberapa fitur dipakai bersama oleh wali kelas, BK, dan kesiswaan, jadi
| kondisi awalnya disiapkan di sini supaya sama persis di semua pengujian.
|
*/

/**
 * Wali kelas beserta kelas dan seorang siswa yang sudah terdaftar di dalamnya.
 * Wali kelas langsung masuk ke aplikasi.
 *
 * @return array{0: Pengguna, 1: Kelas, 2: ProfilSiswa}
 */
function waliKelasDenganKelas(string $namaSiswa = 'Ahmad Fauzi', string $nisn = '1234567890'): array
{
    [$wali, $kelas, $siswa] = kelasBerisiSiswa($namaSiswa, $nisn);

    masukSebagai($wali);

    return [$wali, $kelas, $siswa];
}

/**
 * Sebuah kelas beserta wali kelas dan seorang siswa di dalamnya, tanpa ada yang
 * masuk ke aplikasi. Dipakai pengujian peran lain yang cuma butuh kelasnya sudah
 * terisi, misalnya kesiswaan atau BK.
 *
 * @return array{0: Pengguna, 1: Kelas, 2: ProfilSiswa}
 */
function kelasBerisiSiswa(string $namaSiswa = 'Ahmad Fauzi', string $nisn = '1234567890'): array
{
    $wali = Pengguna::factory()->homeroom()->create([
        'nama' => 'Raka Pradipta',
        'email' => 'wali.kelas@sentrisiswa.test',
        'status' => 'registered',
    ]);

    // Guru selalu punya NIP, karena NIP itulah yang dipakai admin saat mendaftarkannya.
    ProfilGuru::factory()->homeroom()->create([
        'pengguna_id' => $wali->id,
        'nip' => '198701012010011001',
        'telepon' => '081234567890',
    ]);

    $kelas = Kelas::create([
        'nama' => '10 IPA 1',
        'tingkat' => '10',
        'wali_kelas_id' => $wali->id,
    ]);

    $penggunaSiswa = Pengguna::factory()->student()->create([
        'nama' => $namaSiswa,
        'status' => 'registered',
    ]);

    $siswa = ProfilSiswa::factory()->create([
        'pengguna_id' => $penggunaSiswa->id,
        'nisn' => $nisn,
        'nis' => '10001',
        'kelas_id' => $kelas->id,
    ]);

    return [$wali, $kelas, $siswa];
}

/** Menambah seorang siswa lain ke sebuah kelas. */
function siswaLainDiKelas(int $kelasId, string $nama, string $nisn, string $nis): ProfilSiswa
{
    $pengguna = Pengguna::factory()->student()->create([
        'nama' => $nama,
        'status' => 'registered',
    ]);

    return ProfilSiswa::factory()->create([
        'pengguna_id' => $pengguna->id,
        'nisn' => $nisn,
        'nis' => $nis,
        'kelas_id' => $kelasId,
    ]);
}

/** Mencatat kehadiran seorang siswa pada tanggal tertentu. */
function catatKehadiran(string $nisn, string $tanggal, string $status): void
{
    Absensi::create([
        'profil_siswa_id' => $nisn,
        'tanggal' => $tanggal,
        'status' => $status,
    ]);
}

/**
 * Mencatat kehadiran lengkap dengan jam absen dan foto selfie-nya, seperti hasil
 * siswa yang benar-benar absen sendiri lewat aplikasi.
 */
function catatKehadiranBerselfie(string $nisn, string $tanggal, string $jamMasuk = '06:45'): Absensi
{
    return Absensi::create([
        'profil_siswa_id' => $nisn,
        'tanggal' => $tanggal,
        'status' => 'hadir',
        'waktu_masuk' => $tanggal.' '.$jamMasuk.':00',
        'path_selfie' => "attendance-selfies/{$nisn}/{$tanggal}.jpg",
    ]);
}

/** Isian catatan pelanggaran yang sah. */
function dataPelanggaranSiswa(ProfilSiswa $siswa, JenisPelanggaran $jenis, array $ubahan = []): array
{
    return array_merge([
        'profil_siswa_id' => $siswa->nisn,
        'jenis_pelanggaran_id' => $jenis->id,
        'tanggal_pelanggaran' => '2026-07-06',
        'catatan' => 'Terlambat 20 menit tanpa keterangan.',
    ], $ubahan);
}

/** Isian jenis pelanggaran yang sah. */
function dataJenisPelanggaran(array $ubahan = []): array
{
    return array_merge([
        'nama' => 'Terlambat masuk kelas',
        'kategori' => 'ringan',
        'pengurangan_poin' => 10,
        'keterangan' => 'Datang setelah bel masuk berbunyi.',
        'aktif' => 1,
    ], $ubahan);
}

/** Sebuah tata tertib yang sudah ada di daftar. */
function tataTertibTersimpan(string $judul, bool $dipublikasikan = false): TataTertib
{
    return TataTertib::create([
        'judul' => $judul,
        'path_file' => 'school-rules/'.md5($judul).'.pdf',
        'dipublikasikan' => $dipublikasikan,
    ]);
}

/** Sebuah pengajuan poin dari wali kelas yang masih menunggu keputusan kesiswaan. */
function pengajuanPoinMenunggu(ProfilSiswa $siswa, int $waliId, string $alasan = 'Juara lomba cerdas cermat.'): PengajuanPoin
{
    return PengajuanPoin::create([
        'profil_siswa_id' => $siswa->nisn,
        'diajukan_oleh_id' => $waliId,
        'alasan' => $alasan,
        'status' => 'pending',
    ]);
}

/** Pengguna yang sudah punya akun, dipakai pengujian pemulihan kata sandi. */
function penggunaLupaSandi(): Pengguna
{
    return Pengguna::factory()->student()->create([
        'nama' => 'Ahmad Fauzi',
        'email' => 'ahmad@sentrisiswa.test',
        'status' => 'registered',
    ]);
}

/** Tautan pemulihan yang diterima pengguna lewat email. */
function tautanPemulihan(Pengguna $pengguna): string
{
    $tautan = '';

    Notification::assertSentTo($pengguna, ResetPassword::class, function (ResetPassword $notifikasi) use (&$tautan): bool {
        $tautan = "/reset-sandi/{$notifikasi->token}";

        return true;
    });

    return $tautan;
}

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

/** Link cek absensi yang dikirim ke WhatsApp wali kelas untuk diteruskan ke orang tua. */
function linkAbsensiOrangTua(int $kelasId, string $tanggal): string
{
    $token = TokenAksesAbsensi::buatAtauPerbarui($kelasId, $tanggal);

    return "/absensi/publik/{$token->token}";
}

/** Kesiswaan yang sudah masuk ke aplikasi. */
function kesiswaanMasuk(): Pengguna
{
    $kesiswaan = Pengguna::factory()->studentAffairs()->create([
        'nama' => 'Bagas Kesiswaan',
        'email' => 'kesiswaan@sentrisiswa.test',
        'status' => 'registered',
    ]);

    masukSebagai($kesiswaan);

    return $kesiswaan;
}

/** Kode OTP yang diterima pengguna lewat email. */
function kodeOtpTerkirim(): string
{
    $kode = '';

    Mail::assertSent(OtpMail::class, function (OtpMail $surel) use (&$kode): bool {
        $kode = $surel->otp;

        return true;
    });

    return $kode;
}

/** Wali kelas yang sudah masuk dan sudah meminta kode OTP untuk mengganti kata sandi. */
function mintaKodeGantiSandi(): string
{
    waliKelasDenganKelas();

    test()->post('/wali-kelas/profil/ganti-sandi');

    return kodeOtpTerkirim();
}

/** Siswa yang sudah masuk ke aplikasi, beserta kelas tempatnya terdaftar. */
function siswaMasuk(): ProfilSiswa
{
    [, , $siswa] = kelasBerisiSiswa();

    masukSebagai($siswa->pengguna);

    return $siswa;
}

/** Selfie absensi, dengan ukuran dalam kilobita. */
function selfieAbsensi(int $ukuranKb = 100): UploadedFile
{
    return UploadedFile::fake()->create('selfie.jpg', $ukuranKb, 'image/jpeg');
}

/**
 * Guru BK yang sudah masuk ke aplikasi. Tiap guru BK hanya memegang satu tingkat,
 * dan hanya boleh memantau siswa di tingkat itu.
 */
function bkMasuk(string $tingkat = '10'): Pengguna
{
    $bk = Pengguna::factory()->counselor()->create([
        'nama' => 'Ibu Sari',
        'email' => 'bk@sentrisiswa.test',
        'status' => 'registered',
    ]);

    ProfilGuru::factory()->counselor()->create([
        'pengguna_id' => $bk->id,
        'tingkat' => $tingkat,
    ]);

    masukSebagai($bk);

    return $bk;
}

/** Berkas tata tertib berbentuk PDF, dengan ukuran dalam kilobita. */
function berkasTataTertib(int $ukuranKb = 500): UploadedFile
{
    return UploadedFile::fake()->create('tata-tertib-sekolah.pdf', $ukuranKb, 'application/pdf');
}

/** Sebuah jenis pelanggaran yang siap dipilih saat mencatat pelanggaran. */
function jenisPelanggaranTersedia(array $ubahan = []): JenisPelanggaran
{
    return JenisPelanggaran::create(array_merge([
        'nama' => 'Terlambat masuk kelas',
        'kategori' => 'ringan',
        'pengurangan_poin' => 10,
        'aktif' => true,
    ], $ubahan));
}

/** Mencatat sebuah pelanggaran yang sudah disetujui untuk seorang siswa. */
function catatPelanggaran(
    ProfilSiswa $siswa,
    string $nama,
    string $kategori,
    string $tanggal,
    int $pengurangan = 5,
): PelanggaranSiswa {
    $jenis = JenisPelanggaran::firstOrCreate(
        ['nama' => $nama],
        ['kategori' => $kategori, 'pengurangan_poin' => $pengurangan, 'aktif' => true],
    );

    return PelanggaranSiswa::create([
        'profil_siswa_id' => $siswa->nisn,
        'jenis_pelanggaran_id' => $jenis->id,
        'tanggal_pelanggaran' => $tanggal,
        'nama_pelanggaran' => $nama,
        'kategori_pelanggaran' => $kategori,
        'pengurangan_poin' => $pengurangan,
        'status' => 'approved',
    ]);
}
