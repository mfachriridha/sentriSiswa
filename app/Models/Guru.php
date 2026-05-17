<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Guru extends Model
{
    protected $fillable = ['user_id', 'nip', 'nama', 'no_hp'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function waliKelas(): HasOne
    {
        return $this->hasOne(WaliKelas::class);
    }

    public function bkTingkat(): HasMany
    {
        return $this->hasMany(BkTingkat::class);
    }

    public function pengajuanPoin(): HasMany
    {
        return $this->hasMany(PengajuanPoin::class, 'guru_id');
    }
}
