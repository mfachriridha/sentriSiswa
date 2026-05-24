<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function attendanceTime(): View
    {
        return view('admin.settings.attendance-time', [
            'startTime' => Setting::get('attendance_start_time', '06:30'),
            'endTime' => Setting::get('attendance_end_time', '07:00'),
            'lateTime' => Setting::get('attendance_late_time', '07:00'),
        ]);
    }

    public function attendanceTimeUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'attendance_start_time' => ['required', 'date_format:H:i'],
            'attendance_end_time' => ['required', 'date_format:H:i', 'after:attendance_start_time'],
            'attendance_late_time' => ['required', 'date_format:H:i', 'after_or_equal:attendance_start_time'],
        ], [
            'attendance_start_time.required' => 'Jam mulai absen wajib diisi.',
            'attendance_start_time.date_format' => 'Format jam tidak valid.',
            'attendance_end_time.required' => 'Jam selesai absen wajib diisi.',
            'attendance_end_time.date_format' => 'Format jam tidak valid.',
            'attendance_end_time.after' => 'Jam selesai harus setelah jam mulai.',
            'attendance_late_time.required' => 'Batas terlambat wajib diisi.',
            'attendance_late_time.date_format' => 'Format jam tidak valid.',
            'attendance_late_time.after_or_equal' => 'Batas terlambat harus setelah jam mulai.',
        ]);

        Setting::set('attendance_start_time', $validated['attendance_start_time']);
        Setting::set('attendance_end_time', $validated['attendance_end_time']);
        Setting::set('attendance_late_time', $validated['attendance_late_time']);

        return redirect()->route('admin.settings.attendance-time.index')->with('success', 'Konfigurasi waktu absen berhasil disimpan.');
    }

    public function whatsapp(): View
    {
        return view('admin.settings.whatsapp', [
            'fonnteApiKey' => Setting::get('fonnte_api_key', ''),
        ]);
    }

    public function whatsappUpdate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fonnte_api_key' => ['nullable', 'string', 'max:255'],
        ], [
            'fonnte_api_key.max' => 'API key tidak boleh lebih dari 255 karakter.',
        ]);

        Setting::set('fonnte_api_key', $validated['fonnte_api_key'] ?? '');

        return redirect()->route('admin.settings.whatsapp.index')->with('success', 'Konfigurasi WhatsApp API berhasil disimpan.');
    }
}
