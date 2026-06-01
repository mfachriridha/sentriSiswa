@extends('layouts.app')

@section('title', 'Waktu Absen')

@section('content')
@php
    [$startHour, $startMinute] = explode(':', $startTime);
    [$endHour, $endMinute] = explode(':', $endTime);
    $lateToleranceOptions = [0, 5, 10, 15, 20, 30, 45, 60, 90, 120];
@endphp

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Waktu Absen</h1>
    <p class="mt-1 text-sm text-gray-500">Konfigurasi jam absensi dan toleransi keterlambatan.</p>
</div>

<form method="POST" action="{{ route('admin.settings.attendance-time.update') }}" class="space-y-6"
      x-data="{
          loading: false,
          startHour: @js(old('attendance_start_hour', $startHour)),
          startMinute: @js(old('attendance_start_minute', $startMinute)),
          endHour: @js(old('attendance_end_hour', $endHour)),
          endMinute: @js(old('attendance_end_minute', $endMinute)),
          lateTolerance: @js((string) old('attendance_late_tolerance_minutes', $lateToleranceMinutes)),
          get lateUntil() {
              const totalMinutes = Math.max(0, (Number(this.endHour) * 60) + Number(this.endMinute) - Number(this.lateTolerance));
              const hour = String(Math.floor(totalMinutes / 60)).padStart(2, '0');
              const minute = String(totalMinutes % 60).padStart(2, '0');

              return `${hour}:${minute}`;
          }
      }" @submit="loading = true">
    @csrf
    @method('PUT')

    <div class="rounded-xl border border-gray-200 bg-white p-6">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Jam Mulai Absen <span class="text-red-500">*</span>
                </label>
                <div class="mt-1.5 flex items-center gap-2">
                    <select name="attendance_start_hour"
                            x-model="startHour"
                            required
                            aria-label="Jam mulai absen"
                            class="block w-24 rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm transition-colors focus:border-primary focus:ring-2 focus:ring-primary/20">
                        @foreach(range(0, 23) as $hour)
                            @php($value = sprintf('%02d', $hour))
                            <option value="{{ $value }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <span class="font-semibold text-gray-400">:</span>
                    <select name="attendance_start_minute"
                            x-model="startMinute"
                            required
                            aria-label="Menit mulai absen"
                            class="block w-24 rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm transition-colors focus:border-primary focus:ring-2 focus:ring-primary/20">
                        @foreach(range(0, 59) as $minute)
                            @php($value = sprintf('%02d', $minute))
                            <option value="{{ $value }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                @error('attendance_start_hour')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('attendance_start_minute')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Jam Selesai Absen <span class="text-red-500">*</span>
                </label>
                <div class="mt-1.5 flex items-center gap-2">
                    <select name="attendance_end_hour"
                            x-model="endHour"
                            required
                            aria-label="Jam selesai absen"
                            class="block w-24 rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm transition-colors focus:border-primary focus:ring-2 focus:ring-primary/20">
                        @foreach(range(0, 23) as $hour)
                            @php($value = sprintf('%02d', $hour))
                            <option value="{{ $value }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <span class="font-semibold text-gray-400">:</span>
                    <select name="attendance_end_minute"
                            x-model="endMinute"
                            required
                            aria-label="Menit selesai absen"
                            class="block w-24 rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm transition-colors focus:border-primary focus:ring-2 focus:ring-primary/20">
                        @foreach(range(0, 59) as $minute)
                            @php($value = sprintf('%02d', $minute))
                            <option value="{{ $value }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                @error('attendance_end_hour')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('attendance_end_minute')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="attendance_late_tolerance_minutes" class="block text-sm font-medium text-gray-700">
                    Toleransi Terlambat <span class="text-red-500">*</span>
                </label>
                <div class="mt-1.5 flex flex-wrap items-center gap-2">
                    <select id="attendance_late_tolerance_minutes"
                            name="attendance_late_tolerance_minutes"
                            x-model="lateTolerance"
                            required
                            class="block w-28 rounded-lg border border-gray-300 px-3 py-3 text-sm text-gray-900 shadow-sm transition-colors focus:border-primary focus:ring-2 focus:ring-primary/20">
                        @foreach($lateToleranceOptions as $option)
                            <option value="{{ $option }}" @selected((string) old('attendance_late_tolerance_minutes', $lateToleranceMinutes) === (string) $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                    <span class="text-sm text-gray-500">menit</span>
                </div>
                <p class="mt-1.5 text-sm text-gray-500">Toleransi keterlambatan hingga jam <span x-text="lateUntil"></span>.</p>

                @error('attendance_late_tolerance_minutes')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="flex items-center gap-4">
        <button type="submit" :disabled="loading"
                class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm
                       transition-colors hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 disabled:opacity-60">
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
