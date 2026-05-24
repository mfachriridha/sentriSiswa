<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Http\Requests\Siswa\UpdateSiswaProfilRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfilController extends Controller
{
    public function show(): View
    {
        $student = Auth::user();
        $student->load(['studentProfile.class', 'studentProfile.biodata']);

        return view('siswa.profil', compact('student'));
    }

    public function edit(): View
    {
        $student = Auth::user();
        $student->load('studentProfile');

        return view('siswa.profil-edit', compact('student'));
    }

    public function update(UpdateSiswaProfilRequest $request): RedirectResponse
    {
        $student = Auth::user();

        $student->studentProfile->update([
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        return redirect()->route('siswa.profil')->with('success', 'Profil berhasil diperbarui.');
    }

    public function uploadPhoto(): RedirectResponse
    {
        request()->validate([
            'photo' => ['required', 'image', 'max:2048'],
        ]);

        $student = Auth::user();
        $profile = $student->studentProfile;

        if ($profile->photo) {
            Storage::disk('public')->delete($profile->photo);
        }

        $path = request()->file('photo')->store('photos/students', 'public');

        $profile->update(['photo' => $path]);

        return redirect()->route('siswa.profil')->with('success', 'Foto berhasil diunggah.');
    }

    public function deletePhoto(): RedirectResponse
    {
        $student = Auth::user();
        $profile = $student->studentProfile;

        if ($profile->photo) {
            Storage::disk('public')->delete($profile->photo);
            $profile->update(['photo' => null]);
        }

        return redirect()->route('siswa.profil')->with('success', 'Foto berhasil dihapus.');
    }
}
