<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateProfilAdminRequest;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function show(): View
    {
        return view('admin.profil', [
            'admin' => Auth::user(),
        ]);
    }

    public function edit(): View
    {
        return view('admin.profil-edit', [
            'admin' => Auth::user(),
        ]);
    }

    public function update(UpdateProfilAdminRequest $request): RedirectResponse
    {
        $admin = $request->user();
        $data = $request->validated();

        $admin->nama = $data['nama'];
        $admin->nomor_wa = $data['whatsapp_number'] ?? null;

        if ($request->has('delete_photo') && filter_var($request->input('delete_photo'), FILTER_VALIDATE_BOOLEAN)) {
            if ($admin->foto) {
                Storage::disk('public')->delete($admin->foto);
                $admin->foto = null;
            }
        } elseif ($request->hasFile('photo')) {
            if ($admin->foto) {
                Storage::disk('public')->delete($admin->foto);
            }

            $admin->foto = $request->file('photo')->store('photos/admins', 'public');
        }

        $admin->save();

        // Admin email change is direct (no OTP) so admin can always fix a seeder email
        if (isset($data['email']) && $data['email'] !== $admin->email) {
            $admin->email = $data['email'];
            $admin->email_verified_at = now();
            $admin->save();
        }

        return redirect()->route('admin.profil')->with('success', 'Profil admin berhasil diperbarui.');
    }

    public function gantiSandiForm(): View
    {
        $user = Auth::user();
        $maskedEmail = $this->maskEmail($user->email ?? '');

        return view('admin.profil-ganti-sandi', compact('maskedEmail'));
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
            return redirect()->route('admin.profil');
        }

        return view('admin.profil-set-sandi-baru');
    }

    public function setSandiBaru(Request $request): RedirectResponse
    {
        if (! session('password_change_verified')) {
            return redirect()->route('admin.profil');
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

        return redirect()->route('admin.profil')
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
}
