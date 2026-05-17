<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonfigurasiAbsensi extends Model
{
    protected $table = 'konfigurasi_absensi';

    protected $fillable = ['kunci', 'nilai'];
}
