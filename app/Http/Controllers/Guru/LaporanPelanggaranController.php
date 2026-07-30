<?php

namespace App\Http\Controllers\Guru;

use App\Exports\LaporanPelanggaranExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\ViolationReportFilterRequest;
use App\Models\JenisPelanggaran;
use App\Models\Kelas;
use App\Models\PelanggaranSiswa;
use App\Models\PengajuanPoin;
use App\Models\Pengguna;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LaporanPelanggaranController extends Controller
{
    public function index(ViolationReportFilterRequest $request): View
    {
        [$violations, $filters, $classes] = $this->reportData($request);
        $user = Auth::user()->loadMissing('profilGuru');
        $pengajuanPoin = $this->approvedPengajuanPoinQuery($filters, $user)
            ->paginate(15, ['*'], 'pengajuan_page')
            ->withQueryString();
        $categoryLabels = JenisPelanggaran::categoryLabels();
        $routeName = $user->isBk() ? 'bk.laporan' : 'kesiswaan.laporan';
        $title = $user->isBk() ? 'Laporan BK' : 'Laporan Kesiswaan';

        return view('kesiswaan.laporan-pelanggaran.index', compact('violations', 'filters', 'classes', 'categoryLabels', 'pengajuanPoin', 'routeName', 'title'));
    }

    public function exportExcel(ViolationReportFilterRequest $request): BinaryFileResponse|RedirectResponse
    {
        [$violations, $filters] = $this->reportData($request, paginated: false);
        $user = Auth::user()->loadMissing('profilGuru');

        // Berkas kosong tidak menolong siapa pun: penggunanya mengira ekspornya
        // berhasil, lalu bingung membuka berkas yang cuma berisi judul kolom. Lebih
        // baik ia tetap di halamannya dan tahu penyaringnya yang perlu dibetulkan.
        if ($violations->isEmpty()) {
            $routeName = $user->isBk() ? 'bk.laporan' : 'kesiswaan.laporan';

            return redirect()
                ->route($routeName.'.index', $request->query())
                ->with('error', 'Tidak ada pelanggaran yang cocok dengan penyaring ini, jadi tidak ada yang bisa diekspor.');
        }

        // Isi & susunan sheet ini sengaja disamakan dengan bagian di laporan cetak
        // PDF (Pelanggaran, Penambahan Poin) supaya keduanya nampilin informasi
        // yang sama, cuma beda format berkas.
        $violationRows = $violations->map(fn (PelanggaranSiswa $violation): array => [
            $violation->tanggal_pelanggaran->format('Y-m-d'),
            $violation->profilSiswa?->nis ?? '-',
            $violation->profilSiswa?->pengguna?->nama ?? '-',
            $violation->profilSiswa?->kelas?->nama ?? '-',
            $violation->nama_pelanggaran,
            JenisPelanggaran::categoryLabels()[$violation->kategori_pelanggaran] ?? $violation->kategori_pelanggaran,
            '-'.$violation->pengurangan_poin,
            $violation->dicatatOleh?->nama ?? '-',
        ])->values()->all();

        $pointAdditionRows = $this->approvedPengajuanPoinQuery($filters, $user)->get()
            ->map(fn (PengajuanPoin $pengajuan): array => [
                $pengajuan->disetujui_pada?->format('Y-m-d') ?? '-',
                $pengajuan->profilSiswa?->nis ?? '-',
                $pengajuan->profilSiswa?->pengguna?->nama ?? '-',
                $pengajuan->profilSiswa?->kelas?->nama ?? '-',
                $pengajuan->alasan,
                $pengajuan->jumlah_poin,
            ])->values()->all();

        $periode = ($filters['mulai'] ?? null) && ($filters['selesai'] ?? null)
            ? "{$filters['mulai']}-sampai-{$filters['selesai']}"
            : 'semua-tanggal';

        return Excel::download(
            new LaporanPelanggaranExport($violationRows, $pointAdditionRows),
            "laporan-pelanggaran-{$periode}.xlsx",
        );
    }

    /**
     * Halaman cetak laporan. Peramban yang mencetaknya, dan penggunanya memilih
     * "Simpan sebagai PDF" di dialog cetak.
     */
    public function cetak(ViolationReportFilterRequest $request): View
    {
        [$violations, $filters] = $this->reportData($request, paginated: false);
        $user = Auth::user()->loadMissing('profilGuru');

        return view('cetak.laporan-pelanggaran', [
            'judul' => $user->isBk() ? 'Laporan BK' : 'Laporan Kesiswaan',
            'violations' => $violations,
            'filters' => $filters,
            'pengajuanPoin' => $this->approvedPengajuanPoinQuery($filters, $user)->get(),
            'categoryLabels' => JenisPelanggaran::categoryLabels(),
        ]);
    }

    private function reportData(ViolationReportFilterRequest $request, bool $paginated = true): array
    {
        $filters = $request->validated();
        $user = Auth::user()->loadMissing('profilGuru');

        $query = PelanggaranSiswa::with(['profilSiswa.pengguna', 'profilSiswa.kelas', 'dicatatOleh', 'disetujuiOleh'])
            ->when($filters['mulai'] ?? null, fn ($query, $date) => $query->whereDate('tanggal_pelanggaran', '>=', $date))
            ->when($filters['selesai'] ?? null, fn ($query, $date) => $query->whereDate('tanggal_pelanggaran', '<=', $date))
            ->when($filters['kelas_id'] ?? null, fn ($query, $classId) => $query->whereHas('profilSiswa', fn ($studentQuery) => $studentQuery->where('kelas_id', $classId)))
            ->when($filters['tingkat'] ?? null, fn ($query, $grade) => $query->whereHas('profilSiswa.kelas', fn ($classQuery) => $classQuery->where('tingkat', $grade)))
            ->when($filters['kategori'] ?? null, fn ($query, $kategori) => $query->where('kategori_pelanggaran', $kategori));

        if ($user->isBk()) {
            $query->whereHas('profilSiswa.kelas', fn (Builder $classQuery) => $classQuery->where('tingkat', $user->profilGuru?->tingkat));
            $classes = Kelas::where('tingkat', $user->profilGuru?->tingkat)->orderBy('nama')->get();
            $filters['tingkat'] = $user->profilGuru?->tingkat;
        } else {
            $classes = Kelas::orderBy('tingkat')->orderBy('nama')->get();
        }

        $query->latest('tanggal_pelanggaran');

        return [
            $paginated ? $query->paginate(25)->withQueryString() : $query->get(),
            $filters,
            $classes,
        ];
    }

    /**
     * @return Builder<PengajuanPoin>
     */
    private function approvedPengajuanPoinQuery(array $filters, Pengguna $user): Builder
    {
        $query = PengajuanPoin::with(['profilSiswa.pengguna', 'profilSiswa.kelas', 'diajukanOleh', 'disetujuiOleh'])
            ->where('status', 'approved')
            ->when($filters['mulai'] ?? null, fn ($query, $date) => $query->whereDate('disetujui_pada', '>=', $date))
            ->when($filters['selesai'] ?? null, fn ($query, $date) => $query->whereDate('disetujui_pada', '<=', $date))
            ->when($filters['kelas_id'] ?? null, fn ($query, $classId) => $query->whereHas('profilSiswa', fn ($studentQuery) => $studentQuery->where('kelas_id', $classId)))
            ->when($filters['tingkat'] ?? null, fn ($query, $grade) => $query->whereHas('profilSiswa.kelas', fn ($classQuery) => $classQuery->where('tingkat', $grade)));

        if ($user->isBk()) {
            $query->whereHas('profilSiswa.kelas', fn (Builder $classQuery) => $classQuery->where('tingkat', $user->profilGuru?->tingkat));
        }

        return $query->latest('disetujui_pada');
    }
}
