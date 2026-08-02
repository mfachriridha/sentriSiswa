<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\PengajuanPoin\StorePengajuanPoinRequest;
use App\Models\KategoriPengajuanPoin;
use App\Models\PengajuanPoin;
use App\Models\ProfilSiswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PengajuanPoinController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->get('status', '');

        $pengajuanPoin = PengajuanPoin::with(['profilSiswa.pengguna', 'profilSiswa.kelas', 'disetujuiOleh', 'kategori'])
            ->where('diajukan_oleh_id', Auth::id())
            ->when($status, fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $statusLabels = PengajuanPoin::statusLabels();

        return view('wali-kelas.pengajuan-poin.index', compact('pengajuanPoin', 'status', 'statusLabels'));
    }

    public function create(): View
    {
        $students = $this->students();
        $categories = KategoriPengajuanPoin::orderBy('urutan')->get()->groupBy('grup');

        return view('wali-kelas.pengajuan-poin.create', compact('students', 'categories'));
    }

    public function store(StorePengajuanPoinRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $student = ProfilSiswa::findOrFail($data['profil_siswa_id']);
        $this->authorizeOwnClass($student);

        $category = KategoriPengajuanPoin::findOrFail($data['kategori_pengajuan_poin_id']);

        PengajuanPoin::create([
            'profil_siswa_id' => $student->nisn,
            'kategori_pengajuan_poin_id' => $category->id,
            'diajukan_oleh_id' => Auth::id(),
            'alasan' => $data['alasan'],
            'jumlah_poin' => $category->poin,
            'status' => 'pending',
        ]);

        return redirect()->route('wali-kelas.pengajuan-poin.index')->with('success', 'Pengajuan penambahan poin berhasil dikirim ke kesiswaan.');
    }

    /**
     * @return Collection<int, ProfilSiswa>
     */
    private function students(): Collection
    {
        $class = Auth::user()->kelasWali;

        if (! $class) {
            return collect();
        }

        return $class->siswa()
            ->with('pengguna')
            ->get()
            ->sortBy(fn (ProfilSiswa $profilSiswa) => $profilSiswa->pengguna?->nama ?? '')
            ->values();
    }

    private function authorizeOwnClass(ProfilSiswa $student): void
    {
        abort_unless($student->kelas_id === Auth::user()->kelasWali?->id, 403);
    }
}
