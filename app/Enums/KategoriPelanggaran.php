<?php

namespace App\Enums;

enum KategoriPelanggaran: string
{
    case Ringan = 'ringan';
    case Sedang = 'sedang';
    case Berat = 'berat';
    case AmatBerat = 'amat_berat';
}
