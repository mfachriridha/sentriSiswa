<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\TeacherClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaliKelasController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $teacherClass = TeacherClass::where('user_id', $user->id)
            ->where('role', 'wali_kelas')
            ->with('schoolClass')
            ->first();

        if (! $teacherClass) {
            return view('wali-kelas.dashboard', [
                'schoolClass' => null,
                'attendances' => collect(),
                'students' => collect(),
            ])->with('error', 'Anda belum di-assign ke kelas manapun.');
        }

        $today = now()->toDateString();

        $students = Student::where('school_class_id', $teacherClass->school_class_id)
            ->orderBy('name')
            ->get();

        $attendances = Attendance::whereIn('student_id', $students->pluck('id'))
            ->where('date', $today)
            ->get()
            ->keyBy('student_id');

        return view('wali-kelas.dashboard', compact('teacherClass', 'students', 'attendances'));
    }

    public function confirm(Attendance $attendance)
    {
        $attendance->update([
            'confirmed_by' => Auth::id(),
            'confirmed_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Presensi dikonfirmasi. Notifikasi akan dikirim ke orang tua.']);
    }

    public function updateStatus(Request $request, Attendance $attendance)
    {
        $data = $request->validate([
            'status' => 'required|in:hadir,izin,sakit,alfa',
            'note' => 'nullable|string|max:500',
        ]);

        $attendance->update([
            'status' => $data['status'],
            'note' => $data['note'] ?? $attendance->note,
        ]);

        return response()->json(['success' => true, 'message' => 'Status presensi diubah.']);
    }
}
