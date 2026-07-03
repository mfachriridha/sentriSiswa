<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['profil_siswa_id', 'diajukan_oleh_id', 'alasan', 'status', 'jumlah_poin', 'disetujui_oleh_id', 'disetujui_pada', 'alasan_penolakan'])]
class PengajuanPoin extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_poin';

    public function profilSiswa(): BelongsTo
    {
        return $this->belongsTo(ProfilSiswa::class, 'profil_siswa_id');
    }

    public function diajukanOleh(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'diajukan_oleh_id');
    }

    public function disetujuiOleh(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'disetujui_oleh_id');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public static function statusLabels(): array
    {
        return [
            'pending' => 'Menunggu ACC',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
        ];
    }

    protected function casts(): array
    {
        return [
            'jumlah_poin' => 'integer',
            'disetujui_pada' => 'datetime',
        ];
    }
}
