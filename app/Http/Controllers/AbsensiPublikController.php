<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\ProfilSiswa;
use App\Models\TokenAksesAbsensi;
use Illuminate\Http\RedirectResponse;
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

    public function cek(Request $request, string $token): View|RedirectResponse
    {
        $aksesToken = TokenAksesAbsensi::where('token', $token)->first();

        if (! $aksesToken || $aksesToken->sudahExpired()) {
            return view('publik.absensi.expired');
        }

        $request->validate([
            'nisn' => ['required', 'string'],
        ]);

        $siswa = ProfilSiswa::with(['pengguna', 'kelas'])
            ->where('kelas_id', $aksesToken->kelas_id)
            ->where('nisn', $request->string('nisn')->trim()->value())
            ->first();

        if (! $siswa) {
            return back()
                ->withErrors(['nisn' => 'Data siswa tidak ditemukan. Periksa kembali NISN-nya.'])
                ->withInput();
        }

        $absensi = Absensi::where('profil_siswa_id', $siswa->nisn)
            ->where('tanggal', $aksesToken->tanggal)
            ->first();

        return view('publik.absensi.hasil', [
            'siswa' => $siswa,
            'absensi' => $absensi,
            'aksesToken' => $aksesToken,
            'poin' => $siswa->poin,
        ]);
    }
}
