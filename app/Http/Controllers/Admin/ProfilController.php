<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateProfilAdminRequest;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
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

        // Update non-critical fields first
        $admin->whatsapp_number = $data['whatsapp_number'] ?? null;

        if ($request->hasFile('photo')) {
            if ($admin->photo) {
                Storage::disk('public')->delete($admin->photo);
            }

            $admin->photo = $request->file('photo')->store('photos/admins', 'public');
        }

        $admin->save();

        // Intercept critical changes
        $emailChanged = $data['email'] !== $admin->email;
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

            app(OtpService::class)->generate($admin, $otpType, $pending);

            return redirect()->route('otp.show')
                ->with('success', 'Kode OTP telah dikirim ke email Anda saat ini untuk memverifikasi perubahan.');
        }

        return redirect()->route('admin.profil')->with('success', 'Profil admin berhasil diperbarui.');
    }
}
