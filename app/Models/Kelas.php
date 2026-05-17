<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Kelas extends Model
{
    protected $fillable = ['nama', 'tingkat', 'jurusan'];

    public function siswa(): HasMany
    {
        return $this->hasMany(Siswa::class);
    }

    public function waliKelas(): HasOne
    {
        return $this->hasOne(WaliKelas::class);
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }
}
