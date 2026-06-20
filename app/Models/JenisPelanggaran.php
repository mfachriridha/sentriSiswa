<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisPelanggaran extends Model
{
    protected $table = 'jenis_pelanggaran';

    public const KATEGORI_RINGAN = 'ringan';

    public const KATEGORI_SEDANG = 'sedang';

    public const KATEGORI_BERAT = 'berat';

    public const KATEGORI_AMAT_BERAT = 'amat_berat';

    protected $fillable = ['kategori', 'nama', 'poin', 'deskripsi', 'aktif'];

    protected function casts(): array
    {
        return [
            'poin' => 'integer',
            'aktif' => 'boolean',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function labelKategori(): array
    {
        return [
            self::KATEGORI_RINGAN => 'Sanksi Ringan',
            self::KATEGORI_SEDANG => 'Sanksi Sedang',
            self::KATEGORI_BERAT => 'Sanksi Berat',
            self::KATEGORI_AMAT_BERAT => 'Sanksi Amat Berat',
        ];
    }

    public function pelanggaran(): HasMany
    {
        return $this->hasMany(Pelanggaran::class, 'jenis_id');
    }
}
