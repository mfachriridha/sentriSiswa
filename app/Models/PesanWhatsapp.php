<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesanWhatsapp extends Model
{
    protected $table = 'pesan_whatsapp';

    protected $fillable = [
        'kelas_id',
        'telepon_penerima',
        'nama_penerima',
        'tipe_pesan',
        'id_pesan_provider',
        'isi_pesan',
        'status',
        'respons',
        'dikirim_pada',
    ];

    protected function casts(): array
    {
        return [
            'dikirim_pada' => 'datetime',
        ];
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}
