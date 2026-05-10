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

        // Baca baris pertama untuk deteksi delimiter & BOM
        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            return back()->with('error', 'File CSV kosong.');
        }

        // Hapus BOM (﻿)
        $firstLine = preg_replace('/^\xEF\xBB\xBF/', '', $firstLine);

        // Deteksi delimiter: kalau ada ";" pakai ";", kalau tidak pakai ","
        $delimiter = (str_contains($firstLine, ';')) ? ';' : ',';

        // Parse header
        $header = str_getcsv(trim($firstLine), $delimiter);
        $imported = 0;

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
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