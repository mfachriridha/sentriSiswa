<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\UpdateProfilRequest;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function show(): View
    {
        $teacher = Auth::user();
        $teacher->load(['profilGuru', 'kelasWali']);

        return view('guru.profil', compact('teacher'));
    }

    public function edit(): View
    {
        $teacher = Auth::user();
        $teacher->load(['profilGuru', 'kelasWali']);

        return view('guru.profil-edit', compact('teacher'));
    }

    public function update(UpdateProfilRequest $request): RedirectResponse
    {
        $teacher = Auth::user();
        $data = $request->validated();

        $teacher->update([
            'nama' => $data['nama'],
        ]);

        $profile = $teacher->profilGuru;
        if (! $profile) {
            $profile = $teacher->profilGuru()->create([]);
        }

        if ($request->has('delete_photo') && filter_var($request->input('delete_photo'), FILTER_VALIDATE_BOOLEAN)) {
            if ($profile->foto) {
                Storage::disk('public')->delete($profile->foto);
                $profile->update(['foto' => null]);
            }
        } elseif ($request->hasFile('photo')) {
            if ($profile->foto) {
                Storage::disk('public')->delete($profile->foto);
            }
            $path = $request->file('photo')->store('photos/teachers', 'public');
            $profile->update(['foto' => $path]);
        }

        $profile->update(['telepon' => $data['telepon'] ?? null]);

        $emailChanged = isset($data['email']) && $data['email'] !== $teacher->email;
        $passwordChanged = $request->filled('password');

        if ($emailChanged || $passwordChanged) {
            $otpType = $emailChanged ? 'email_change' : 'password_change';

            $pending = [];
            if ($emailChanged) {
                $pending['new_email'] = $data['email'];
            }
            if ($passwordChanged) {
                $pending['new_password'] = $data['password'];
            }

            session([
                'otp_type' => $otpType,
                'otp_pending' => $pending,
            ]);

            app(OtpService::class)->generate($teacher, $otpType, $pending);

            return redirect()->route('otp.show')
                ->with('success', 'Kode OTP telah dikirim ke email Anda saat ini untuk memverifikasi perubahan.');
        }

        return redirect()->route($teacher->profilRouteName())->with('success', 'Profil berhasil diperbarui.');
    }

    public function uploadPhoto(): RedirectResponse
    {
        request()->validate([
            'photo' => ['required', 'image', 'max:2048'],
        ]);

        $teacher = Auth::user();
        $profile = $teacher->profilGuru ?? $teacher->profilGuru()->create();

        if ($profile->foto) {
            Storage::disk('public')->delete($profile->foto);
        }

        $path = request()->file('photo')->store('photos/teachers', 'public');

        $profile->update(['foto' => $path]);

        return redirect()->route($teacher->profilRouteName())->with('success', 'Foto berhasil diunggah.');
    }

    public function deletePhoto(): RedirectResponse
    {
        $teacher = Auth::user();
        $profile = $teacher->profilGuru;

        if ($profile && $profile->foto) {
            Storage::disk('public')->delete($profile->foto);
            $profile->update(['foto' => null]);
        }

        return redirect()->route($teacher->profilRouteName())->with('success', 'Foto berhasil dihapus.');
    }
}
