@extends('layouts.app')

@section('title', 'Waktu Absen')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Waktu Absen</h1>
    <p class="mt-1 text-base text-gray-500">Konfigurasi jam absensi dan batas keterlambatan.</p>
</div>

<form method="POST" action="{{ route('admin.settings.attendance-time.update') }}" class="space-y-6"
      x-data="{ loading: false }" @submit="loading = true">
    @csrf
    @method('PUT')

    <div class="rounded-xl border border-gray-200 bg-white p-8">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <div>
                <label for="attendance_start_time" class="block text-base font-medium text-gray-700">Jam Mulai Absen <span class="text-red-500">*</span></label>
                <input id="attendance_start_time" type="time" name="attendance_start_time" value="{{ old('attendance_start_time', $startTime) }}" required
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-base text-gray-900 shadow-sm
                              focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                <p class="mt-1.5 text-sm text-gray-500">Siswa bisa mulai absen dari jam ini.</p>
                @error('attendance_start_time')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="attendance_end_time" class="block text-base font-medium text-gray-700">Jam Selesai Absen <span class="text-red-500">*</span></label>
                <input id="attendance_end_time" type="time" name="attendance_end_time" value="{{ old('attendance_end_time', $endTime) }}" required
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-base text-gray-900 shadow-sm
                              focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                <p class="mt-1.5 text-sm text-gray-500">Siswa tidak bisa absen setelah jam ini.</p>
                @error('attendance_end_time')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="attendance_late_time" class="block text-base font-medium text-gray-700">Batas Terlambat <span class="text-red-500">*</span></label>
                <input id="attendance_late_time" type="time" name="attendance_late_time" value="{{ old('attendance_late_time', $lateTime) }}" required
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-base text-gray-900 shadow-sm
                              focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                <p class="mt-1.5 text-sm text-gray-500">Absen setelah jam ini dicatat sebagai terlambat.</p>
                @error('attendance_late_time')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="flex items-center gap-4">
        <button type="submit" :disabled="loading"
                class="inline-flex items-center gap-2 rounded-lg bg-primary px-6 py-3 text-base font-semibold text-white shadow-sm
                       hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50
                       transition-colors disabled:opacity-60">
            <span x-show="loading">
                <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
            </span>
            Simpan
        </button>
    </div>
</form>
@endsection