<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama', 'kategori', 'pengurangan_poin', 'keterangan', 'aktif'])]
class JenisPelanggaran extends Model
{
    /** @use HasFactory<\Database\Factories\JenisPelanggaranFactory> */
    use HasFactory;

    protected $table = 'jenis_pelanggaran';

    public const string CATEGORY_LIGHT = 'ringan';

    public const string CATEGORY_MEDIUM = 'sedang';

    public const string CATEGORY_HEAVY = 'berat';

    public const string CATEGORY_SEVERE = 'sangat_berat';

    public static function categoryLabels(): array
    {
        return [
            self::CATEGORY_LIGHT => 'Sanksi Ringan',
            self::CATEGORY_MEDIUM => 'Sanksi Sedang',
            self::CATEGORY_HEAVY => 'Sanksi Berat',
            self::CATEGORY_SEVERE => 'Sanksi Sangat Berat',
        ];
    }

    public static function categoryRanges(): array
    {
        return [
            self::CATEGORY_LIGHT => [5, 25],
            self::CATEGORY_MEDIUM => [26, 50],
            self::CATEGORY_HEAVY => [51, 75],
            self::CATEGORY_SEVERE => [76, 100],
        ];
    }

    public function pelanggaranSiswa(): HasMany
    {
        return $this->hasMany(PelanggaranSiswa::class, 'jenis_pelanggaran_id');
    }

    protected function casts(): array
    {
        return [
            'pengurangan_poin' => 'integer',
            'aktif' => 'boolean',
        ];
    }
}
