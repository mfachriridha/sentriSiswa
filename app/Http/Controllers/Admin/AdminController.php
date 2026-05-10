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
            'hadirHariIni' => 0,
        ]);
    }

    public function siswa()
    {
        return view('admin.siswa', [
            'students' => Student::with('kelas')->latest()->get(),
            'totalSiswa' => Student::count(),
        ]);
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
        $delimiter = (str_contains($firstLine, ';')) ? ';' : ',';
        $header = str_getcsv(trim($firstLine), $delimiter);
        $rows = [];

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (count($row) < 4) continue;

            $nama = trim($row[0] ?? '');
            $jk = trim($row[1] ?? '');
            $nis = trim($row[2] ?? '');
            $kelasName = trim($row[3] ?? '');
            $nisn = trim($row[4] ?? '');

            if (empty($nama) || empty($nis)) continue;

            $rows[] = ['nama' => $nama, 'gender' => $jk, 'nis' => $nis, 'kelas' => $kelasName, 'nisn' => $nisn];
        }

        fclose($handle);

        if (empty($rows)) {
            return back()->with('error', 'Tidak ada data valid di file CSV.');
        }

        session()->flash('csv_rows', $rows);

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

    public function importCsv(Request $request)
    {
        $rows = session('csv_rows', []);

        if (empty($rows)) {
            return redirect()->route('admin.siswa')->with('error', 'Tidak ada data CSV untuk disimpan.');
        }

        $imported = 0;
        foreach ($rows as $row) {
            $kelas = null;
            if (!empty($row['kelas'])) {
                $kelas = Kelas::firstOrCreate(['name' => $row['kelas']]);
            }

            Student::create([
                'name' => $row['nama'],
                'gender' => $row['gender'],
                'nis' => $row['nis'],
                'kelas_id' => $kelas?->id,
                'nisn' => $row['nisn'] ?: null,
            ]);

            $imported++;
        }

        session()->forget('csv_rows');

        return redirect()->route('admin.siswa')
            ->with('success', "{$imported} siswa berhasil diimpor.");
    }
}