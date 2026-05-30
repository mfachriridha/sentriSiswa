@extends('layouts.app')

@section('title', 'Monitoring Siswa')

@section('content')
<div class="mb-6 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
    <h1 class="text-2xl font-bold text-gray-900">Monitoring Siswa</h1>
</div>

<div class="rounded-xl border border-gray-200 bg-white">
    <!-- Filter and Search -->
    <div class="border-b border-gray-200 p-4 sm:p-6">
        <form method="GET" action="{{ route('guru.monitoring.index') }}" class="flex w-full flex-col gap-4 sm:flex-row">
            <div class="w-full sm:w-1/3">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, NISN, atau NIS..." class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20">
            </div>
            <div class="w-full sm:w-1/4">
                <select name="class_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary/20" onchange="this.form.submit()">
                    <option value="">-- Semua Kelas --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $filterClass == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-dark transition-colors">
                    Cari
                </button>
                @if($search || $filterClass)
                    <a href="{{ route('guru.monitoring.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full whitespace-nowrap text-left text-sm text-gray-600">
            <thead class="bg-gray-50/50 text-xs uppercase text-gray-500">
                <tr>
                    <th scope="col" class="px-6 py-4 font-semibold">Nama</th>
                    <th scope="col" class="px-6 py-4 font-semibold">Kelas</th>
                    <th scope="col" class="px-6 py-4 font-semibold">Status</th>
                    <th scope="col" class="px-6 py-4 font-semibold">Kehadiran (%)</th>
                    <th scope="col" class="px-6 py-4 font-semibold">Sisa Poin</th>
                    <th scope="col" class="px-6 py-4 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($students as $student)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $student->user->name }}</div>
                            <div class="text-xs text-gray-500">{{ $student->nisn }} / {{ $student->nis }}</div>
                        </td>
                        <td class="px-6 py-4">{{ $student->class->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            @php
                                $todayAttendance = $student->attendances->first();
                                $status = $todayAttendance ? $todayAttendance->status : 'none';
                            @endphp
                            @if($status === 'present')
                                <span class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700">Hadir</span>
                            @elseif($status === 'late')
                                <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-700">Terlambat</span>
                            @elseif($status === 'sick')
                                <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700">Sakit</span>
                            @elseif($status === 'permission')
                                <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700">Izin</span>
                            @elseif($status === 'absent')
                                <span class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-medium text-red-700">Alpa</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $total = $student->total_attendances ?? 0;
                                $present = $student->present_attendances ?? 0;
                                $percentage = $total > 0 ? round(($present / $total) * 100) : 0;
                            @endphp
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-gray-900">{{ $percentage }}%</span>
                                @if($total > 0)
                                    <span class="text-xs text-gray-500">({{ $present }}/{{ $total }})</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $points = $student->points;
                                $colorClass = $points > 75 ? 'text-green-600' : ($points > 50 ? 'text-amber-600' : 'text-red-600');
                            @endphp
                            <span class="font-bold {{ $colorClass }}">{{ $points }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('guru.monitoring.show', $student) }}" class="font-medium text-primary hover:text-primary-dark transition-colors">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">Tidak ada data siswa ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if ($students->hasPages())
        <div class="border-t border-gray-200 px-6 py-4">
            {{ $students->links() }}
        </div>
    @endif
</div>
@endsection
