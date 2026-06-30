<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\Siswa\UpdateSiswaProfilRequest;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function poin(): View
    {
        $student = Auth::user();
        $student->loadMissing(['profilSiswa.pelanggaranSiswa' => function ($q) {
            $q->approved()->latest('tanggal_pelanggaran');
        }]);

        $profile = $student->profilSiswa;
        $violations = $profile?->pelanggaranSiswa ?? collect();
        $totalPoints = $profile?->poin ?? 100;
        $totalDeductions = $violations->sum('pengurangan_poin');

        return view('siswa.poin', compact('profile', 'violations', 'totalPoints', 'totalDeductions'));
    }

    public function show(): View
    {
        $student = Auth::user();
        $student->load(['profilSiswa.kelas', 'profilSiswa.biodata']);

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

        $student->profilSiswa->update([
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

    public function uploadPhoto(): RedirectResponse
    {
        request()->validate([
            'photo' => ['required', 'image', 'max:2048'],
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
