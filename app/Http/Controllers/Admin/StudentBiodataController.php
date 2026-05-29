<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\UpdateStudentBiodataRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\View\View;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class StudentBiodataController extends Controller
{
    public function edit(User $student): View
    {
        $student->load('studentProfile.biodata');

        return view('admin.student.biodata', compact('student'));
    }

    public function update(UpdateStudentBiodataRequest $request, User $student): RedirectResponse
    {
        $student->load('studentProfile');
        $profile = $student->studentProfile;

        abort_if(! $profile, 404);

        $profile->biodata()->updateOrCreate(
            ['student_profile_id' => $profile->id],
            $request->validated(),
        );

        return redirect()
            ->route('admin.siswa.biodata.edit', $student)
            ->with('success', 'Biodata berhasil diperbarui.');
    }

    public function uploadPhoto(Request $request, User $student): JsonResponse
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $student->load('studentProfile');
        $profile = $student->studentProfile;
        $file = $request->file('photo');

        abort_if(! $profile || ! $file instanceof UploadedFile, 422);

        if ($profile->photo) {
            $oldPath = storage_path('app/public/'.$profile->photo);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $filename = $student->id.'.'.$file->getClientOriginalExtension();
        $path = 'photos/students/'.$filename;
        $realPath = $file->getRealPath();

        abort_if($realPath === false, 422);

        $manager = new ImageManager(new Driver);
        $image = $manager->read($realPath);
        $image->cover(300, 400);
        $image->toJpeg(85)->save(storage_path('app/public/'.$path));

        $profile->update(['photo' => $path]);

        return response()->json(['url' => asset('storage/'.$path)]);
    }

    public function deletePhoto(User $student): JsonResponse
    {
        $student->load('studentProfile');
        $profile = $student->studentProfile;

        if ($profile?->photo) {
            $oldPath = storage_path('app/public/'.$profile->photo);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }

            $profile->update(['photo' => null]);
        }

        return response()->json(['success' => true]);
    }
}
