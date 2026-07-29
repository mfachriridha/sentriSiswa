<?php

namespace App\Services;

class FormatNama
{
    /**
     * Merapikan nama dari berkas impor sekolah, yang biasanya ditulis SEMUA
     * HURUF BESAR, menjadi Kapital Tiap Kata.
     *
     * Gelar akademik sengaja tidak ikut dirapikan. Kapitalisasinya tidak
     * mengikuti pola kata biasa - "ST.", "S.PdI", "S.KM" akan rusak jadi "St.",
     * "S.Pdi", "S.Km" kalau diseragamkan. Di berkas impor gelar ditulis sesudah
     * koma dan kapitalisasinya sudah benar, jadi bagian itu dibiarkan apa adanya.
     *
     * Contoh: "NURWANTI PUJI L , ST., S.Pd" -> "Nurwanti Puji L, ST., S.Pd"
     */
    public static function rapikan(string $nama): string
    {
        $nama = trim($nama);

        if ($nama === '') {
            return '';
        }

        // Cuma dipecah di koma pertama: sisanya (bisa lebih dari satu gelar)
        // tetap satu kesatuan yang tidak disentuh.
        [$namaOrang, $gelar] = array_pad(explode(',', $nama, 2), 2, null);

        $rapi = mb_convert_case(trim($namaOrang), MB_CASE_TITLE, 'UTF-8');

        return $gelar === null ? $rapi : $rapi.', '.trim($gelar);
    }
}
