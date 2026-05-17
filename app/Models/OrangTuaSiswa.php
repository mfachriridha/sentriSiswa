<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrangTuaSiswa extends Model
{
    protected $fillable = [
        'siswa_id', 'nama_ayah', 'nama_ibu', 'alamat_ortu',
        'no_telp_ortu', 'pekerjaan_ayah', 'pekerjaan_ibu',
        'nama_wali', 'alamat_wali', 'no_telp_wali', 'pekerjaan_wali',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }
}
