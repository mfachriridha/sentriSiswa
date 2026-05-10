<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $student = Student::where('user_id', Auth::id())->first();

        if (! $student) {
            return redirect()->route('siswa.dashboard')->with('error', 'Data siswa tidak ditemukan.');
        }

        $today = now()->toDateString();
        $attendance = Attendance::where('student_id', $student->id)->where('date', $today)->first();

        return view('siswa.kehadiran', compact('student', 'attendance'));
    }

    public function storeAttendance(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'selfie' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $student = Student::where('user_id', Auth::id())->first();

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
