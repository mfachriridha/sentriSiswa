<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\User as PenggunaGoogle;
use Laravel\Socialite\Facades\Socialite;

/**
 * Masuk, daftar, dan hubungkan akun lewat Google.
 *
 * Pencocokan akun selalu lewat id_google, tidak pernah lewat email. Google
 * memberi id yang tidak pernah berubah meski pemiliknya mengganti alamat Gmail,
 * sedangkan email Google boleh berbeda dari email akun di sekolah ini.
 *
 * Ketiga alurnya kembali ke satu alamat balik yang sama, jadi maksudnya (masuk,
 * daftar, atau hubungkan) dititipkan di sesi sebelum berangkat ke Google.
 */
class GoogleController extends Controller
{
    public function masuk(): RedirectResponse
    {
        session(['maksud_google' => 'masuk']);

        return Socialite::driver('google')->redirect();
    }

    /**
     * Pendaftaran lewat Google.
     *
     * Guru wajib mengisi nomor HP, sama seperti pendaftaran biasa. Karena itu
     * tombolnya mengirim formulir, bukan sekadar tautan: nomornya diperiksa dulu
     * di sini, disimpan di sesi, baru berangkat ke Google.
     */
    public function daftar(Request $request): RedirectResponse
    {
        if (! session('register_role') || ! session('register_user_id')) {
            return redirect()->route('register')
                ->withErrors(['identity' => 'Sesi pendaftaran kedaluwarsa. Silakan ulangi verifikasi identitas.']);
        }

        $telepon = null;

        if (session('register_role') === 'teacher') {
            $tervalidasi = $request->validate([
                'telepon' => ['required', 'string', 'min:10', 'max:15', 'regex:/^[0-9+\-\s()]*$/'],
            ], [
                'telepon.required' => 'Nomor HP wajib diisi.',
                'telepon.min' => 'Nomor HP minimal 10 digit.',
                'telepon.max' => 'Nomor HP maksimal 15 digit.',
                'telepon.regex' => 'Format nomor HP tidak valid.',
            ]);

            $telepon = $tervalidasi['telepon'];
        }

        session([
            'maksud_google' => 'daftar',
            'telepon_pendaftaran' => $telepon,
        ]);

        return Socialite::driver('google')->redirect();
    }

    public function hubungkan(): RedirectResponse
    {
        session([
            'maksud_google' => 'hubungkan',
            'pengguna_hubungkan_google' => Auth::id(),
        ]);

        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        $maksud = session('maksud_google', 'masuk');

        try {
            $penggunaGoogle = Socialite::driver('google')->user();
        } catch (\Throwable) {
            return $this->batalkan('Autentikasi Google gagal. Silakan coba lagi.', $maksud);
        }

        session()->forget('maksud_google');

        return match ($maksud) {
            'daftar' => $this->selesaikanPendaftaran($penggunaGoogle),
            'hubungkan' => $this->selesaikanPenghubungan($penggunaGoogle),
            default => $this->selesaikanMasuk($penggunaGoogle),
        };
    }

    public function putuskan(): RedirectResponse
    {
        /** @var Pengguna $pengguna */
        $pengguna = Auth::user();

        // Memutus Google pada akun yang belum punya kata sandi sama saja mengunci
        // pemiliknya di luar akunnya sendiri.
        if (! $pengguna->hasPassword()) {
            return redirect()->route($pengguna->profilRouteName())
                ->withErrors(['google' => 'Buat kata sandi dulu lewat Ganti Kata Sandi, baru akun Google bisa diputuskan.']);
        }

        $pengguna->update(['id_google' => null]);

        return redirect()->route($pengguna->profilRouteName())
            ->with('success', 'Akun Google berhasil diputuskan.');
    }

    private function selesaikanMasuk(PenggunaGoogle $penggunaGoogle): RedirectResponse
    {
        $pengguna = Pengguna::where('id_google', $penggunaGoogle->getId())->first();

        if ($pengguna && $pengguna->isRegistered()) {
            Auth::login($pengguna);
            session()->regenerate();

            return redirect()->route($pengguna->dashboardRouteName());
        }

        // Sengaja tidak menghubungkan otomatis walau emailnya sama: menghubungkan
        // akun harus keputusan sadar pemiliknya, dari halaman profilnya sendiri.
        $emailSudahDipakai = Pengguna::where('email', $penggunaGoogle->getEmail())
            ->where('status', 'registered')
            ->exists();

        if ($emailSudahDipakai) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Akun Anda belum terhubung ke Google. Masuk seperti biasa, lalu hubungkan dari halaman Profil.']);
        }

        return redirect()->route('register')
            ->withErrors(['identity' => 'Akun Google ini belum terdaftar di Sentri Siswa. Silakan daftar terlebih dahulu.']);
    }

    private function selesaikanPendaftaran(PenggunaGoogle $penggunaGoogle): RedirectResponse
    {
        $peran = session('register_role');
        $penggunaId = session('register_user_id');

        if (! $peran || ! $penggunaId) {
            return redirect()->route('register')
                ->withErrors(['identity' => 'Sesi pendaftaran kedaluwarsa. Silakan ulangi verifikasi identitas.']);
        }

        $pengguna = Pengguna::find($penggunaId);

        if (! $pengguna || $pengguna->isRegistered()) {
            return redirect()->route('register')
                ->withErrors(['identity' => 'Akun sudah terdaftar atau tidak valid.']);
        }

        if (Pengguna::where('id_google', $penggunaGoogle->getId())->exists()) {
            return redirect()->route('register.step2')
                ->withErrors(['email' => 'Akun Google ini sudah terhubung ke akun lain.']);
        }

        if (Pengguna::where('email', $penggunaGoogle->getEmail())->where('id', '!=', $pengguna->id)->exists()) {
            return redirect()->route('register.step2')
                ->withErrors(['email' => 'Email Google ini sudah digunakan oleh akun lain.']);
        }

        $pengguna->forceFill([
            'email' => $penggunaGoogle->getEmail(),
            'id_google' => $penggunaGoogle->getId(),
            'status' => 'registered',
            'email_verified_at' => now(),
            // Akun bawaan dari impor sudah membawa kata sandi awal. Kalau dibiarkan,
            // akun ini tetap bisa dimasuki siapa pun yang tahu kata sandi itu.
            // Yang mendaftar lewat Google memang tidak punya kata sandi sampai ia
            // membuatnya sendiri lewat Lupa Kata Sandi atau Ganti Kata Sandi.
            'password' => null,
        ])->save();

        if ($peran === 'teacher') {
            $pengguna->profilGuru?->update(['telepon' => session('telepon_pendaftaran')]);
        }

        session()->forget([
            'register_role', 'register_user_id', 'register_identity',
            'register_name', 'telepon_pendaftaran',
        ]);

        Auth::login($pengguna);
        session()->regenerate();

        return redirect()->route($pengguna->dashboardRouteName())
            ->with('success', 'Pendaftaran berhasil! Selamat datang di Sentri Siswa.');
    }

    private function selesaikanPenghubungan(PenggunaGoogle $penggunaGoogle): RedirectResponse
    {
        $pengguna = Auth::user() ?? Pengguna::find(session('pengguna_hubungkan_google'));
        session()->forget('pengguna_hubungkan_google');

        if (! $pengguna) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Sesi penghubungan Google kedaluwarsa. Masuk dulu, lalu coba lagi.']);
        }

        $dipakaiAkunLain = Pengguna::where('id_google', $penggunaGoogle->getId())
            ->where('id', '!=', $pengguna->id)
            ->exists();

        if ($dipakaiAkunLain) {
            return redirect()->route($pengguna->profilRouteName())
                ->withErrors(['google' => 'Akun Google ini sudah terhubung ke akun lain.']);
        }

        // Email Google sengaja boleh berbeda dari email akun: banyak orang memakai
        // email sekolah untuk akunnya, tetapi Gmail pribadi untuk Google-nya.
        $pengguna->update(['id_google' => $penggunaGoogle->getId()]);

        return redirect()->route($pengguna->profilRouteName())
            ->with('success', 'Akun berhasil dihubungkan ke Google.');
    }

    private function batalkan(string $pesan, string $maksud): RedirectResponse
    {
        session()->forget(['maksud_google', 'pengguna_hubungkan_google', 'telepon_pendaftaran']);

        if ($maksud === 'hubungkan' && Auth::check()) {
            return redirect()->route(Auth::user()->profilRouteName())
                ->withErrors(['google' => $pesan]);
        }

        return redirect()->route('login')->withErrors(['email' => $pesan]);
    }
}
