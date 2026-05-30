<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\UpdateProfilRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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

        $teacher->update([
            'email' => $data['email'] ?? null,
        ]);

        if ($request->filled('password')) {
            $teacher->update([
                'password' => Hash::make($data['password']),
            ]);
        }

        $teacher->teacherProfile()->updateOrCreate(
            ['user_id' => $teacher->id],
            ['phone' => $data['phone'] ?? null],
        );

        return redirect()->route('guru.profil')->with('success', 'Profil berhasil diperbarui.');
    }
}
