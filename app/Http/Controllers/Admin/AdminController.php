<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Student;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'totalSiswa' => Student::count(),
            'totalKelas' => Kelas::count(),
            'hadirHariIni' => 0, // nanti dari absensi
        ]);
    }

    public function siswa()
    {
        return view('admin.siswa', [
            'students' => Student::with('kelas')->latest()->get(),
            'totalSiswa' => Student::count(),
        ]);
    }

    public function importCsv(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt']);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle); // skip header
        $imported = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 4) continue;

            $nama = trim($row[0] ?? '');
            $jk = trim($row[1] ?? '');
            $nis = trim($row[2] ?? '');
            $kelasName = trim($row[3] ?? '');
            $nisn = trim($row[4] ?? '');

            if (empty($nama) || empty($nis)) continue;

            $kelas = null;
            if (!empty($kelasName)) {
                $kelas = Kelas::firstOrCreate(['name' => $kelasName]);
            }

            Student::create([
                'name' => $nama,
                'gender' => $jk,
                'nis' => $nis,
                'kelas_id' => $kelas?->id,
                'nisn' => $nisn ?: null,
            ]);

            $imported++;
        }

        fclose($handle);

        return redirect()->route('admin.siswa')
            ->with('success', "{$imported} siswa berhasil diimpor.");
    }
}