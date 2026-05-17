<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PelanggaranSiswa extends Model
{
    protected $fillable = ['user_id', 'poin_pelanggaran_id', 'keterangan', 'dicatat_oleh'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function poinPelanggaran(): BelongsTo
    {
        return $this->belongsTo(PoinPelanggaran::class);
    }

    public function dicatatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }
}
