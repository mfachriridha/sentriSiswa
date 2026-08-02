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

    /**
     * Rentang tanggal periode aktif [mulai, selesai].
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    public static function rentangTanggalPeriodeAktif(): array
    {
        $startDateRaw = static::get('period_start_date');
        $endDateRaw = static::get('period_end_date');

        try {
            $start = $startDateRaw ? Carbon::parse($startDateRaw)->startOfDay() : today()->subMonths(6)->startOfDay();
            $end = $endDateRaw ? Carbon::parse($endDateRaw)->endOfDay() : today()->endOfDay();
        } catch (\Throwable) {
            $start = today()->subMonths(6)->startOfDay();
            $end = today()->endOfDay();
        }

        if ($end->lessThan($start)) {
            $temp = $start;
            $start = $end->copy()->startOfDay();
            $end = $temp->copy()->endOfDay();
        }

        return [$start, $end];
    }

    public static function batasMaksimalAlpha(): int
    {
        $val = static::get('max_alpha_limit', 6);

        return is_numeric($val) && (int) $val > 0 ? (int) $val : 6;
    }

    /**
     * Ambang batas peringatan alpha: sp1, sp2, wakasis.
     *
     * @return array{sp1: int, sp2: int, wakasis: int}
     */
    public static function ambangPeringatanAlpha(): array
    {
        return [
            'sp1' => (int) static::get('alpha_sp1_threshold', 3),
            'sp2' => (int) static::get('alpha_sp2_threshold', 4),
            'wakasis' => (int) static::get('alpha_wakasis_threshold', 6),
        ];
    }

    /**
     * Status & info peringatan alpha untuk seorang siswa berdasarkan jumlah alpha-nya.
     *
     * @return array{kode: string, label: string, warna: string, sisa: int}
     */
    public static function statusPeringatanAlpha(int $jumlahAlpha): array
    {
        $max = static::batasMaksimalAlpha();
        $ambang = static::ambangPeringatanAlpha();
        $sisa = max(0, $max - $jumlahAlpha);

        if ($jumlahAlpha >= $ambang['wakasis']) {
            return [
                'kode' => 'wakasis',
                'label' => 'Batas Wakasis (Dikembalikan)',
                'warna' => 'red',
                'sisa' => $sisa,
            ];
        }

        if ($jumlahAlpha >= $ambang['sp2']) {
            return [
                'kode' => 'sp2',
                'label' => 'SP 2 (Guru BK)',
                'warna' => 'rose',
                'sisa' => $sisa,
            ];
        }

        if ($jumlahAlpha >= $ambang['sp1']) {
            return [
                'kode' => 'sp1',
                'label' => 'SP 1 (Wali Kelas & BK)',
                'warna' => 'amber',
                'sisa' => $sisa,
            ];
        }

        if ($jumlahAlpha >= 1) {
            return [
                'kode' => 'wali_kelas',
                'label' => 'Perhatian Wali Kelas',
                'warna' => 'yellow',
                'sisa' => $sisa,
            ];
        }

        return [
            'kode' => 'normal',
            'label' => 'Aman',
            'warna' => 'emerald',
            'sisa' => $sisa,
        ];
    }
}
