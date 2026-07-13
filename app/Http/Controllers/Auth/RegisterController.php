<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\VerifyIdentityRequest;
use App\Models\Pengguna;
use App\Models\ProfilGuru;
use App\Models\ProfilSiswa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function verify(VerifyIdentityRequest $request): RedirectResponse
    {
        $role = $request->peran;
        $identity = $request->identity;

        if ($role === 'teacher') {
            $profile = ProfilGuru::where('nip', $identity)->first();
            if (! $profile) {
                return $this->kembaliKeVerifikasi('NIP tidak ditemukan.');
            }

            if ($profile->pengguna && $profile->pengguna->isRegistered()) {
                return $this->kembaliKeVerifikasi('NIP sudah terdaftar. Silakan masuk.');
            }
        } else {
            $profile = ProfilSiswa::where('nisn', $identity)
                ->orWhere('nis', $identity)
                ->first();

            if (! $profile) {
                return $this->kembaliKeVerifikasi('NISN/NIS tidak ditemukan.');
            }

            if ($profile->pengguna && $profile->pengguna->isRegistered()) {
                return $this->kembaliKeVerifikasi('NISN/NIS sudah terdaftar. Silakan masuk.');
            }
        }

        session([
            'register_role' => $role,
            'register_user_id' => $profile->pengguna_id,
            'register_identity' => $identity,
            'register_name' => $profile->pengguna?->nama ?? '',
        ]);

        // Diarahkan, bukan langsung dirender: kalau halaman langkah 2 tampil dari
        // POST ini, alamat "halaman sebelumnya" milik peramban ikut menunjuk ke
        // sini. Formulir langkah 2 yang gagal validasi akan dilempar balik ke situ
        // - dan alamat itu cuma mengarahkan ulang ke langkah 1, sehingga pesan
        // galatnya hilang dan formulirnya kosong lagi.
        return redirect()->route('register.step2');
    }

    /**
     * Kembali ke formulir langkah 1 beserta alasannya. Peran yang tadi dipilih ikut
     * dibawa; tanpa itu, guru yang salah ketik NIP dilempar balik dengan pilihan
     * yang sudah berubah sendiri jadi Siswa.
     */
    private function kembaliKeVerifikasi(string $pesan): RedirectResponse
    {
        return back()
            ->withErrors(['identity' => $pesan])
            ->onlyInput('identity', 'peran');
    }

    public function showForm(): RedirectResponse|View
    {
        $role = session('register_role');
        $identity = session('register_identity');
        $name = session('register_name');

        if (! $role || ! $identity) {
            return redirect()->route('register');
        }

        return view('auth.register-step2', compact('role', 'identity', 'name'));
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $role = session('register_role');
        $userId = session('register_user_id');

        if (! $role || ! $userId) {
            return redirect()->route('register')->withErrors(['identity' => 'Sesi verifikasi kedaluwarsa. Silakan ulangi.']);
        }

        $user = Pengguna::find($userId);

        if (! $user || $user->isRegistered()) {
            return redirect()->route('register')->withErrors(['identity' => 'Akun sudah terdaftar atau tidak valid.']);
        }

        $user->update([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'registered',
        ]);

        if ($role === 'teacher') {
            $user->profilGuru?->update([
                'telepon' => $request->telepon,
            ]);
        }

        session()->forget(['register_role', 'register_user_id', 'register_identity', 'register_name']);

        return redirect()
            ->route('login')
            ->with('success', 'Pendaftaran berhasil. Silakan masuk dengan akun Anda.');
    }
}
