<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['kunci', 'nilai'])]
class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    public static function get(string $kunci, mixed $default = null): mixed
    {
        return cache()->remember("pengaturan:{$kunci}", now()->addDay(), fn () => static::where('kunci', $kunci)->value('nilai')) ?? $default;
    }

    public static function set(string $kunci, mixed $nilai): void
    {
        static::updateOrCreate(['kunci' => $kunci], ['nilai' => $nilai]);
        cache()->forget("pengaturan:{$kunci}");
    }
}
