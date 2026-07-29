<?php

namespace App\Services;

use App\Models\Pengguna;

class FotoPengguna
{
    /**
     * Foto pengguna disimpan di tempat berbeda tergantung perannya. Urutan
     * pencariannya dikumpulkan di sini supaya sidebar, bilah bawah, dan tampilan
     * lain tidak masing-masing menuliskan ulang aturan yang sama.
     */
    public static function url(Pengguna $pengguna): ?string
    {
        $path = match (true) {
            filled($pengguna->foto) => $pengguna->foto,
            $pengguna->isGuru() => $pengguna->profilGuru?->foto,
            $pengguna->isSiswa() => $pengguna->profilSiswa?->foto,
            default => null,
        };

        return filled($path) ? asset('storage/'.$path) : null;
    }
}
