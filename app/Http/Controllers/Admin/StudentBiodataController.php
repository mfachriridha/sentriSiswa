<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\UpdateStudentBiodataRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $student->studentProfile->biodata()->updateOrCreate(
            ['student_profile_id' => $student->studentProfile->id],
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

        if ($student->studentProfile->photo) {
            $oldPath = storage_path('app/public/'.$student->studentProfile->photo);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        $file = $request->file('photo');
        $filename = $student->id.'.'.$file->getClientOriginalExtension();
        $path = 'photos/students/'.$filename;

        $manager = new ImageManager(new Driver);
        $image = $manager->read($file->getRealPath());
        $image->cover(300, 400);
        $image->toJpeg(85)->save(storage_path('app/public/'.$path));

        $student->studentProfile->update(['photo' => $path]);

        return response()->json(['url' => asset('storage/'.$path)]);
    }

    public function deletePhoto(User $student): JsonResponse
    {
        $student->load('studentProfile');

        if ($student->studentProfile->photo) {
            $oldPath = storage_path('app/public/'.$student->studentProfile->photo);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }

            $student->studentProfile->update(['photo' => null]);
        }

        return response()->json(['success' => true]);
    }
}
