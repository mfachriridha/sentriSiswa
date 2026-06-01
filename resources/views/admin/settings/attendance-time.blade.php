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
          activePicker: null,
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

    <input type="hidden" name="attendance_start_hour" x-model="startHour">
    <input type="hidden" name="attendance_start_minute" x-model="startMinute">
    <input type="hidden" name="attendance_end_hour" x-model="endHour">
    <input type="hidden" name="attendance_end_minute" x-model="endMinute">

    <div class="rounded-xl border border-gray-200 bg-white p-6">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="relative" @click.outside="activePicker === 'start' && (activePicker = null)">
                <label class="block text-sm font-medium text-gray-700">
                    Jam Mulai Absen <span class="text-red-500">*</span>
                </label>
                <button type="button"
                        @click="activePicker = activePicker === 'start' ? null : 'start'"
                        class="mt-1.5 inline-flex w-36 items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-900 shadow-sm transition-colors hover:bg-gray-50 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                    <span x-text="startHour"></span>
                    <span class="text-gray-400">:</span>
                    <span x-text="startMinute"></span>
                </button>

                <div x-show="activePicker === 'start'" x-transition
                     class="absolute z-20 mt-2 w-72 rounded-xl border border-gray-200 bg-white p-4 shadow-lg">
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Jam</p>
                    <div class="grid grid-cols-6 gap-1">
                        @foreach(range(0, 23) as $hour)
                            @php($value = sprintf('%02d', $hour))
                            <button type="button"
                                    @click="startHour = @js($value)"
                                    :class="startHour === @js($value) ? 'bg-primary text-white' : 'bg-gray-50 text-gray-700 hover:bg-gray-100'"
                                    class="rounded-md px-2 py-1.5 text-sm font-medium transition-colors">{{ $value }}</button>
                        @endforeach
                    </div>

                    <p class="mb-2 mt-4 text-xs font-semibold uppercase tracking-wide text-gray-500">Menit</p>
                    <div class="grid grid-cols-10 gap-1">
                        @foreach(range(0, 59) as $minute)
                            @php($value = sprintf('%02d', $minute))
                            <button type="button"
                                    @click="startMinute = @js($value)"
                                    :class="startMinute === @js($value) ? 'bg-primary text-white' : 'bg-gray-50 text-gray-700 hover:bg-gray-100'"
                                    class="rounded-md px-1.5 py-1.5 text-xs font-medium transition-colors">{{ $value }}</button>
                        @endforeach
                    </div>
                </div>

                @error('attendance_start_hour')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('attendance_start_minute')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="relative" @click.outside="activePicker === 'end' && (activePicker = null)">
                <label class="block text-sm font-medium text-gray-700">
                    Jam Selesai Absen <span class="text-red-500">*</span>
                </label>
                <button type="button"
                        @click="activePicker = activePicker === 'end' ? null : 'end'"
                        class="mt-1.5 inline-flex w-36 items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-900 shadow-sm transition-colors hover:bg-gray-50 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                    <span x-text="endHour"></span>
                    <span class="text-gray-400">:</span>
                    <span x-text="endMinute"></span>
                </button>

                <div x-show="activePicker === 'end'" x-transition
                     class="absolute z-20 mt-2 w-72 rounded-xl border border-gray-200 bg-white p-4 shadow-lg md:left-0">
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Jam</p>
                    <div class="grid grid-cols-6 gap-1">
                        @foreach(range(0, 23) as $hour)
                            @php($value = sprintf('%02d', $hour))
                            <button type="button"
                                    @click="endHour = @js($value)"
                                    :class="endHour === @js($value) ? 'bg-primary text-white' : 'bg-gray-50 text-gray-700 hover:bg-gray-100'"
                                    class="rounded-md px-2 py-1.5 text-sm font-medium transition-colors">{{ $value }}</button>
                        @endforeach
                    </div>

                    <p class="mb-2 mt-4 text-xs font-semibold uppercase tracking-wide text-gray-500">Menit</p>
                    <div class="grid grid-cols-10 gap-1">
                        @foreach(range(0, 59) as $minute)
                            @php($value = sprintf('%02d', $minute))
                            <button type="button"
                                    @click="endMinute = @js($value)"
                                    :class="endMinute === @js($value) ? 'bg-primary text-white' : 'bg-gray-50 text-gray-700 hover:bg-gray-100'"
                                    class="rounded-md px-1.5 py-1.5 text-xs font-medium transition-colors">{{ $value }}</button>
                        @endforeach
                    </div>
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
