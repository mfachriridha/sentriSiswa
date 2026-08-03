<?php

namespace App\Http\Controllers;

use App\Models\PelanggaranSiswa;
use App\Models\Pengaturan;
use App\Models\Presensi;
use App\Models\ProfilSiswa;
use App\Models\TokenAksesPresensi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PresensiPublikController extends Controller
{
    public function show(string $token): View
    {
        $aksesToken = TokenAksesPresensi::where('token', $token)->first();

        if (! $aksesToken || $aksesToken->sudahExpired()) {
            return view('publik.absensi.expired');
        }

        return view('publik.absensi.verifikasi', compact('token'));
    }

    public function cek(Request $request, string $token): View|RedirectResponse
    {
        $aksesToken = TokenAksesPresensi::where('token', $token)->first();

        if (! $aksesToken || $aksesToken->sudahExpired()) {
            return view('publik.absensi.expired');
        }

        $request->validate([
            'nisn' => ['required', 'string'],
        ], [
            'nisn.required' => 'NISN atau NIS wajib diisi.',
        ]);

        $query = $request->string('nisn')->trim()->value();

        $siswa = ProfilSiswa::with(['pengguna', 'kelas'])
            ->where('kelas_id', $aksesToken->kelas_id)
            ->where(function ($q) use ($query) {
                $q->where('nisn', $query)
                    ->orWhere('nis', $query);
            })
            ->first();

        if (! $siswa) {
            return back()
                ->withErrors(['nisn' => 'Data siswa tidak ditemukan. Periksa kembali NISN atau NIS-nya.'])
                ->withInput();
        }

        $absensi = Presensi::where('profil_siswa_id', $siswa->nisn)
            ->where('tanggal', $aksesToken->tanggal)
            ->first();

        [$startDate, $endDate] = Pengaturan::rentangTanggalPeriodeAktif();
        $alphaCount = Presensi::where('profil_siswa_id', $siswa->nisn)
            ->where('status', 'alpha')
            ->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])
            ->count();

        $maxAlpha = Pengaturan::batasMaksimalAlpha();
        $statusAlpha = Pengaturan::statusPeringatanAlpha($alphaCount);

        $pelanggaran = PelanggaranSiswa::query()
            ->where('profil_siswa_id', $siswa->nisn)
            ->disetujui()
            ->latest('tanggal_pelanggaran')
            ->get();

        return view('publik.absensi.hasil', [
            'siswa' => $siswa,
            'absensi' => $absensi,
            'aksesToken' => $aksesToken,
            'poin' => $siswa->poin,
            'alphaCount' => $alphaCount,
            'maxAlpha' => $maxAlpha,
            'sisaAlpha' => max(0, $maxAlpha - $alphaCount),
            'statusAlpha' => $statusAlpha,
            'pelanggaran' => $pelanggaran,
        ]);
    }
}
