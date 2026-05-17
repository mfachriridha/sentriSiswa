<?php

namespace App\Enums;

enum StatusAbsensi: string
{
    case Hadir = 'hadir';
    case Terlambat = 'terlambat';
    case Sakit = 'sakit';
    case Izin = 'izin';
    case TanpaKeterangan = 'tanpa_keterangan';
}
