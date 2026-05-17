<?php

namespace App\Enums;

enum StatusPengajuanPoin: string
{
    case Pending = 'pending';
    case Disetujui = 'disetujui';
    case Ditolak = 'ditolak';
}
