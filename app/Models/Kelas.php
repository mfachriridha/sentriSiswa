<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nama', 'tingkat', 'wali_kelas_id', 'jumlah_siswa'])]
class Kelas extends Model
{
    protected $table = 'kelas';

    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'wali_kelas_id');
    }

    public function siswa(): HasMany
    {
        return $this->hasMany(ProfilSiswa::class, 'kelas_id');
    }
}
