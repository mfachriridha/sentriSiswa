<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\Kelas;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\StudentViolation;
use App\Models\Violation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // ─── Dashboard ───
    public function dashboard()
    {
        return view('admin.dashboard', [
            'totalSiswa' => Student::count(),
            'totalKelas' => Kelas::count(),
            'hadirHariIni' => 0,
        ]);
    }

    // ─── Kelas ───
    public function kelas()
    {
        return view('admin.kelas', [
            'kelases' => Kelas::withCount('students')->latest()->get(),
        ]);
    }

    public function storeKelas(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:50|unique:kelas,name']);
        Kelas::create($data);
        return back()->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function updateKelas(Request $request, Kelas $kelas)
    {
        $data = $request->validate(['name' => 'required|string|max:50|unique:kelas,name,' . $kelas->id]);
        $kelas->update($data);
        return back()->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroyKelas(Kelas $kelas)
    {
        if ($kelas->students()->exists()) {
            return back()->with('error', 'Kelas tidak bisa dihapus karena masih memiliki siswa.');
        }
        $kelas->delete();
        return back()->with('success', 'Kelas berhasil dihapus.');
    }

    // ─── Siswa ───
    public function siswa()
    {
        $students = Student::with(['kelas', 'father', 'mother'])->latest()->get();
        return view('admin.siswa', ['students' => $students, 'totalSiswa' => $students->count()]);
    }

    public function storeSiswa(Request $request)
    {
        $data = $request->validate([
            'nis' => 'required|string|max:20',
            'nisn' => 'nullable|string|max:20',
            'name' => 'required|string|max:200',
            'gender' => 'nullable|string',
            'kelas_name' => 'nullable|string|max:50',
            'birth_place' => 'nullable|string', 'birth_date' => 'nullable|date',
            'religion' => 'nullable|string', 'family_status' => 'nullable|string',
            'birth_order' => 'nullable|string', 'address' => 'nullable|string',
            'home_phone' => 'nullable|string', 'prev_school' => 'nullable|string',
            'admission_class' => 'nullable|string', 'admission_date' => 'nullable|string',
            'father_name' => 'nullable|string', 'father_job' => 'nullable|string', 'father_phone' => 'nullable|string',
            'mother_name' => 'nullable|string', 'mother_job' => 'nullable|string', 'mother_phone' => 'nullable|string',
            'guardian_name' => 'nullable|string', 'guardian_job' => 'nullable|string', 'guardian_phone' => 'nullable|string', 'guardian_address' => 'nullable|string',
        ]);

        $kelas = null;
        if (!empty($data['kelas_name'])) {
            $kelas = Kelas::firstOrCreate(['name' => $data['kelas_name']]);
        }

        $student = Student::create([
            'nis' => $data['nis'], 'nisn' => $data['nisn'] ?? null,
            'name' => $data['name'], 'gender' => $data['gender'] ?? null,
            'kelas_id' => $kelas?->id,
            'birth_place' => $data['birth_place'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'religion' => $data['religion'] ?? null,
            'family_status' => $data['family_status'] ?? null,
            'birth_order' => $data['birth_order'] ?? null,
            'address' => $data['address'] ?? null,
            'home_phone' => $data['home_phone'] ?? null,
            'prev_school' => $data['prev_school'] ?? null,
            'admission_class' => $data['admission_class'] ?? null,
            'admission_date' => $data['admission_date'] ?? null,
        ]);

        if (!empty($data['father_name'])) {
            StudentParent::create(['student_id' => $student->id, 'type' => 'father', 'name' => $data['father_name'], 'job' => $data['father_job'] ?? null, 'phone' => $data['father_phone'] ?? null]);
        }
        if (!empty($data['mother_name'])) {
            StudentParent::create(['student_id' => $student->id, 'type' => 'mother', 'name' => $data['mother_name'], 'job' => $data['mother_job'] ?? null, 'phone' => $data['mother_phone'] ?? null]);
        }
        if (!empty($data['guardian_name'])) {
            Guardian::create(['student_id' => $student->id, 'name' => $data['guardian_name'], 'job' => $data['guardian_job'] ?? null, 'phone' => $data['guardian_phone'] ?? null, 'address' => $data['guardian_address'] ?? null]);
        }

        return redirect()->route('admin.siswa')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function editSiswa(Student $student)
    {
        $student->load(['kelas', 'father', 'mother', 'guardian']);
        return view('admin.siswa-form', ['student' => $student]);
    }

    public function updateSiswa(Request $request, Student $student)
    {
        $data = $request->validate([
            'nis' => 'required|string|max:20',
            'nisn' => 'nullable|string|max:20',
            'name' => 'required|string|max:200',
            'gender' => 'nullable|string',
            'kelas_name' => 'nullable|string|max:50',
            'birth_place' => 'nullable|string', 'birth_date' => 'nullable|date',
            'religion' => 'nullable|string', 'family_status' => 'nullable|string',
            'birth_order' => 'nullable|string', 'address' => 'nullable|string',
            'home_phone' => 'nullable|string', 'prev_school' => 'nullable|string',
            'admission_class' => 'nullable|string', 'admission_date' => 'nullable|string',
            'father_name' => 'nullable|string', 'father_job' => 'nullable|string', 'father_phone' => 'nullable|string',
            'mother_name' => 'nullable|string', 'mother_job' => 'nullable|string', 'mother_phone' => 'nullable|string',
            'guardian_name' => 'nullable|string', 'guardian_job' => 'nullable|string', 'guardian_phone' => 'nullable|string', 'guardian_address' => 'nullable|string',
        ]);

        $kelas = null;
        if (!empty($data['kelas_name'])) {
            $kelas = Kelas::firstOrCreate(['name' => $data['kelas_name']]);
        }

        $student->update([
            'nis' => $data['nis'], 'nisn' => $data['nisn'] ?? null,
            'name' => $data['name'], 'gender' => $data['gender'] ?? null,
            'kelas_id' => $kelas?->id ?? $student->kelas_id,
            'birth_place' => $data['birth_place'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'religion' => $data['religion'] ?? null,
            'family_status' => $data['family_status'] ?? null,
            'birth_order' => $data['birth_order'] ?? null,
            'address' => $data['address'] ?? null,
            'home_phone' => $data['home_phone'] ?? null,
            'prev_school' => $data['prev_school'] ?? null,
            'admission_class' => $data['admission_class'] ?? null,
            'admission_date' => $data['admission_date'] ?? null,
        ]);

        // Upsert parents
        if (!empty($data['father_name'])) {
            StudentParent::updateOrCreate(
                ['student_id' => $student->id, 'type' => 'father'],
                ['name' => $data['father_name'], 'job' => $data['father_job'] ?? null, 'phone' => $data['father_phone'] ?? null]
            );
        }
        if (!empty($data['mother_name'])) {
            StudentParent::updateOrCreate(
                ['student_id' => $student->id, 'type' => 'mother'],
                ['name' => $data['mother_name'], 'job' => $data['mother_job'] ?? null, 'phone' => $data['mother_phone'] ?? null]
            );
        }
        if (!empty($data['guardian_name'])) {
            Guardian::updateOrCreate(
                ['student_id' => $student->id],
                ['name' => $data['guardian_name'], 'job' => $data['guardian_job'] ?? null, 'phone' => $data['guardian_phone'] ?? null, 'address' => $data['guardian_address'] ?? null]
            );
        }

        return redirect()->route('admin.siswa')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroySiswa(Student $student)
    {
        $student->delete();
        return back()->with('success', 'Siswa berhasil dihapus.');
    }

    // ─── CSV Import ───
    public function previewCsv(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt']);
        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $firstLine = fgets($handle);
        if ($firstLine === false) { fclose($handle); return back()->with('error', 'File CSV kosong.'); }
        $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine);
        $delimiter = str_contains($firstLine, ';') ? ';' : ',';
        $rows = [];
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (count($row) < 4) continue;
            $nama = trim($row[0] ?? ''); $jk = trim($row[1] ?? '');
            $nis = trim($row[2] ?? ''); $kelasName = trim($row[3] ?? ''); $nisn = trim($row[4] ?? '');
            if (empty($nama) || empty($nis)) continue;
            $rows[] = ['nama' => $nama, 'gender' => $jk, 'nis' => $nis, 'kelas' => $kelasName, 'nisn' => $nisn];
        }
        fclose($handle);
        if (empty($rows)) return back()->with('error', 'Tidak ada data valid di file CSV.');
        session()->put('csv_rows', $rows);
        return redirect()->route('admin.siswa.preview');
    }

    public function preview()
    {
        $rows = session('csv_rows', []);
        if (empty($rows)) return redirect()->route('admin.siswa')->with('error', 'Tidak ada data CSV untuk dipratinjau.');
        return view('admin.siswa-preview', ['rows' => $rows, 'total' => count($rows)]);
    }

    public function importCsv()
    {
        $rows = session('csv_rows', []);
        if (empty($rows)) return redirect()->route('admin.siswa')->with('error', 'Tidak ada data CSV untuk disimpan.');
        $imported = 0;
        foreach ($rows as $row) {
            $kelas = !empty($row['kelas']) ? Kelas::firstOrCreate(['name' => $row['kelas']]) : null;
            Student::create([
                'name' => $row['nama'], 'gender' => $row['gender'], 'nis' => $row['nis'],
                'kelas_id' => $kelas?->id, 'nisn' => $row['nisn'] ?: null,
            ]);
            $imported++;
        }
        session()->forget('csv_rows');
        return redirect()->route('admin.siswa')->with('success', "{$imported} siswa berhasil diimpor.");
    }

    // ─── Poin Pelanggaran ───
    public function poin()
    {
        return view('admin.poin-pelanggaran', [
            'violations' => Violation::orderBy('poin')->get(),
            'students' => Student::select('id', 'name', 'nis', 'poin', 'kelas_id')->with('kelas')->get(),
            'riwayat' => StudentViolation::with(['student', 'violation'])->latest()->take(20)->get(),
        ]);
    }

    public function storePoin(Request $request)
    {
        $data = $request->validate([
            'student_id' => 'required|exists:students,id',
            'violation_id' => 'required|exists:violations,id',
            'note' => 'nullable|string',
        ]);

        $student = Student::findOrFail($data['student_id']);
        $violation = Violation::findOrFail($data['violation_id']);

        $student->decrement('poin', $violation->poin);

        StudentViolation::create([
            'student_id' => $student->id,
            'violation_id' => $violation->id,
            'note' => $data['note'] ?? null,
        ]);

        return back()->with('success', "Pelanggaran ditambahkan. Poin {$student->name}: {$student->fresh()->poin}");
    }

    // ─── Tata Tertib ───
    public function tataTertib()
    {
        return view('admin.tata-tertib');
    }

    public function uploadTataTertib(Request $request)
    {
        $request->validate(['pdf' => 'required|file|mimes:pdf|max:10240']);
        $path = $request->file('pdf')->storeAs('tata-tertib', 'tata_tertib.pdf', 'public');
        return back()->with('success', 'PDF Tata Tertib berhasil diunggah.');
    }

    // ─── Profile ───
    public function profile()
    {
        return view('admin.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        if (!empty($data['password'])) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        session()->put('user_name', $user->name);
        session()->put('user_email', $user->email);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}