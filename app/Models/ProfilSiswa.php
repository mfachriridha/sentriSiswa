<?php

namespace App\Models;

use Database\Factories\ProfilSiswaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nisn', 'nis', 'jenis_kelamin', 'kelas_id', 'telepon', 'alamat', 'foto'])]
class ProfilSiswa extends Model
{
    /** @use HasFactory<ProfilSiswaFactory> */
    use HasFactory;

    protected $table = 'profil_siswa';

    const CREATED_AT = 'dibuat_pada';

    const UPDATED_AT = 'diperbarui_pada';

    protected $primaryKey = 'nisn';

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @return array<string, string>
     */
    public static function labelJenisKelamin(): array
    {
        return [
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
        ];
    }

    /** Jenis kelamin siap tampil; tanda hubung kalau memang belum terisi. */
    public function getLabelJenisKelaminAttribute(): string
    {
        return self::labelJenisKelamin()[$this->jenis_kelamin] ?? '-';
    }

    public function pengguna(): BelongsTo
    {
        return $this->belongsTo(Pengguna::class, 'pengguna_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class, 'profil_siswa_id');
    }

    public function pelanggaranSiswa(): HasMany
    {
        return $this->hasMany(PelanggaranSiswa::class, 'profil_siswa_id');
    }

    public function pengajuanPoin(): HasMany
    {
        return $this->hasMany(PengajuanPoin::class, 'profil_siswa_id');
    }

    public function getPoinAttribute(): int
    {
        $deductions = 0;

        if (array_key_exists('pelanggaran_siswa_sum_pengurangan_poin', $this->attributes)) {
            $deductions = (int) $this->attributes['pelanggaran_siswa_sum_pengurangan_poin'];
        } elseif ($this->relationLoaded('pelanggaranSiswa')) {
            $deductions = $this->pelanggaranSiswa->where('status', 'approved')->sum('pengurangan_poin');
        } else {
            $deductions = (int) $this->pelanggaranSiswa()->disetujui()->sum('pengurangan_poin');
        }

        $additions = 0;

        if (array_key_exists('pengajuan_poin_sum_jumlah_poin', $this->attributes)) {
            $additions = (int) $this->attributes['pengajuan_poin_sum_jumlah_poin'];
        } elseif ($this->relationLoaded('pengajuanPoin')) {
            $additions = $this->pengajuanPoin->where('status', 'approved')->sum('jumlah_poin');
        } else {
            $additions = (int) $this->pengajuanPoin()->disetujui()->sum('jumlah_poin');
        }

        return max(0, min(100, 100 - $deductions + $additions));
    }
}
