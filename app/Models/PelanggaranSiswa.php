<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['profil_siswa_id', 'jenis_pelanggaran_id', 'dicatat_oleh_id', 'tanggal_pelanggaran', 'nama_pelanggaran', 'kategori_pelanggaran', 'pengurangan_poin', 'catatan', 'status', 'disetujui_oleh_id', 'disetujui_pada', 'alasan_penolakan'])]
class PelanggaranSiswa extends Model
{
    /** @use HasFactory<\Database\Factories\PelanggaranSiswaFactory> */
    use HasFactory;

    protected $table = 'pelanggaran_siswa';

    const CREATED_AT = 'dibuat_pada';

    const UPDATED_AT = 'diperbarui_pada';

    public function profilSiswa(): BelongsTo
    {
        return $this->belongsTo(ProfilSiswa::class, 'profil_siswa_id');
    }

    public function jenisPelanggaran(): BelongsTo
    {
        return $this->belongsTo(JenisPelanggaran::class, 'jenis_pelanggaran_id');
    }

    public function dicatatOleh(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'dicatat_oleh_id');
    }

    public function disetujuiOleh(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'disetujui_oleh_id');
    }

    public function scopeDisetujui(Builder $query): Builder
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
            'tanggal_pelanggaran' => 'date',
            'pengurangan_poin' => 'integer',
            'disetujui_pada' => 'datetime',
        ];
    }
}
