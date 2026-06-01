<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guru\UpdateDailyAttendanceRequest;
use App\Models\Attendance;
use App\Models\StudentProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ClassRosterController extends Controller
{
    public function index(Request $request): View
    {
        $class = Auth::user()->homeroomClass;

        if (! $class) {
            return view('guru.kelas-saya.empty');
        }

        $today = now()->toDateString();
        $isWeekday = now()->isWeekday();
        $studentIds = $class->students()->pluck('student_profiles.id');
        $attendances = Attendance::query()
            ->whereIn('student_profile_id', $studentIds)
            ->whereDate('date', $today)
            ->get()
            ->keyBy('student_profile_id');

        $students = $class->students()
            ->join('users', 'student_profiles.user_id', '=', 'users.id')
            ->with('user')
            ->when($request->string('search')->toString(), function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('users.name', 'like', "%{$search}%")
                        ->orWhere('student_profiles.nis', 'like', "%{$search}%");
                });
            })
            ->orderBy('users.name')
            ->select('student_profiles.*')
            ->get();

        $stats = [
            'hadir' => 0,
            'terlambat' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpha' => 0,
            'belum_absen' => 0,
        ];

        foreach ($studentIds as $studentId) {
            $status = $attendances->get($studentId)?->status ?? 'belum_absen';
            $stats[$status]++;
        }

        return view('guru.kelas-saya.index', compact('class', 'students', 'attendances', 'stats', 'isWeekday'));
    }

    public function updateAttendance(UpdateDailyAttendanceRequest $request, StudentProfile $studentProfile): RedirectResponse
    {
        $class = Auth::user()->homeroomClass;

        if (! $class || $studentProfile->class_id !== $class->id) {
            abort(403);
        }

        if (now()->isWeekend()) {
            return redirect()->route('guru.kelas-saya')->with('error', 'Absensi hanya tersedia pada hari Senin sampai Jumat.');
        }

        $attendance = Attendance::query()
            ->where('student_profile_id', $studentProfile->id)
            ->whereDate('date', now()->toDateString())
            ->first();

        if (! $attendance) {
            $attendance = new Attendance([
                'student_profile_id' => $studentProfile->id,
                'date' => now()->toDateString(),
            ]);
        }

        $attendance->status = $request->validated('status');
        $attendance->save();

        return redirect()->route('guru.kelas-saya')->with('success', 'Status absensi hari ini berhasil diperbarui.');
    }
}
