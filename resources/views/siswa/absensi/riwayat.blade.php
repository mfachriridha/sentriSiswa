@extends('layouts.app')

@section('title', 'Riwayat Absensi')

@section('content')
<div class="mb-6">
    <a href="{{ route('siswa.absensi') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
</div>

<div class="rounded-xl border border-gray-200 bg-white p-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Riwayat Absensi</h1>
            <p class="mt-1 text-sm text-gray-500">Catatan kehadiran bulan {{ $monthLabel }}</p>
        </div>

        <form method="GET" action="{{ route('siswa.absensi.riwayat') }}" class="flex flex-wrap items-center gap-2">
            <label for="month" class="text-sm font-medium text-gray-600">Bulan</label>
            <select id="month"
                    name="month"
                    onchange="this.form.submit()"
                    class="rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20">
                @foreach ($monthOptions as $value => $label)
                    <option value="{{ $value }}" @selected($selectedMonth === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <noscript>
                <button type="submit"
                        class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white transition-colors hover:bg-primary-dark">
                    Filter
                </button>
            </noscript>
        </form>
    </div>

    <div class="mt-8 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Jam Absen</th>
                    <th class="px-4 py-3">Selfie</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($attendances as $attendance)
                    @php
                        $statusConfig = [
                            'hadir' => ['bg-green-50 text-green-700', 'Hadir'],
                            'izin' => ['bg-blue-50 text-blue-700', 'Izin'],
                            'sakit' => ['bg-purple-50 text-purple-700', 'Sakit'],
                            'alpha' => ['bg-red-50 text-red-700', 'Alpha'],
                        ];
                        [$badgeClass, $statusLabel] = $statusConfig[$attendance->status] ?? ['bg-gray-50 text-gray-700', $attendance->status];
                    @endphp
                    <tr>
                        <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-gray-900">
                            {{ $attendance->tanggal->translatedFormat('d F Y') }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-4">
                            <span class="inline-flex rounded-full {{ $badgeClass }} px-3 py-1 text-sm font-semibold">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-4 py-4 text-sm text-gray-600">
                            {{ $attendance->waktu_masuk?->format('H:i') ?? '-' }}
                        </td>
                        <td class="whitespace-nowrap px-4 py-4">
                            @if($attendance->path_selfie)
                                <a href="{{ asset('storage/'.$attendance->path_selfie) }}" target="_blank" class="inline-block">
                                    <img src="{{ asset('storage/'.$attendance->path_selfie) }}"
                                         alt="Selfie absensi {{ $attendance->tanggal->translatedFormat('d F Y') }}"
                                         class="h-14 w-14 rounded-lg border border-gray-200 object-cover">
                                </a>
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-10 text-center text-sm text-gray-500">
                            Belum ada catatan absensi pada bulan ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
