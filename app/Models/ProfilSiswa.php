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

    public function presensi(): HasMany
    {
        return $this->hasMany(Presensi::class, 'profil_siswa_id');
    }

    public function absensi(): HasMany
    {
        return $this->presensi();
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
        $violations = $this->relationLoaded('pelanggaranSiswa')
            ? $this->pelanggaranSiswa->where('status', 'approved')
            : $this->pelanggaranSiswa()->disetujui()->get();

        $additions = $this->relationLoaded('pengajuanPoin')
            ? $this->pengajuanPoin->where('status', 'approved')
            : $this->pengajuanPoin()->disetujui()->get();

        $events = collect();

        foreach ($violations as $v) {
            $ts = $v->disetujui_pada?->timestamp
                ?? ($v->tanggal_pelanggaran ? $v->tanggal_pelanggaran->timestamp : ($v->dibuat_pada?->timestamp ?? 0));

            $events->push([
                'timestamp' => $ts,
                'id' => $v->id ?? 0,
                'type' => 'deduction',
                'amount' => (int) $v->pengurangan_poin,
            ]);
        }

        foreach ($additions as $a) {
            $ts = $a->disetujui_pada?->timestamp ?? ($a->dibuat_pada?->timestamp ?? 0);

            $events->push([
                'timestamp' => $ts,
                'id' => $a->id ?? 0,
                'type' => 'addition',
                'amount' => (int) $a->jumlah_poin,
            ]);
        }

        $events = $events->sortBy(fn ($item) => [$item['timestamp'], $item['id']]);

        $poin = 100;
        foreach ($events as $event) {
            if ($event['type'] === 'deduction') {
                $poin = max(0, $poin - $event['amount']);
            } else {
                $poin = min(100, $poin + $event['amount']);
            }
        }

        return $poin;
    }
}
