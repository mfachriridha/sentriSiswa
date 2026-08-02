<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama', 'grup', 'poin', 'urutan'])]
class KategoriPengajuanPoin extends Model
{
    use HasFactory;

    protected $table = 'kategori_pengajuan_poin';

    const CREATED_AT = 'dibuat_pada';

    const UPDATED_AT = 'diperbarui_pada';

    public function pengajuanPoin(): HasMany
    {
        return $this->belongsTo(PengajuanPoin::class, 'kategori_pengajuan_poin_id');
    }

    protected function casts(): array
    {
        return [
            'poin' => 'integer',
            'urutan' => 'integer',
        ];
    }
}
