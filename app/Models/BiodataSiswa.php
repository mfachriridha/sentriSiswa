<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BiodataSiswa extends Model
{
    protected $table = 'biodata_siswa';

    const CREATED_AT = 'dibuat_pada';

    const UPDATED_AT = 'diperbarui_pada';

    protected $fillable = [
        'profil_siswa_id',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'status_keluarga',
        'anak_ke',
        'asal_sekolah',
        'tanggal_masuk',
        'nama_ayah',
        'pekerjaan_ayah',
        'nama_ibu',
        'pekerjaan_ibu',
        'alamat_ortu',
        'telepon_ortu',
        'nama_wali',
        'pekerjaan_wali',
        'alamat_wali',
        'telepon_wali',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
        'anak_ke' => 'integer',
    ];

    public function profilSiswa(): BelongsTo
    {
        return $this->belongsTo(ProfilSiswa::class, 'profil_siswa_id');
    }

    public function lengkap(): bool
    {
        return filled($this->tempat_lahir)
            && filled($this->tanggal_lahir)
            && filled($this->jenis_kelamin)
            && filled($this->agama);
    }
}
