<?php

namespace Database\Seeders;

use App\Models\Absensi;
use App\Models\JenisPelanggaran;
use App\Models\Kelas;
use App\Models\PelanggaranSiswa;
use App\Models\PengajuanPoin;
use App\Models\Pengaturan;
use App\Models\Pengguna;
use App\Models\ProfilSiswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

/**
 * Menghidupkan data sekolah yang sudah diimpor: siswa didaftarkan, absensi
 * sebulan terakhir dibuat, sebagian siswa diberi pelanggaran, dan pengajuan poin
 * diisi supaya antrean persetujuan kesiswaan tidak kosong.
 *
 * Seeder ini TIDAK PERNAH menghapus apa pun. Data yang sudah ada hanya dilengkapi,
 * jadi aman dijalankan di atas data impor yang asli. Aman pula dijalankan berkali-
 * kali: tiap tahap memeriksa dulu, dan angka acaknya dikunci supaya hasilnya sama.
 *
 * Guru tidak disentuh sama sekali.
 *
 * Jalankan: php artisan db:seed --class=SekolahAktifSeeder
 */
class SekolahAktifSeeder extends Seeder
{
    private const HARI_KE_BELAKANG = 30;

    private const JUMLAH_POTRET = 70;

    private const FOLDER_POTRET = 'attendance-selfies/pool';

    public function run(): void
    {
        // Dikunci supaya seeder yang diulang menghasilkan sekolah yang sama persis.
        mt_srand(20260712);

        $siswa = $this->siswaYangDilibatkan();

        if ($siswa->isEmpty()) {
            $this->command?->warn('Tidak ada siswa yang bisa dihidupkan. Impor data siswa lebih dulu.');

            return;
        }

        $this->command?->info("Menghidupkan {$siswa->count()} siswa di semua kelas.");

        $this->daftarkanSiswa($siswa);
        $this->catatAbsensi($siswa);
        $this->catatPelanggaran($siswa);
        $this->buatPengajuanPoin();
    }

    /**
     * Seluruh siswa yang ikut dihidupkan, semua kelas.
     *
     * @return Collection<int, ProfilSiswa>
     */
    private function siswaYangDilibatkan(): Collection
    {
        return ProfilSiswa::query()
            ->with('pengguna')
            ->whereHas('pengguna', fn ($query) => $query->where('peran', 'siswa'))
            ->orderBy('nisn')
            ->get();
    }

    /** @param Collection<int, ProfilSiswa> $siswa */
    private function daftarkanSiswa(Collection $siswa): void
    {
        $belumDaftar = $siswa->filter(fn (ProfilSiswa $s): bool => $s->pengguna?->status !== 'registered');

        if ($belumDaftar->isEmpty()) {
            $this->command?->line('  Siswa terdaftar    : 0 (semuanya sudah punya akun)');

            return;
        }

        // Kata sandinya sama untuk semua, jadi cukup di-hash sekali. Meng-hash
        // 1.500 kali akan makan waktu berpuluh detik tanpa manfaat apa pun.
        $sandi = Hash::make('password123');

        // Nomor urut emailnya diambil dari posisi siswa di daftar PENUH (bukan
        // cuma yang belum daftar), supaya urutannya stabil walau seeder diulang
        // setelah sebagian siswa sudah terdaftar duluan.
        $nomorUrut = $siswa->values()->mapWithKeys(fn (ProfilSiswa $s, int $i): array => [$s->nisn => $i + 1]);

        DB::transaction(function () use ($belumDaftar, $sandi, $nomorUrut): void {
            foreach ($belumDaftar as $profil) {
                $nomor = str_pad((string) $nomorUrut[$profil->nisn], 3, '0', STR_PAD_LEFT);

                DB::table('pengguna')
                    ->where('id', $profil->pengguna_id)
                    ->update([
                        'status' => 'registered',
                        'email' => "siswa{$nomor}@sentrisiswa.test",
                        'password' => $sandi,
                        'diperbarui_pada' => now(),
                    ]);
            }
        });

        $this->command?->line("  Siswa didaftarkan  : {$belumDaftar->count()}");
    }

    /** @param Collection<int, ProfilSiswa> $siswa */
    private function catatAbsensi(Collection $siswa): void
    {
        $tanggal = $this->hariAbsensi();

        if ($tanggal->isEmpty()) {
            $this->command?->line('  Absensi dibuat     : 0 (tidak ada hari absensi aktif)');

            return;
        }

        $potret = $this->siapkanPotret();
        $hariIni = today()->toDateString();
        $dibuat = 0;
        $antrean = [];

        foreach ($siswa->values() as $urutan => $profil) {
            $pola = $this->polaKehadiran($urutan);
            $selfie = $potret ? $this->potretSiswa($profil->nisn) : null;

            foreach ($tanggal as $tanggalAbsen) {
                $status = $pola[$tanggalAbsen->dayOfYear % count($pola)];

                // Hari ini sengaja dibuat beragam supaya papan pantau "hari ini"
                // tidak kelihatan janggal: ada yang sudah hadir, ada yang memang
                // belum absen.
                if ($tanggalAbsen->toDateString() === $hariIni) {
                    $status = $urutan % 4 === 3 ? 'belum_absen' : 'hadir';
                }

                $hadir = $status === 'hadir';

                $antrean[] = [
                    'profil_siswa_id' => $profil->nisn,
                    'tanggal' => $tanggalAbsen->toDateString(),
                    'status' => $status,
                    'waktu_masuk' => $hadir ? sprintf('06:%02d:00', 35 + ($urutan % 10)) : null,
                    'path_selfie' => $hadir ? $selfie : null,
                    'dibuat_pada' => now(),
                    'diperbarui_pada' => now(),
                ];

                $dibuat++;

                if (count($antrean) >= 500) {
                    $this->simpanAbsensi($antrean);
                    $antrean = [];
                }
            }
        }

        $this->simpanAbsensi($antrean);

        $this->command?->line("  Absensi dibuat     : {$dibuat} catatan, {$tanggal->count()} hari absensi");
    }

    /** @param list<array<string, mixed>> $antrean */
    private function simpanAbsensi(array $antrean): void
    {
        if ($antrean === []) {
            return;
        }

        // Tabel absensi sudah punya indeks unik (profil_siswa_id, tanggal), jadi
        // catatan yang sudah ada diperbarui, bukan digandakan.
        Absensi::upsert($antrean, ['profil_siswa_id', 'tanggal'], ['status', 'waktu_masuk', 'path_selfie', 'diperbarui_pada']);
    }

    /**
     * Hari absensi aktif dalam sebulan terakhir, mengikuti pengaturan sekolah
     * (biasanya Senin sampai Jumat).
     *
     * @return Collection<int, Carbon>
     */
    private function hariAbsensi(): Collection
    {
        return collect(range(0, self::HARI_KE_BELAKANG))
            ->map(fn (int $mundur): Carbon => today()->subDays($mundur))
            ->filter(fn (Carbon $tanggal): bool => Pengaturan::hariAbsenAktif($tanggal))
            ->sortBy(fn (Carbon $tanggal): string => $tanggal->toDateString())
            ->values();
    }

    /**
     * Pola kehadiran seorang siswa. Sebagian besar siswa rajin, sebagian sesekali
     * izin atau sakit, dan sebagian kecil rawan — alpha-nya sengaja dibuat sampai
     * menembus ambang peringatan (3 kali) supaya penandanya benar-benar muncul.
     *
     * @return list<string>
     */
    private function polaKehadiran(int $urutan): array
    {
        return match (true) {
            $urutan % 10 < 6 => ['hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir', 'hadir'],
            $urutan % 10 < 8 => ['hadir', 'hadir', 'hadir', 'izin', 'hadir', 'hadir', 'hadir'],
            $urutan % 10 === 8 => ['hadir', 'sakit', 'hadir', 'izin', 'hadir', 'hadir', 'sakit'],
            default => ['alpha', 'hadir', 'alpha', 'izin', 'alpha', 'hadir', 'sakit', 'alpha', 'hadir'],
        };
    }

    /** @param Collection<int, ProfilSiswa> $siswa */
    private function catatPelanggaran(Collection $siswa): void
    {
        $jenis = JenisPelanggaran::where('aktif', true)->get();
        $pencatat = Pengguna::where('peran', 'kesiswaan')->value('id');

        if ($jenis->isEmpty() || ! $pencatat) {
            $this->command?->line('  Pelanggaran        : 0 (jenis pelanggaran atau akun kesiswaan belum ada)');

            return;
        }

        $sudahPunya = PelanggaranSiswa::pluck('profil_siswa_id')->flip();
        $antrean = [];

        // Siapa yang melanggar ditentukan dari urutan siswa di daftar penuh, bukan
        // dari daftar yang sudah disaring. Kalau memakai daftar tersaring, seeder
        // yang diulang akan memilih orang lain lagi dan pelanggarannya terus
        // bertambah tiap kali dijalankan.
        foreach ($siswa->values() as $urutan => $profil) {
            // Kira-kira satu dari delapan siswa. Sisanya sengaja dibiarkan bersih
            // supaya ada kontras di monitoring.
            if ($urutan % 8 !== 0) {
                continue;
            }

            if ($sudahPunya->has($profil->nisn)) {
                continue;
            }

            // Segelintir siswa dibuat berat sekali, supaya ada yang poinnya jeblok
            // sampai kategori Perhatian.
            $jumlah = $urutan % 40 === 0 ? 3 : ($urutan % 24 === 0 ? 2 : 1);

            for ($ke = 0; $ke < $jumlah; $ke++) {
                $pelanggaran = $jenis[mt_rand(0, $jenis->count() - 1)];
                $tanggal = today()->subDays(mt_rand(1, self::HARI_KE_BELAKANG));

                $antrean[] = [
                    'profil_siswa_id' => $profil->nisn,
                    'jenis_pelanggaran_id' => $pelanggaran->id,
                    'dicatat_oleh_id' => $pencatat,
                    'tanggal_pelanggaran' => $tanggal->toDateString(),
                    'nama_pelanggaran' => $pelanggaran->nama,
                    'kategori_pelanggaran' => $pelanggaran->kategori,
                    'pengurangan_poin' => $pelanggaran->pengurangan_poin,
                    'catatan' => 'Dicatat berdasarkan laporan piket harian.',
                    'status' => 'approved',
                    'disetujui_oleh_id' => $pencatat,
                    'disetujui_pada' => now(),
                    'dibuat_pada' => now(),
                    'diperbarui_pada' => now(),
                ];
            }
        }

        foreach (array_chunk($antrean, 500) as $bagian) {
            PelanggaranSiswa::insert($bagian);
        }

        $jumlahSiswa = collect($antrean)->pluck('profil_siswa_id')->unique()->count();
        $this->command?->line('  Pelanggaran dibuat : '.count($antrean)." catatan, menimpa {$jumlahSiswa} siswa");
    }

    private function buatPengajuanPoin(): void
    {
        $kesiswaan = Pengguna::where('peran', 'kesiswaan')->value('id');

        if (! $kesiswaan) {
            $this->command?->line('  Pengajuan poin     : 0 (akun kesiswaan belum ada)');

            return;
        }

        $rencana = [
            ['status' => 'pending', 'alasan' => 'Aktif membantu kegiatan Jumat bersih selama sebulan penuh.'],
            ['status' => 'approved', 'alasan' => 'Juara 1 lomba cerdas cermat tingkat kabupaten.', 'jumlah_poin' => 15],
            ['status' => 'rejected', 'alasan' => 'Membantu guru merapikan ruang kelas.', 'alasan_penolakan' => 'Belum ada bukti pendukung, mohon lengkapi laporannya.'],
            ['status' => 'approved', 'alasan' => 'Mewakili sekolah pada lomba paduan suara tingkat provinsi.', 'jumlah_poin' => 10],
        ];

        $kelas = Kelas::query()
            ->whereNotNull('wali_kelas_id')
            ->with('siswa')
            ->get();

        $dibuat = 0;
        $putaran = 0;

        foreach ($kelas as $rombel) {
            $siswaKelas = $rombel->siswa;

            if ($siswaKelas->isEmpty()) {
                continue;
            }

            // Kelas yang pengajuannya sudah ada dilewati, supaya seeder yang
            // diulang tidak menumpuk antrean persetujuan.
            if (PengajuanPoin::whereIn('profil_siswa_id', $siswaKelas->pluck('nisn'))->exists()) {
                continue;
            }

            foreach (range(0, 1) as $ke) {
                $item = $rencana[$putaran++ % count($rencana)];
                $profil = $siswaKelas[($ke * 7) % $siswaKelas->count()];
                $menunggu = $item['status'] === 'pending';

                PengajuanPoin::create([
                    'profil_siswa_id' => $profil->nisn,
                    'diajukan_oleh_id' => $rombel->wali_kelas_id,
                    'alasan' => $item['alasan'],
                    'status' => $item['status'],
                    'jumlah_poin' => $item['jumlah_poin'] ?? null,
                    'disetujui_oleh_id' => $menunggu ? null : $kesiswaan,
                    'disetujui_pada' => $menunggu ? null : today()->subDays(mt_rand(1, 14)),
                    'alasan_penolakan' => $item['alasan_penolakan'] ?? null,
                ]);

                $dibuat++;
            }
        }

        $this->command?->line("  Pengajuan poin     : {$dibuat} pengajuan");
    }

    /**
     * Menyiapkan kumpulan potret untuk selfie absensi.
     *
     * Diunduh sekali saja, lalu dipakai bergiliran oleh seluruh siswa. Membuat
     * satu berkas per catatan absensi berarti puluhan ribu gambar — makan waktu
     * dan ruang tanpa manfaat.
     *
     * Kalau tidak ada internet atau unduhannya gagal, seeder tidak ikut gagal:
     * selfie-nya dibiarkan kosong.
     */
    private function siapkanPotret(): bool
    {
        // Pengujian tidak boleh menyentuh jaringan.
        if (app()->environment('testing')) {
            return false;
        }

        $disk = Storage::disk('public');

        if ($disk->exists(self::FOLDER_POTRET.'/1.jpg') && $disk->exists(self::FOLDER_POTRET.'/'.self::JUMLAH_POTRET.'.jpg')) {
            return true;
        }

        $this->command?->line('  Mengunduh '.self::JUMLAH_POTRET.' potret untuk selfie absensi...');

        try {
            foreach (range(1, self::JUMLAH_POTRET) as $nomor) {
                $berkas = self::FOLDER_POTRET."/{$nomor}.jpg";

                if ($disk->exists($berkas)) {
                    continue;
                }

                $jawaban = Http::timeout(15)->get("https://i.pravatar.cc/400?img={$nomor}");

                if (! $jawaban->successful()) {
                    throw new \RuntimeException("Gagal mengunduh potret ke-{$nomor}.");
                }

                $disk->put($berkas, $jawaban->body());
            }
        } catch (\Throwable $galat) {
            $this->command?->warn('  Potret gagal diunduh ('.$galat->getMessage().'). Selfie dibiarkan kosong.');

            return false;
        }

        return true;
    }

    /** Tiap siswa selalu dapat potret yang sama, supaya selfie-nya masuk akal. */
    private function potretSiswa(string $nisn): string
    {
        $nomor = ((int) $nisn % self::JUMLAH_POTRET) + 1;

        return self::FOLDER_POTRET."/{$nomor}.jpg";
    }
}
