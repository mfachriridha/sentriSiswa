<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\UpdateProfilRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function show(): View
    {
        $teacher = Auth::user();
        $teacher->load(['teacherProfile', 'homeroomClass']);

        return view('guru.profil', compact('teacher'));
    }

    public function edit(): View
    {
        $teacher = Auth::user();
        $teacher->load(['teacherProfile', 'homeroomClass']);

        return view('guru.profil-edit', compact('teacher'));
    }

    public function update(UpdateProfilRequest $request): RedirectResponse
    {
        $teacher = Auth::user();
        $data = $request->validated();

        // Update user fields
        $teacher->update([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
        ]);

        if ($request->filled('password')) {
            $teacher->update([
                'password' => Hash::make($data['password']),
            ]);
        }

        // Handle profile photo
        $profile = $teacher->teacherProfile;
        if (! $profile) {
            $profile = $teacher->teacherProfile()->create([]);
        }

        // Delete photo if requested
        if ($request->has('delete_photo') && filter_var($request->input('delete_photo'), FILTER_VALIDATE_BOOLEAN)) {
            if ($profile->photo) {
                Storage::disk('public')->delete($profile->photo);
                $profile->update(['photo' => null]);
            }
        }
        // Upload new photo
        elseif ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($profile->photo) {
                Storage::disk('public')->delete($profile->photo);
            }
            $path = $request->file('photo')->store('photos/teachers', 'public');
            $profile->update(['photo' => $path]);
        }

        // Update phone (still separate)
        $profile->update(['phone' => $data['phone'] ?? null]);

        return redirect()->route($teacher->profilRouteName())->with('success', 'Profil berhasil diperbarui.');
    }

    public function uploadPhoto(): RedirectResponse
    {
        request()->validate([
            'photo' => ['required', 'image', 'max:2048'],
        ]);

        $teacher = Auth::user();
        $profile = $teacher->teacherProfile ?? $teacher->teacherProfile()->create();

        if ($profile->photo) {
            Storage::disk('public')->delete($profile->photo);
        }

        $path = request()->file('photo')->store('photos/teachers', 'public');

        $profile->update(['photo' => $path]);

        return redirect()->route($teacher->profilRouteName())->with('success', 'Foto berhasil diunggah.');
    }

    public function deletePhoto(): RedirectResponse
    {
        $teacher = Auth::user();
        $profile = $teacher->teacherProfile;

        if ($profile && $profile->photo) {
            Storage::disk('public')->delete($profile->photo);
            $profile->update(['photo' => null]);
        }

        return redirect()->route($teacher->profilRouteName())->with('success', 'Foto berhasil dihapus.');
    }
}
