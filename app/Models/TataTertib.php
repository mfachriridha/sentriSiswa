<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TataTertib extends Model
{
    protected $table = 'tata_tertib';

    protected $fillable = ['judul', 'file', 'diterbitkan', 'diunggah_oleh'];

    protected function casts(): array
    {
        return [
            'diterbitkan' => 'boolean',
        ];
    }

    public function pengunggah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diunggah_oleh');
    }
}
