<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PoinPelanggaran extends Model
{
    protected $fillable = ['kategori', 'jenis_pelanggaran', 'poin'];

    public function pelanggaranSiswa(): HasMany
    {
        return $this->hasMany(PelanggaranSiswa::class);
    }
}
