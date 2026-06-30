<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TokenAksesAbsensi extends Model
{
    protected $table = 'token_akses_absensi';

    protected $fillable = ['token', 'class_id', 'tanggal', 'expires_at'];

    protected $casts = ['tanggal' => 'date', 'expires_at' => 'datetime'];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function sudahExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public static function buatAtauPerbarui(int $classId, string $tanggal): self
    {
        return static::updateOrCreate(
            ['class_id' => $classId, 'tanggal' => $tanggal],
            [
                'token' => Str::random(64),
                'expires_at' => now()->endOfDay(),
            ]
        );
    }
}
