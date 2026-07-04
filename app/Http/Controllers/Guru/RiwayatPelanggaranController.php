<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\PelanggaranSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RiwayatPelanggaranController extends Controller
{
    public function index(Request $request): View
    {
        $class = Auth::user()->kelasWali;

        if (! $class) {
            return view('wali-kelas.pelanggaran.empty');
        }

        $violations = PelanggaranSiswa::with(['profilSiswa.pengguna', 'jenisPelanggaran', 'dicatatOleh'])
            ->whereHas('profilSiswa', fn ($q) => $q->where('kelas_id', $class->id))
            ->approved()
            ->when($request->profil_siswa_id, fn ($q, $id) => $q->where('profil_siswa_id', $id))
            ->when($request->category, fn ($q, $cat) => $q->where('kategori_pelanggaran', $cat))
            ->when($request->date_from, fn ($q, $date) => $q->where('tanggal_pelanggaran', '>=', $date))
            ->when($request->date_to, fn ($q, $date) => $q->where('tanggal_pelanggaran', '<=', $date))
            ->latest('tanggal_pelanggaran')
            ->paginate(25)
            ->withQueryString();

        $students = $class->siswa()
            ->join('pengguna', 'profil_siswa.pengguna_id', '=', 'pengguna.id')
            ->with('pengguna')
            ->orderBy('pengguna.nama')
            ->select('profil_siswa.*')
            ->get();

        return view('wali-kelas.pelanggaran.index', compact('class', 'violations', 'students'));
    }
}
