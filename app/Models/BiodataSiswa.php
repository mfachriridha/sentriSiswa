<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BiodataSiswa extends Model
{
    protected $fillable = [
        'siswa_id', 'foto', 'tempat_lahir', 'tanggal_lahir',
        'agama', 'status_keluarga', 'anak_ke', 'alamat',
        'no_telp_rumah', 'sekolah_asal', 'diterima_kelas', 'diterima_tanggal',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }
}
