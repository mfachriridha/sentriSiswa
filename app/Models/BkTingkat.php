<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BkTingkat extends Model
{
    protected $fillable = ['guru_id', 'tingkat'];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }
}
