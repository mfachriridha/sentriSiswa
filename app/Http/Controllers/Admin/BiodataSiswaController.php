<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Siswa\UpdateBiodataSiswaRequest;
use App\Models\Pengguna;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\View\View;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class BiodataSiswaController extends Controller
{
    public function edit(Pengguna $siswa): View
    {
        $siswa->load('profilSiswa.biodata');

        return view('admin.siswa.biodata', compact('siswa'));
    }

    public function update(UpdateBiodataSiswaRequest $request, Pengguna $siswa): RedirectResponse
    {
        $siswa->load('profilSiswa');
        $profile = $siswa->profilSiswa;

        abort_if(! $profile, 404);

        $profile->biodata()->updateOrCreate(
            ['profil_siswa_id' => $profile->nisn],
            $request->validated(),
        );

        return redirect()
            ->route('admin.siswa.biodata.edit', $siswa)
            ->with('success', 'Biodata berhasil diperbarui.');
    }

    public function uploadPhoto(Request $request, Pengguna $siswa): JsonResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $siswa->load('profilSiswa');
        $profile = $siswa->profilSiswa;
        $file = $request->file('photo');

        abort_if(! $profile || ! $file instanceof UploadedFile, 422);

        if ($profile->foto) {
            $oldPath = storage_path('app/public/'.$profile->foto);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $filename = $siswa->id.'.'.$file->getClientOriginalExtension();
        $path = 'photos/students/'.$filename;
        $realPath = $file->getRealPath();

        abort_if($realPath === false, 422);

        $manager = new ImageManager(new Driver);
        $image = $manager->read($realPath);
        $image->cover(300, 400);
        $image->toJpeg(85)->save(storage_path('app/public/'.$path));

        $profile->update(['foto' => $path]);

        return response()->json(['url' => asset('storage/'.$path)]);
    }

    public function deletePhoto(Pengguna $siswa): JsonResponse
    {
        $siswa->load('profilSiswa');
        $profile = $siswa->profilSiswa;

        if ($profile?->foto) {
            $oldPath = storage_path('app/public/'.$profile->foto);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }

            $profile->update(['foto' => null]);
        }

        return response()->json(['success' => true]);
    }
}
