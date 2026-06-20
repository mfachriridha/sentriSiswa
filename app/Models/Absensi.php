<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Absensi extends Model
{
    protected $table = 'absensi';

    public const STATUS_HADIR = 'hadir';

    public const STATUS_TERLAMBAT = 'terlambat';

    public const STATUS_IZIN = 'izin';

    public const STATUS_SAKIT = 'sakit';

    public const STATUS_ALPHA = 'alpha';

    public const STATUS_BELUM_ABSEN = 'belum_absen';

    protected $fillable = [
        'siswa_id',
        'tanggal',
        'status',
        'waktu_masuk',
        'foto_selfie',
        'lintang',
        'bujur',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'waktu_masuk' => 'datetime:H:i',
        ];
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}
