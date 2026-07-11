<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

#[Fillable(['kunci', 'nilai'])]
class Pengaturan extends Model
{
    /** Senin-Jumat, dipakai kalau admin belum pernah nyimpen konfigurasi. */
    public const DEFAULT_HARI_ABSEN = [1, 2, 3, 4, 5];

    protected $table = 'pengaturan';

    const CREATED_AT = 'dibuat_pada';

    const UPDATED_AT = 'diperbarui_pada';

    public static function get(string $kunci, mixed $default = null): mixed
    {
        return cache()->remember("pengaturan:{$kunci}", now()->addDay(), fn () => static::where('kunci', $kunci)->value('nilai')) ?? $default;
    }

    public static function set(string $kunci, mixed $nilai): void
    {
        static::updateOrCreate(['kunci' => $kunci], ['nilai' => $nilai]);
        cache()->forget("pengaturan:{$kunci}");
    }

    /**
     * Hari aktif absensi sebagai angka ISO-8601 (1 = Senin ... 7 = Minggu).
     *
     * @return list<int>
     */
    public static function hariAbsen(): array
    {
        $raw = static::get('attendance_active_days');

        if (! is_string($raw) || blank($raw)) {
            return self::DEFAULT_HARI_ABSEN;
        }

        $days = collect(explode(',', $raw))
            ->map(fn (string $day): int => (int) trim($day))
            ->filter(fn (int $day): bool => $day >= 1 && $day <= 7)
            ->unique()
            ->sort()
            ->values()
            ->all();

        return $days === [] ? self::DEFAULT_HARI_ABSEN : $days;
    }

    public static function hariAbsenAktif(?Carbon $date = null): bool
    {
        return in_array(($date ?? now())->dayOfWeekIso, static::hariAbsen(), true);
    }

    /** @return array<int, string> */
    public static function namaHari(): array
    {
        return [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];
    }

    /** Contoh: "Senin, Selasa, Rabu, Kamis, dan Jumat". */
    public static function labelHariAbsen(): string
    {
        $names = array_map(fn (int $day): string => static::namaHari()[$day], static::hariAbsen());

        if (count($names) === 1) {
            return $names[0];
        }

        $last = array_pop($names);

        return implode(', ', $names).' dan '.$last;
    }
}
