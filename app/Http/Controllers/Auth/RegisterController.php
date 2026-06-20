<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SimpanRegistrasiRequest;
use App\Http\Requests\Auth\VerifikasiIdentitasRequest;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class RegisterController extends Controller
{
    /**
     * Tampilkan form daftar step 1 (input NISN/NIP).
     */
    public function create(): Response
    {
        return Inertia::render('auth/Daftar');
    }

    /**
     * Verifikasi identitas (NISN/NIP) — step 1.
     */
    public function verify(VerifikasiIdentitasRequest $request): RedirectResponse|Response
    {
        $peran = $request->peran;
        $identitas = $request->identitas;

        if ($peran === 'guru') {
            $pengguna = User::where('peran', '!=', 'siswa')
                ->where(function ($query) use ($identitas) {
                    $query->where('email', $identitas)
                        ->orWhere('nama', $identitas);
                })
                ->first();

            if (! $pengguna) {
                return back()->withErrors(['identitas' => 'Identitas guru tidak ditemukan.'])->onlyInput('identitas', 'peran');
            }

            if ($pengguna->isTerdaftar()) {
                return back()->withErrors(['identitas' => 'Akun sudah terdaftar. Silakan masuk.'])->onlyInput('identitas', 'peran');
            }

            session([
                'daftar_peran' => $peran,
                'daftar_pengguna_id' => $pengguna->id,
                'daftar_identitas' => $identitas,
                'daftar_nama' => $pengguna->nama,
            ]);

            return Inertia::render('auth/DaftarLengkapi', [
                'peran' => $peran,
                'identitas' => $identitas,
                'nama' => $pengguna->nama,
            ]);
        }

        $siswa = Siswa::where('nisn', $identitas)
            ->orWhere('nis', $identitas)
            ->first();

        if (! $siswa) {
            return back()->withErrors(['identitas' => 'NISN/NIS tidak ditemukan.'])->onlyInput('identitas', 'peran');
        }

        if ($siswa->pengguna && $siswa->pengguna->isTerdaftar()) {
            return back()->withErrors(['identitas' => 'NISN/NIS sudah terdaftar. Silakan masuk.'])->onlyInput('identitas', 'peran');
        }

        $nama = $siswa->pengguna?->nama ?? '';

        session([
            'daftar_peran' => $peran,
            'daftar_pengguna_id' => $siswa->pengguna_id,
            'daftar_identitas' => $identitas,
            'daftar_nama' => $nama,
        ]);

        return Inertia::render('auth/DaftarLengkapi', [
            'peran' => $peran,
            'identitas' => $identitas,
            'nama' => $nama,
        ]);
    }

    /**
     * Tampilkan form daftar step 2 (email + password).
     */
    public function showForm(): RedirectResponse|Response
    {
        $peran = session('daftar_peran');
        $identitas = session('daftar_identitas');
        $nama = session('daftar_nama');

        if (! $peran || ! $identitas) {
            return redirect()->route('daftar');
        }

        return Inertia::render('auth/DaftarLengkapi', [
            'peran' => $peran,
            'identitas' => $identitas,
            'nama' => $nama,
        ]);
    }

    /**
     * Simpan registrasi — step 2.
     */
    public function store(SimpanRegistrasiRequest $request): RedirectResponse
    {
        $peran = session('daftar_peran');
        $penggunaId = session('daftar_pengguna_id');

        if (! $peran || ! $penggunaId) {
            return redirect()->route('daftar')->withErrors(['identitas' => 'Sesi verifikasi kedaluwarsa. Silakan ulangi.']);
        }

        $pengguna = User::find($penggunaId);

        if (! $pengguna || $pengguna->isTerdaftar()) {
            return redirect()->route('daftar')->withErrors(['identitas' => 'Akun sudah terdaftar atau tidak valid.']);
        }

        $pengguna->update([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => User::STATUS_TERDAFTAR,
        ]);

        session()->forget(['daftar_peran', 'daftar_pengguna_id', 'daftar_identitas', 'daftar_nama']);

        Auth::login($pengguna);

        return redirect()->route('dashboard');
    }
}
