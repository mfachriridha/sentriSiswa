<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    protected $fillable = ['kunci', 'nilai'];

    public static function ambil(string $kunci, mixed $default = null): mixed
    {
        $pengaturan = static::where('kunci', $kunci)->first();

        return $pengaturan?->nilai ?? $default;
    }

    public static function simpan(string $kunci, mixed $nilai): void
    {
        static::updateOrCreate(
            ['kunci' => $kunci],
            ['nilai' => is_array($nilai) ? json_encode($nilai) : (string) $nilai],
        );
    }
}
