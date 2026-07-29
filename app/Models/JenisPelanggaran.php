<?php

namespace App\Models;

use Database\Factories\JenisPelanggaranFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama', 'kategori', 'pengurangan_poin', 'keterangan', 'aktif'])]
class JenisPelanggaran extends Model
{
    /** @use HasFactory<JenisPelanggaranFactory> */
    use HasFactory;

    protected $table = 'jenis_pelanggaran';

    const CREATED_AT = 'dibuat_pada';

    const UPDATED_AT = 'diperbarui_pada';

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

    /**
     * Warna lencana tiap kategori. Ditaruh di sini bersama nilai kategorinya
     * supaya tampilan tidak menuliskan ulang daftar kategori sendiri - salinan
     * seperti itu pernah memakai nama kategori berbahasa Inggris sementara yang
     * tersimpan di basis data berbahasa Indonesia, sehingga tak pernah cocok dan
     * semua lencana jatuh ke warna abu-abu.
     *
     * @return array<string, string>
     */
    public static function categoryBadgeClasses(): array
    {
        return [
            self::CATEGORY_LIGHT => 'bg-green-50 text-green-700',
            self::CATEGORY_MEDIUM => 'bg-amber-50 text-amber-700',
            self::CATEGORY_HEAVY => 'bg-orange-50 text-orange-700',
            self::CATEGORY_SEVERE => 'bg-red-50 text-red-700',
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
