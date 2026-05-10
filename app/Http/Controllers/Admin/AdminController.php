<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guardian;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentParent;
use App\Models\StudentViolation;
use App\Models\TeacherClass;
use App\Models\User;
use App\Models\Violation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'totalStudents' => Student::count(),
            'totalClasses' => SchoolClass::count(),
            'presentToday' => 0,
        ]);
    }

    // ─── Guru ───
    public function guru()
    {
        $teachers = User::whereIn('role', ['wali_kelas', 'bk'])
            ->with('teacherClasses.schoolClass')
            ->latest()
            ->get();
        $classes = SchoolClass::orderBy('name')->get();

        return view('admin.guru', compact('teachers', 'classes'));
    }

    public function storeGuru(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'nip' => 'nullable|regex:/^[0-9\s]+$/|max:22|unique:users,nip',
            'role' => 'required|in:wali_kelas,bk',
            'class_ids' => 'nullable|array',
            'class_ids.*' => 'exists:school_classes,id',
        ]);

        $nip = $data['nip'] ? trim($data['nip']) : null;
        $email = $nip
            ? preg_replace('/\s+/', '', $nip).'@sentrisiswa.sch.id'
            : strtolower(preg_replace('/[^a-z]/', '', $data['name'])).'@sentrisiswa.sch.id';

        $user = User::create([
            'name' => $data['name'],
            'nip' => $nip,
            'email' => $email,
            'role' => $data['role'],
            'is_active' => false,
            'password' => Hash::make('password123'),
        ]);

        $this->syncTeacherClasses($user, $data['role'], $data['class_ids'] ?? []);

        return back()->with('success', 'Guru berhasil ditambahkan.');
    }

    public function editGuru(User $user)
    {
        $user->load('teacherClasses.schoolClass');

        if (request()->ajax() || request()->wantsJson()) {
            $classes = SchoolClass::orderBy('name')->get();

            return response()->json([
                'user' => $user,
                'classes' => $classes,
            ]);
        }

        return view('admin.guru-form', compact('user'));
    }

    public function updateGuru(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'nip' => 'nullable|regex:/^[0-9\s]+$/|max:22|unique:users,nip,'.$user->id,
            'role' => 'required|in:wali_kelas,bk',
            'class_ids' => 'nullable|array',
            'class_ids.*' => 'exists:school_classes,id',
        ]);

        $user->update([
            'name' => $data['name'],
            'nip' => $data['nip'] ? trim($data['nip']) : null,
            'role' => $data['role'],
        ]);

        $this->syncTeacherClasses($user, $data['role'], $data['class_ids'] ?? []);

        return back()->with('success', 'Data guru berhasil diperbarui.');
    }

    protected function syncTeacherClasses(User $user, string $role, array $classIds): void
    {
        TeacherClass::where('user_id', $user->id)->delete();

        foreach ($classIds as $classId) {
            TeacherClass::create([
                'user_id' => $user->id,
                'school_class_id' => $classId,
                'role' => $role,
            ]);
        }
    }

    public function destroyGuru(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Admin tidak dapat dihapus.');
        }

        $user->delete();

        return back()->with('success', 'Guru berhasil dihapus.');
    }

    public function previewGuruCsv(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt']);
        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);

            return back()->with('error', 'File CSV kosong.');
        }
        $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine);
        $delimiter = str_contains($firstLine, ';') ? ';' : ',';
        $rows = [];
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $nama = trim($row[0] ?? '');
            $nip = trim($row[1] ?? '');
            if (empty($nama)) {
                continue;
            }
            $rows[] = ['nama' => $nama, 'nip' => $nip !== '-' ? $nip : ''];
        }
        fclose($handle);
        if (empty($rows)) {
            return back()->with('error', 'Tidak ada data valid di file CSV.');
        }
        session()->put('guru_csv_rows', $rows);

        return redirect()->route('admin.guru.preview');
    }

    public function previewGuru()
    {
        $rows = session('guru_csv_rows', []);
        if (empty($rows)) {
            return redirect()->route('admin.guru')->with('error', 'Tidak ada data CSV untuk dipratinjau.');
        }

        return view('admin.guru-preview', ['rows' => $rows, 'total' => count($rows)]);
    }

    public function importGuruCsv()
    {
        $rows = session('guru_csv_rows', []);
        if (empty($rows)) {
            return redirect()->route('admin.guru')->with('error', 'Tidak ada data CSV untuk disimpan.');
        }
        $imported = 0;
        foreach ($rows as $row) {
            $nip = ! empty($row['nip']) ? trim($row['nip']) : null;
            $email = $nip
                ? preg_replace('/\s+/', '', $nip).'@sentrisiswa.sch.id'
                : strtolower(preg_replace('/[^a-z]/', '', $row['nama'])).'@sentrisiswa.sch.id';

            User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $row['nama'],
                    'nip' => $nip,
                    'role' => 'wali_kelas',
                    'is_active' => false,
                    'password' => Hash::make('password123'),
                ]
            );
            $imported++;
        }
        session()->forget('guru_csv_rows');

        return redirect()->route('admin.guru')->with('success', "{$imported} guru berhasil diimpor.");
    }

    public function downloadGuruTemplate()
    {
        $content = "Nama;NIP\n";
        $content .= "BUDI SANTOSO, S.Pd;19920912 202221 2 018\n";

        return response($content, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_guru.csv"',
        ]);
    }

    // ─── Classes (manajemen kelas) ───
    public function kelas()
    {
        return view('admin.kelas', [
            'classes' => SchoolClass::withCount('students')->latest()->get(),
        ]);
    }

    public function storeClass(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:50|unique:school_classes,name']);
        SchoolClass::create($data);

        return back()->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function updateClass(Request $request, SchoolClass $class)
    {
        $data = $request->validate(['name' => 'required|string|max:50|unique:school_classes,name,'.$class->id]);
        $class->update($data);

        return back()->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroyClass(SchoolClass $class)
    {
        if ($class->students()->exists()) {
            return back()->with('error', 'Kelas tidak bisa dihapus karena masih memiliki siswa.');
        }
        $class->delete();

        return back()->with('success', 'Kelas berhasil dihapus.');
    }

    // ─── Students ───
    public function siswa()
    {
        $students = Student::with(['schoolClass', 'father', 'mother', 'user'])->latest()->get();

        return view('admin.siswa', ['students' => $students, 'totalStudents' => $students->count()]);
    }

    public function storeStudent(Request $request)
    {
        $data = $request->validate([
            'nis' => 'required|string|max:20',
            'nisn' => 'nullable|string|max:20',
            'name' => 'required|string|max:200',
            'gender' => 'nullable|string',
            'class_name' => 'nullable|string|max:50',
            'birth_place' => 'nullable|string', 'birth_date' => 'nullable|date',
            'religion' => 'nullable|string', 'family_status' => 'nullable|string',
            'birth_order' => 'nullable|string', 'address' => 'nullable|string',
            'home_phone' => 'nullable|string', 'prev_school' => 'nullable|string',
            'admission_class' => 'nullable|string', 'admission_date' => 'nullable|string',
            'father_name' => 'nullable|string', 'father_job' => 'nullable|string', 'father_phone' => 'nullable|string',
            'mother_name' => 'nullable|string', 'mother_job' => 'nullable|string', 'mother_phone' => 'nullable|string',
            'guardian_name' => 'nullable|string', 'guardian_job' => 'nullable|string', 'guardian_phone' => 'nullable|string', 'guardian_address' => 'nullable|string',
        ]);

        $class = null;
        if (! empty($data['class_name'])) {
            $class = SchoolClass::firstOrCreate(['name' => $data['class_name']]);
        }

        $student = Student::create([
            'nis' => $data['nis'], 'nisn' => $data['nisn'] ?? null,
            'name' => $data['name'], 'gender' => $data['gender'] ?? null,
            'school_class_id' => $class?->id,
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

        if (! empty($data['father_name'])) {
            StudentParent::create(['student_id' => $student->id, 'type' => 'father', 'name' => $data['father_name'], 'job' => $data['father_job'] ?? null, 'phone' => $data['father_phone'] ?? null]);
        }
        if (! empty($data['mother_name'])) {
            StudentParent::create(['student_id' => $student->id, 'type' => 'mother', 'name' => $data['mother_name'], 'job' => $data['mother_job'] ?? null, 'phone' => $data['mother_phone'] ?? null]);
        }
        if (! empty($data['guardian_name'])) {
            Guardian::create(['student_id' => $student->id, 'name' => $data['guardian_name'], 'job' => $data['guardian_job'] ?? null, 'phone' => $data['guardian_phone'] ?? null, 'address' => $data['guardian_address'] ?? null]);
        }

        return redirect()->route('admin.siswa')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function editSiswa(Student $student)
    {
        $student->load(['schoolClass', 'father', 'mother', 'guardian']);

        return view('admin.siswa-form', ['student' => $student]);
    }

    public function updateStudent(Request $request, Student $student)
    {
        $data = $request->validate([
            'nis' => 'required|string|max:20',
            'nisn' => 'nullable|string|max:20',
            'name' => 'required|string|max:200',
            'gender' => 'nullable|string',
            'class_name' => 'nullable|string|max:50',
            'birth_place' => 'nullable|string', 'birth_date' => 'nullable|date',
            'religion' => 'nullable|string', 'family_status' => 'nullable|string',
            'birth_order' => 'nullable|string', 'address' => 'nullable|string',
            'home_phone' => 'nullable|string', 'prev_school' => 'nullable|string',
            'admission_class' => 'nullable|string', 'admission_date' => 'nullable|string',
            'father_name' => 'nullable|string', 'father_job' => 'nullable|string', 'father_phone' => 'nullable|string',
            'mother_name' => 'nullable|string', 'mother_job' => 'nullable|string', 'mother_phone' => 'nullable|string',
            'guardian_name' => 'nullable|string', 'guardian_job' => 'nullable|string', 'guardian_phone' => 'nullable|string', 'guardian_address' => 'nullable|string',
        ]);

        $class = null;
        if (! empty($data['class_name'])) {
            $class = SchoolClass::firstOrCreate(['name' => $data['class_name']]);
        }

        $student->update([
            'nis' => $data['nis'], 'nisn' => $data['nisn'] ?? null,
            'name' => $data['name'], 'gender' => $data['gender'] ?? null,
            'school_class_id' => $class?->id ?? $student->school_class_id,
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

        if (! empty($data['father_name'])) {
            StudentParent::updateOrCreate(
                ['student_id' => $student->id, 'type' => 'father'],
                ['name' => $data['father_name'], 'job' => $data['father_job'] ?? null, 'phone' => $data['father_phone'] ?? null]
            );
        }
        if (! empty($data['mother_name'])) {
            StudentParent::updateOrCreate(
                ['student_id' => $student->id, 'type' => 'mother'],
                ['name' => $data['mother_name'], 'job' => $data['mother_job'] ?? null, 'phone' => $data['mother_phone'] ?? null]
            );
        }
        if (! empty($data['guardian_name'])) {
            Guardian::updateOrCreate(
                ['student_id' => $student->id],
                ['name' => $data['guardian_name'], 'job' => $data['guardian_job'] ?? null, 'phone' => $data['guardian_phone'] ?? null, 'address' => $data['guardian_address'] ?? null]
            );
        }

        return redirect()->route('admin.siswa')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroyStudent(Student $student)
    {
        $student->delete();

        return back()->with('success', 'Siswa berhasil dihapus.');
    }

    public function previewCsv(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt']);
        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);

            return back()->with('error', 'File CSV kosong.');
        }
        $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine);
        $delimiter = str_contains($firstLine, ';') ? ';' : ',';
        $rows = [];
        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (count($row) < 4) {
                continue;
            }
            $nama = trim($row[0] ?? '');
            $jk = trim($row[1] ?? '');
            $nis = trim($row[2] ?? '');
            $class = trim($row[3] ?? '');
            $nisn = trim($row[4] ?? '');
            if (empty($nama) || empty($nis)) {
                continue;
            }
            $rows[] = ['nama' => $nama, 'gender' => $jk, 'nis' => $nis, 'kelas' => $class, 'nisn' => $nisn];
        }
        fclose($handle);
        if (empty($rows)) {
            return back()->with('error', 'Tidak ada data valid di file CSV.');
        }
        session()->put('csv_rows', $rows);

        return redirect()->route('admin.siswa.preview');
    }

    public function preview()
    {
        $rows = session('csv_rows', []);
        if (empty($rows)) {
            return redirect()->route('admin.siswa')->with('error', 'Tidak ada data CSV untuk dipratinjau.');
        }

        return view('admin.siswa-preview', ['rows' => $rows, 'total' => count($rows)]);
    }

    public function importCsv()
    {
        $rows = session('csv_rows', []);
        if (empty($rows)) {
            return redirect()->route('admin.siswa')->with('error', 'Tidak ada data CSV untuk disimpan.');
        }
        $imported = 0;
        foreach ($rows as $row) {
            $class = ! empty($row['kelas']) ? SchoolClass::firstOrCreate(['name' => $row['kelas']]) : null;
            Student::create([
                'name' => $row['nama'], 'gender' => $row['gender'], 'nis' => $row['nis'],
                'school_class_id' => $class?->id, 'nisn' => $row['nisn'] ?: null,
            ]);
            $imported++;
        }
        session()->forget('csv_rows');

        return redirect()->route('admin.siswa')->with('success', "{$imported} siswa berhasil diimpor.");
    }

    public function poin()
    {
        return view('admin.poin-pelanggaran', [
            'violations' => Violation::orderBy('poin')->get(),
            'students' => Student::select('id', 'name', 'nis', 'poin', 'school_class_id')->with('schoolClass')->get(),
            'riwayat' => StudentViolation::with(['student', 'violation'])->latest()->take(20)->get(),
        ]);
    }

    public function storeViolation(Request $request)
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

    public function tataTertib()
    {
        $pdfPath = 'storage/tata-tertib/tata_tertib.pdf';
        $hasPdf = file_exists(public_path($pdfPath));
        $fileSize = $hasPdf ? filesize(public_path($pdfPath)) : null;
        $lastModified = $hasPdf ? filemtime(public_path($pdfPath)) : null;

        return view('admin.tata-tertib', compact('hasPdf', 'pdfPath', 'fileSize', 'lastModified'));
    }

    public function uploadTataTertib(Request $request)
    {
        $request->validate(['pdf' => 'required|file|mimes:pdf|max:10240']);
        $request->file('pdf')->storeAs('tata-tertib', 'tata_tertib.pdf', 'public');

        return back()->with('success', 'PDF Tata Tertib berhasil diunggah.');
    }

    public function deleteTataTertib()
    {
        $path = public_path('storage/tata-tertib/tata_tertib.pdf');
        if (file_exists($path)) {
            unlink($path);
        }

        return back()->with('success', 'PDF Tata Tertib berhasil dihapus.');
    }

    public function profile()
    {
        return view('admin.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);
        $user->update(['name' => $data['name'], 'email' => $data['email']]);
        if (! empty($data['password'])) {
            $user->update(['password' => Hash::make($data['password'])]);
        }
        session()->put('user_name', $user->name);
        session()->put('user_email', $user->email);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
