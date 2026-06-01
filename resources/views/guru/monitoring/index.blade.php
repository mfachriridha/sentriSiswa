@extends('layouts.app')

@section('title', 'Monitoring Siswa')

@section('content')
<div class="mb-5 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Monitoring Siswa</h1>
        <p class="mt-2 text-sm text-gray-500">Pantau kehadiran dan pelanggaran siswa secara keseluruhan.</p>
    </div>
</div>

<x-alert type="success" :message="session('success')" />
<x-alert type="error" :message="session('error')" />

<form method="GET" action="{{ route('guru.monitoring.index') }}" class="mb-4 rounded-xl border border-gray-200 bg-white p-4">
    <div class="grid grid-cols-1 gap-3 lg:grid-cols-6">
        <div class="relative lg:col-span-2">
            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, NISN, atau NIS..."
                   class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder-gray-400 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-colors">
        </div>

        <select name="class_id"
                class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-700 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-colors">
            <option value="">Semua Kelas</option>
            @foreach ($classes as $class)
                <option value="{{ $class->id }}" {{ $filterClass == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
            @endforeach
        </select>

        <div class="flex items-center gap-3 lg:col-span-3">
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-dark transition-colors">
                Cari
            </button>
            @if($search || $filterClass)
                <a href="{{ route('guru.monitoring.index') }}" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                    Reset
                </a>
            @endif
        </div>
    </div>
</form>

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50">
                <tr>
                    <th class="px-4 py-3 font-semibold text-gray-600">Nama</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Kelas</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Status</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Kehadiran (%)</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Sisa Poin</th>
                    <th class="px-4 py-3 font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($students as $student)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $student->user->name }}</div>
                            <div class="mt-1 text-sm text-gray-500">{{ $student->nisn }} / {{ $student->nis }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $student->class->name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            @php
                                $todayAttendance = $student->attendances->first();
                                $status = $todayAttendance ? $todayAttendance->status : 'none';
                            @endphp
                            @if($status === 'present')
                                <span class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-sm font-medium text-green-700">Hadir</span>
                            @elseif($status === 'late')
                                <span class="inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-sm font-medium text-amber-700">Terlambat</span>
                            @elseif($status === 'sick')
                                <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-sm font-medium text-blue-700">Sakit</span>
                            @elseif($status === 'permission')
                                <span class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-sm font-medium text-indigo-700">Izin</span>
                            @elseif($status === 'absent')
                                <span class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-sm font-medium text-red-700">Alpa</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-600">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $total = $student->total_attendances ?? 0;
                                $present = $student->present_attendances ?? 0;
                                $percentage = $total > 0 ? round(($present / $total) * 100) : 0;
                            @endphp
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-gray-900">{{ $percentage }}%</span>
                                @if($total > 0)
                                    <span class="text-sm text-gray-500">({{ $present }}/{{ $total }})</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $points = $student->points;
                                $colorClass = $points > 75 ? 'text-green-600' : ($points > 50 ? 'text-amber-600' : 'text-red-600');
                            @endphp
                            <span class="font-bold {{ $colorClass }}">{{ $points }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('guru.monitoring.show', $student) }}" class="font-medium text-primary hover:text-primary-dark transition-colors">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center text-sm text-gray-500">Tidak ada data siswa ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($students->hasPages())
        <div class="border-t border-gray-200 px-4 py-3">
            {{ $students->links() }}
        </div>
    @endif
</div>
@endsection