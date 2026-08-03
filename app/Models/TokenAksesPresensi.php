<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TokenAksesPresensi extends Model
{
    protected $table = 'token_akses_absensi';

    const CREATED_AT = 'dibuat_pada';

    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = ['token', 'kelas_id', 'tanggal', 'kadaluwarsa_pada'];

    protected $casts = ['tanggal' => 'date', 'kadaluwarsa_pada' => 'datetime'];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function sudahExpired(): bool
    {
        return $this->kadaluwarsa_pada->isPast();
    }

    public static function buatAtauPerbarui(int $kelasId, string $tanggal): self
    {
        return static::updateOrCreate(
            ['kelas_id' => $kelasId, 'tanggal' => $tanggal],
            [
                'token' => Str::random(64),
                'kadaluwarsa_pada' => now()->endOfDay(),
            ]
        );
    }
}
