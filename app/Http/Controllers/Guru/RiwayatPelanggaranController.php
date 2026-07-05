<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\RiwayatPelanggaranFilterRequest;
use App\Models\PelanggaranSiswa;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RiwayatPelanggaranController extends Controller
{
    public function index(RiwayatPelanggaranFilterRequest $request): View
    {
        $class = Auth::user()->kelasWali;

        if (! $class) {
            return view('wali-kelas.pelanggaran.empty');
        }

        $violations = PelanggaranSiswa::with(['profilSiswa.pengguna', 'jenisPelanggaran', 'dicatatOleh'])
            ->whereHas('profilSiswa', fn ($q) => $q->where('kelas_id', $class->id))
            ->disetujui()
            ->when($request->profil_siswa_id, fn ($q, $id) => $q->where('profil_siswa_id', $id))
            ->when($request->kategori, fn ($q, $kategori) => $q->where('kategori_pelanggaran', $kategori))
            ->when($request->date_from, fn ($q, $date) => $q->whereDate('tanggal_pelanggaran', '>=', $date))
            ->when($request->date_to, fn ($q, $date) => $q->whereDate('tanggal_pelanggaran', '<=', $date))
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
