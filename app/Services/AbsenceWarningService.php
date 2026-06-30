<?php

namespace App\Services;

use App\Models\Absensi;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;

class AbsenceWarningService
{
    public const Threshold = 3;

    /**
     * @return array{0: CarbonImmutable, 1: CarbonImmutable}
     */
    public function currentSemesterRange(): array
    {
        $today = CarbonImmutable::now();
        $startMonth = $today->month <= 6 ? 1 : 7;
        $endMonth = $today->month <= 6 ? 6 : 12;

        return [
            $today->setMonth($startMonth)->startOfMonth(),
            $today->setMonth($endMonth)->endOfMonth(),
        ];
    }

    /**
     * @param  Collection<int, int>|array<int, int>  $studentIds
     * @return Collection<int, int>
     */
    public function alphaCountsForStudentIds(Collection|array $studentIds): Collection
    {
        $ids = collect($studentIds)->filter()->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        [$startDate, $endDate] = $this->currentSemesterRange();

        return Absensi::query()
            ->whereIn('profil_siswa_id', $ids)
            ->where('status', 'alpha')
            ->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('profil_siswa_id, count(*) as alpha_count')
            ->groupBy('profil_siswa_id')
            ->pluck('alpha_count', 'profil_siswa_id')
            ->map(fn (int|string $count): int => (int) $count);
    }

    public function alphaCountForStudentId(int $studentId): int
    {
        return $this->alphaCountsForStudentIds([$studentId])->get($studentId, 0);
    }

    public function hasWarning(int $alphaCount): bool
    {
        return $alphaCount >= self::Threshold;
    }
}
