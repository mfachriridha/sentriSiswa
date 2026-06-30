<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\UpdateProfilRequest;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function gantiSandiForm(): View
    {
        $user = Auth::user();
        $maskedEmail = $this->maskEmail($user->email ?? '');

        return view('guru.profil-ganti-sandi', compact('maskedEmail'));
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
            return redirect()->route(Auth::user()->profilRouteName());
        }

        return view('guru.profil-set-sandi-baru');
    }

    public function setSandiBaru(Request $request): RedirectResponse
    {
        if (! session('password_change_verified')) {
            return redirect()->route(Auth::user()->profilRouteName());
        }

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'regex:/[a-z]/i', 'regex:/[0-9]/', 'confirmed'],
        ], [
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.regex' => 'Kata sandi harus memuat huruf dan angka.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sesuai.',
        ]);

        $user = Auth::user();
        $user->update(['password' => \Illuminate\Support\Facades\Hash::make($request->password)]);
        session()->forget('password_change_verified');

        return redirect()->route($user->profilRouteName())
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
