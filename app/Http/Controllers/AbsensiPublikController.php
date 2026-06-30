<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\ProfilSiswa;
use App\Models\TokenAksesAbsensi;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AbsensiPublikController extends Controller
{
    public function show(string $token): View
    {
        $aksesToken = TokenAksesAbsensi::where('token', $token)->first();

        if (! $aksesToken || $aksesToken->sudahExpired()) {
            return view('publik.absensi.expired');
        }

        return view('publik.absensi.verifikasi', compact('token'));
    }

    public function cek(Request $request, string $token)
    {
        $aksesToken = TokenAksesAbsensi::where('token', $token)->first();

        if (! $aksesToken || $aksesToken->sudahExpired()) {
            return view('publik.absensi.expired');
        }

        $request->validate([
            'nis_nisn' => ['required', 'string'],
            'nama' => ['required', 'string'],
        ]);

        $siswa = ProfilSiswa::where('kelas_id', $aksesToken->class_id)
            ->where(function ($q) use ($request) {
                $q->where('nis', $request->nis_nisn)
                  ->orWhere('nisn', $request->nis_nisn);
            })
            ->whereHas('pengguna', fn ($q) => $q->whereRaw('LOWER(nama) = ?', [strtolower($request->nama)]))
            ->first();

        if (! $siswa) {
            return back()->withErrors(['nis_nisn' => 'Data siswa tidak ditemukan. Periksa NIS/NISN dan nama lengkap.'])->withInput();
        }

        $absensi = Absensi::where('profil_siswa_id', $siswa->id)
            ->where('tanggal', $aksesToken->tanggal)
            ->first();

        return view('publik.absensi.hasil', compact('siswa', 'absensi', 'aksesToken'));
    }
}
