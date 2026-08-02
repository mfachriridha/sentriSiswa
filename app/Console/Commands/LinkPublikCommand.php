<?php

namespace App\Console\Commands;

use App\Models\Kelas;
use App\Models\TokenAksesAbsensi;
use Illuminate\Console\Command;

class LinkPublikCommand extends Command
{
    /**
     * Nama dan tanda tangan perintah console.
     *
     * @var string
     */
    protected $signature = 'link:publik {kelas? : Nama atau ID kelas (opsional)}';

    /**
     * Deskripsi perintah console.
     *
     * @var string
     */
    protected $description = 'Dapatkan atau buatkan link publik absensi anak untuk kelas hari ini';

    /**
     * Eksekusi perintah console.
     */
    public function handle(): int
    {
        $inputKelas = $this->argument('kelas');
        $tanggalHariIni = now()->toDateString();

        if ($inputKelas) {
            $kelasQuery = Kelas::where('nama', 'like', "%{$inputKelas}%")
                ->orWhere('id', $inputKelas);
            $kelases = $kelasQuery->get();

            if ($kelases->isEmpty()) {
                $this->error("Kelas '{$inputKelas}' tidak ditemukan.");

                return self::FAILURE;
            }
        } else {
            $kelases = Kelas::all();

            if ($kelases->isEmpty()) {
                $this->error('Belum ada kelas terdaftar di sistem.');

                return self::FAILURE;
            }
        }

        $this->info("=== Link Publik Absensi Hari Ini ({$tanggalHariIni}) ===");
        $this->newLine();

        foreach ($kelases as $kelas) {
            $tokenObj = TokenAksesAbsensi::buatAtauPerbarui($kelas->id, $tanggalHariIni);
            $url = route('absensi.publik', $tokenObj->token);

            $this->line("<fg=yellow;options=bold>📌 Kelas {$kelas->nama}:</>");
            $this->line("<fg=green>{$url}</>");
            $this->newLine();
        }

        return self::SUCCESS;
    }
}
