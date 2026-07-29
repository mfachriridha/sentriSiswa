<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\Siswa\UpdateSiswaProfilRequest;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function poin(): View
    {
        $student = Auth::user();
        $student->loadMissing([
            'profilSiswa.pelanggaranSiswa' => function ($q) {
                $q->disetujui()->latest('tanggal_pelanggaran');
            },
            // Poin siswa tidak hanya berkurang: ia juga bertambah lewat pengajuan
            // wali kelas yang disetujui kesiswaan. Tanpa daftar ini, angka sisa
            // poinnya tidak cocok dengan riwayat yang dibacanya.
            'profilSiswa.pengajuanPoin' => function ($q) {
                $q->disetujui()->latest('disetujui_pada');
            },
        ]);

        $profile = $student->profilSiswa;
        $violations = $profile?->pelanggaranSiswa ?? collect();
        $additions = $profile?->pengajuanPoin ?? collect();
        $totalPoints = $profile?->poin ?? 100;
        $totalDeductions = $violations->sum('pengurangan_poin');
        $totalAdditions = $additions->sum('jumlah_poin');

        return view('siswa.poin', compact(
            'profile',
            'violations',
            'additions',
            'totalPoints',
            'totalDeductions',
            'totalAdditions',
        ));
    }

    public function show(): View
    {
        $student = Auth::user();
        $student->load(['profilSiswa.kelas']);

        return view('siswa.profil', compact('student'));
    }

    public function edit(): View
    {
        $student = Auth::user();
        $student->load('profilSiswa');

        return view('siswa.profil-edit', compact('student'));
    }

    public function update(UpdateSiswaProfilRequest $request): RedirectResponse
    {
        $student = Auth::user();
        $profile = $student->profilSiswa;

        if ($request->has('delete_photo') && filter_var($request->input('delete_photo'), FILTER_VALIDATE_BOOLEAN)) {
            if ($profile->foto) {
                Storage::disk('public')->delete($profile->foto);
                $profile->update(['foto' => null]);
            }
        } elseif ($request->hasFile('photo')) {
            if ($profile->foto) {
                Storage::disk('public')->delete($profile->foto);
            }
            $path = $request->file('photo')->store('photos/students', 'public');
            $profile->update(['foto' => $path]);
        }

        $profile->update([
            'telepon' => $request->telepon,
            'alamat' => $request->alamat,
        ]);

        $emailChanged = $request->email !== $student->email;
        $passwordChanged = $request->filled('password');

        if ($emailChanged || $passwordChanged) {
            $otpType = $emailChanged ? 'email_change' : 'password_change';

            $pending = [];
            if ($emailChanged) {
                $pending['new_email'] = $request->email;
            }
            if ($passwordChanged) {
                $pending['new_password'] = $request->password;
            }

            session([
                'otp_type' => $otpType,
                'otp_pending' => $pending,
            ]);

            app(OtpService::class)->generate($student, $otpType, $pending);

            return redirect()->route('otp.show')
                ->with('success', 'Kode OTP telah dikirim ke email Anda saat ini untuk memverifikasi perubahan.');
        }

        return redirect()->route('siswa.profil')->with('success', 'Profil berhasil diperbarui.');
    }

    public function gantiSandiForm(): View
    {
        $user = Auth::user();
        $maskedEmail = $this->maskEmail($user->email ?? '');

        return view('siswa.profil-ganti-sandi', compact('maskedEmail'));
    }

    public function gantiSandi(Request $request): RedirectResponse
    {
        $user = Auth::user();

        session(['otp_type' => 'password_change', 'otp_pending' => []]);
        app(OtpService::class)->generate($user, 'password_change', []);

        return redirect()->route('otp.show')
            ->with('success', 'Kode OTP telah dikirim ke email Anda.');
    }

    public function setSandiBaruForm(): View|RedirectResponse
    {
        if (! session('password_change_verified')) {
            return redirect()->route('siswa.profil');
        }

        return view('siswa.profil-set-sandi-baru');
    }

    public function setSandiBaru(Request $request): RedirectResponse
    {
        if (! session('password_change_verified')) {
            return redirect()->route('siswa.profil');
        }

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'regex:/[a-z]/i', 'regex:/[0-9]/', 'confirmed'],
        ], [
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.regex' => 'Kata sandi harus memuat huruf dan angka.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sesuai.',
        ]);

        $user = Auth::user();
        $user->update(['password' => Hash::make($request->password)]);
        session()->forget('password_change_verified');

        return redirect()->route('siswa.profil')
            ->with('success', 'Kata sandi berhasil diubah.');
    }

    private function maskEmail(string $email): string
    {
        if (! str_contains($email, '@')) {
            return $email;
        }
        [$local, $domain] = explode('@', $email);
        $masked = substr($local, 0, 2).str_repeat('*', max(0, strlen($local) - 2));

        return $masked.'@'.$domain;
    }

    public function uploadPhoto(): RedirectResponse
    {
        request()->validate([
            'photo' => ['required', 'image', 'max:2048'],
        ], [
            'photo.required' => 'Foto wajib dipilih.',
            'photo.image' => 'Foto harus berupa file gambar.',
            'photo.max' => 'Ukuran foto maksimal 2MB.',
        ]);

        $student = Auth::user();
        $profile = $student->profilSiswa;

        if ($profile->foto) {
            Storage::disk('public')->delete($profile->foto);
        }

        $path = request()->file('photo')->store('photos/students', 'public');

        $profile->update(['foto' => $path]);

        return redirect()->route('siswa.profil')->with('success', 'Foto berhasil diunggah.');
    }

    public function deletePhoto(): RedirectResponse
    {
        $student = Auth::user();
        $profile = $student->profilSiswa;

        if ($profile->foto) {
            Storage::disk('public')->delete($profile->foto);
            $profile->update(['foto' => null]);
        }

        return redirect()->route('siswa.profil')->with('success', 'Foto berhasil dihapus.');
    }
}
