<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['pengguna_id', 'otp', 'tipe', 'email_baru', 'sandi_baru', 'kadaluwarsa_pada'])]
class TokenOtp extends Model
{
    protected $table = 'token_otp';

    protected function casts(): array
    {
        return [
            'kadaluwarsa_pada' => 'datetime',
            'digunakan_pada' => 'datetime',
        ];
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    public function sudahExpired(): bool
    {
        return $this->kadaluwarsa_pada->isPast();
    }

    public function sudahDipakai(): bool
    {
        return $this->digunakan_pada !== null;
    }

    public function scopeBerlaku(Builder $query): Builder
    {
        return $query
            ->whereNull('digunakan_pada')
            ->where('kadaluwarsa_pada', '>', now());
    }
}
