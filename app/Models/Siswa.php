<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Siswa extends Model
{
    protected $fillable = [
        'user_id', 'kelas_id', 'nis', 'nisn', 'nama',
        'jenis_kelamin', 'no_hp', 'poin',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function biodata(): HasOne
    {
        return $this->hasOne(BiodataSiswa::class);
    }

    public function orangTua(): HasOne
    {
        return $this->hasOne(OrangTuaSiswa::class);
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class, 'user_id');
    }

    public function pelanggaran(): HasMany
    {
        return $this->hasMany(PelanggaranSiswa::class, 'user_id');
    }
}
