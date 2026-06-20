<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateProfilAdminRequest;
use Illuminate\Http\RedirectResponse;
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

        $admin->fill([
            'email' => $data['email'],
            'whatsapp_number' => $data['whatsapp_number'] ?? null,
        ]);

        if ($request->filled('password')) {
            $admin->password = Hash::make($data['password']);
        }

        if ($request->hasFile('photo')) {
            if ($admin->photo) {
                Storage::disk('public')->delete($admin->photo);
            }

            $admin->photo = $request->file('photo')->store('photos/admins', 'public');
        }

        $admin->save();

        return redirect()->route('admin.profil')->with('success', 'Profil admin berhasil diperbarui.');
    }
}
