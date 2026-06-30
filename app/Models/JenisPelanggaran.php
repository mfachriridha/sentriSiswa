<?php

namespace App\Models;

use Database\Factories\ViolationTypeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama', 'kategori', 'pengurangan_poin', 'keterangan', 'aktif'])]
class JenisPelanggaran extends Model
{
    /** @use HasFactory<ViolationTypeFactory> */
    use HasFactory;

    protected $table = 'jenis_pelanggaran';

    public const string CATEGORY_LIGHT = 'light';

    public const string CATEGORY_MEDIUM = 'medium';

    public const string CATEGORY_HEAVY = 'heavy';

    public const string CATEGORY_SEVERE = 'severe';

    public static function categoryLabels(): array
    {
        return [
            self::CATEGORY_LIGHT => 'Sanksi Ringan',
            self::CATEGORY_MEDIUM => 'Sanksi Sedang',
            self::CATEGORY_HEAVY => 'Sanksi Berat',
            self::CATEGORY_SEVERE => 'Sanksi Amat Berat',
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
