<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\StudentViolation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SiswaController extends Controller
{
    protected array $geofence = [
        [106.5770739, -6.1175404],
        [106.5773462, -6.1184805],
        [106.5782018, -6.1183004],
        [106.5779336, -6.1173483],
        [106.5775051, -6.1174344],
        [106.5770739, -6.1175404],
    ];

    public function kehadiran()
    {
        $student = $this->getStudent();
        if (! $student) {
            return redirect()->route('siswa.dashboard')->with('error', 'Data siswa tidak ditemukan.');
        }

        $today = now()->toDateString();
        $attendance = Attendance::where('student_id', $student->id)->where('date', $today)->first();

        return view('siswa.kehadiran', compact('student', 'attendance'));
    }

    public function riwayat(Request $request)
    {
        $student = $this->getStudent();
        if (! $student) {
            return redirect()->route('siswa.dashboard')->with('error', 'Data siswa tidak ditemukan.');
        }

        $attendances = Attendance::where('student_id', $student->id)
            ->latest('date')
            ->paginate(25)
            ->appends($request->query());

        $stats = [
            'hadir' => Attendance::where('student_id', $student->id)->where('status', 'hadir')->count(),
            'izin' => Attendance::where('student_id', $student->id)->where('status', 'izin')->count(),
            'sakit' => Attendance::where('student_id', $student->id)->where('status', 'sakit')->count(),
            'alfa' => Attendance::where('student_id', $student->id)->where('status', 'alfa')->count(),
        ];

        return view('siswa.riwayat', compact('student', 'attendances', 'stats'));
    }

    public function poin()
    {
        $student = $this->getStudent();
        if (! $student) {
            return redirect()->route('siswa.dashboard')->with('error', 'Data siswa tidak ditemukan.');
        }

        $violations = StudentViolation::where('student_id', $student->id)
            ->with('violation')
            ->latest()
            ->paginate(25);

        return view('siswa.poin', compact('student', 'violations'));
    }

    public function pengaturan()
    {
        $student = Student::where('user_id', Auth::id())
            ->with(['schoolClass', 'father', 'mother', 'guardian'])
            ->first();
        $user = Auth::user();

        return view('siswa.pengaturan', compact('student', 'user'));
    }

    public function updatePengaturan(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->update(['name' => $data['name'], 'email' => $data['email']]);
        if (! empty($data['password'])) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    protected function getStudent(): ?Student
    {
        return Student::where('user_id', Auth::id())->first();
    }

    public function storeAttendance(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'selfie' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $student = $this->getStudent();

        if (! $student) {
            return response()->json(['success' => false, 'message' => 'Data siswa tidak ditemukan.'], 404);
        }

        $today = now()->toDateString();

        if (Attendance::where('student_id', $student->id)->where('date', $today)->exists()) {
            return response()->json(['success' => false, 'message' => 'Anda sudah presensi hari ini.']);
        }

        $lat = (float) $request->input('latitude');
        $lng = (float) $request->input('longitude');
        $inside = $this->pointInPolygon($lat, $lng);

        $path = $request->file('selfie')->storeAs(
            'selfies',
            $student->nis.'_'.$today.'.'.$request->file('selfie')->extension(),
            'public'
        );

        Attendance::create([
            'student_id' => $student->id,
            'date' => $today,
            'status' => 'hadir',
            'selfie_path' => 'storage/'.$path,
            'latitude' => $lat,
            'longitude' => $lng,
            'is_inside_geofence' => $inside,
        ]);

        if ($inside) {
            return response()->json(['success' => true, 'message' => 'Presensi berhasil. Data disimpan.']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Presensi tercatat. Peringatan: lokasi Anda di luar area sekolah.',
            'warning' => true,
        ]);
    }

    protected function pointInPolygon(float $lat, float $lng): bool
    {
        $polygon = $this->geofence;
        $n = count($polygon);
        $inside = false;

        for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
            $xi = $polygon[$i][0];
            $yi = $polygon[$i][1];
            $xj = $polygon[$j][0];
            $yj = $polygon[$j][1];

            $intersect = (($yi > $lat) !== ($yj > $lat))
                && ($lng < ($xj - $xi) * ($lat - $yi) / ($yj - $yi) + $xi);

            if ($intersect) {
                $inside = ! $inside;
            }
        }

        return $inside;
    }
}
