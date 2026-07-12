@extends('layouts.app')

@section('title', 'Waktu Absen')

@section('content')
@php
    [$startHour, $startMinute] = explode(':', $startTime);
    [$endHour, $endMinute] = explode(':', $endTime);
@endphp

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Waktu Absen</h1>
    <p class="mt-1 text-sm text-gray-500">Konfigurasi jam mulai, jam selesai, dan hari aktif absensi.</p>
</div>

{{-- Tanpa ini, admin menyimpan konfigurasi lalu tidak melihat konfirmasi apa
     pun, padahal sistem sudah mengirim pesannya. --}}
<x-alert type="success" :message="session('success')" />

<div class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <p class="text-sm font-semibold text-green-800">Konfigurasi tersimpan</p>
            <p class="mt-1 text-sm text-green-700">
                Jam mulai {{ $startTime }}, selesai {{ $endTime }}.
            </p>
            <p class="mt-1 text-sm text-green-700">Hari aktif: {{ $activeDaysLabel }}.</p>
        </div>
        <div class="text-sm text-green-700">
            @if($updatedAt)
                Terakhir disimpan {{ \Illuminate\Support\Carbon::parse($updatedAt)->translatedFormat('d F Y H:i') }}
            @else
                Menggunakan konfigurasi bawaan
            @endif
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.pengaturan.waktu-absen.update') }}" class="space-y-6"
      x-data="{
          loading: false,
          startHour: @js(old('attendance_start_hour', $startHour)),
          startMinute: @js(old('attendance_start_minute', $startMinute)),
          endHour: @js(old('attendance_end_hour', $endHour)),
          endMinute: @js(old('attendance_end_minute', $endMinute)),
      }"
      x-effect="
          if ((Number(endHour) * 60 + Number(endMinute)) < (Number(startHour) * 60 + Number(startMinute))) {
              endHour = startHour;
              endMinute = startMinute;
          }
      "
      @submit="loading = true">
    @csrf
    @method('PUT')

    <div class="rounded-xl border border-gray-200 bg-white p-6">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
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
                            @php
                                $value = sprintf('%02d', $hour);
                            @endphp
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
                            @php
                                $value = sprintf('%02d', $minute);
                            @endphp
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
                            @php
                                $value = sprintf('%02d', $hour);
                            @endphp
                            <option value="{{ $value }}" :disabled="{{ $hour }} < Number(startHour)">{{ $value }}</option>
                        @endforeach
                    </select>
                    <span class="font-semibold text-gray-400">:</span>
                    <select name="attendance_end_minute"
                            x-model="endMinute"
                            required
                            aria-label="Menit selesai absen"
                            class="block w-24 rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm transition-colors focus:border-primary focus:ring-2 focus:ring-primary/20">
                        @foreach(range(0, 59) as $minute)
                            @php
                                $value = sprintf('%02d', $minute);
                            @endphp
                            <option value="{{ $value }}" :disabled="Number(endHour) === Number(startHour) && {{ $minute }} < Number(startMinute)">{{ $value }}</option>
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

        </div>

        <div class="mt-6 border-t border-gray-100 pt-6">
            <label class="block text-sm font-medium text-gray-700">
                Hari Aktif Absensi <span class="text-red-500">*</span>
            </label>
            <p class="mt-1 text-sm text-gray-500">Absensi cuma bisa dilakukan di hari yang dicentang.</p>

            @php
                $selectedDays = array_map('intval', old('attendance_active_days', $activeDays));
            @endphp

            <div class="mt-3 flex flex-wrap gap-2">
                @foreach ($dayNames as $value => $label)
                    <label class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 transition-colors hover:bg-gray-50">
                        <input type="checkbox" name="attendance_active_days[]" value="{{ $value }}"
                               {{ in_array($value, $selectedDays, true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-primary focus:ring-primary/30">
                        {{ $label }}
                    </label>
                @endforeach
            </div>

            @error('attendance_active_days')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="flex items-center gap-4">
        <button type="submit" :disabled="loading"
                class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm
                       transition-colors hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 disabled:opacity-60">
            <span x-cloak x-show="loading">
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
