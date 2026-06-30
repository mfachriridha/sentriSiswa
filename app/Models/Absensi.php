<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['profil_siswa_id', 'tanggal', 'status', 'waktu_masuk', 'path_selfie', 'latitude', 'longitude', 'akurasi', 'jarak_meter'])]
class Absensi extends Model
{
    protected $table = 'absensi';

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'waktu_masuk' => 'datetime:H:i',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'akurasi' => 'float',
            'jarak_meter' => 'float',
        ];
    }

    public function profilSiswa(): BelongsTo
    {
        return $this->belongsTo(ProfilSiswa::class, 'profil_siswa_id');
    }
}
