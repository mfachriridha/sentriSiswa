@extends('layouts.app')

@section('title', 'Rekap Absensi')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Rekap Absensi</h1>
    <p class="mt-1 text-sm text-gray-500">Laporan absensi siswa kelas {{ $class->name }}</p>
</div>

<div class="rounded-xl border border-gray-200 bg-white p-6">
    <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <form method="GET" action="{{ route('guru.absensi.index') }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-6">
            <div>
                <label for="month" class="block text-sm font-medium text-gray-700">Bulan</label>
                <input id="month" type="month" name="month" value="{{ $selectedMonth }}"
                       class="mt-1 w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
            </div>
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                <input id="start_date" type="date" name="start_date" value="{{ $startDate }}"
                       class="mt-1 w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                <input id="end_date" type="date" name="end_date" value="{{ $endDate }}"
                       class="mt-1 w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
            </div>
            <div>
                <label for="student_id" class="block text-sm font-medium text-gray-700">Siswa</label>
                <select id="student_id" name="student_id" class="mt-1 w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                    <option value="">Semua Siswa</option>
                    @foreach ($filterStudents as $filterStudent)
                        <option value="{{ $filterStudent->id }}" {{ $selectedStudent == $filterStudent->id ? 'selected' : '' }}>{{ $filterStudent->user?->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select id="status" name="status" class="mt-1 w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                    <option value="">Semua Status</option>
                    @foreach (['hadir' => 'Hadir', 'terlambat' => 'Terlambat', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpha' => 'Alpha'] as $value => $label)
                        <option value="{{ $value }}" {{ $statusFilter === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="self-end rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark">
                Terapkan
            </button>
        </form>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('guru.absensi.export-excel', request()->query()) }}"
               class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Export Excel
            </a>
            <a href="{{ route('guru.absensi.export-pdf', request()->query()) }}"
               class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Export PDF
            </a>
        </div>
    </div>

    @error('start_date')
        <p class="mb-4 text-sm text-red-600">{{ $message }}</p>
    @enderror
    @error('end_date')
        <p class="mb-4 text-sm text-red-600">{{ $message }}</p>
    @enderror

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2.5 text-left text-xs font-medium uppercase tracking-wider text-gray-500">No</th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium uppercase tracking-wider text-gray-500">NIS</th>
                    <th class="px-4 py-2.5 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nama</th>
                    <th class="px-4 py-2.5 text-center text-xs font-medium uppercase tracking-wider text-gray-500">H</th>
                    <th class="px-4 py-2.5 text-center text-xs font-medium uppercase tracking-wider text-gray-500">T</th>
                    <th class="px-4 py-2.5 text-center text-xs font-medium uppercase tracking-wider text-gray-500">I</th>
                    <th class="px-4 py-2.5 text-center text-xs font-medium uppercase tracking-wider text-gray-500">S</th>
                    <th class="px-4 py-2.5 text-center text-xs font-medium uppercase tracking-wider text-gray-500">A</th>
                    <th class="px-4 py-2.5 text-center text-xs font-medium uppercase tracking-wider text-gray-500">%</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($students as $index => $student)
                    @php
                        $stat = $stats[$student->id];
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-900">{{ $index + 1 }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-500">{{ $student->nis ?? '-' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ $student->user->name }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-center text-sm font-medium text-green-600">{{ $stat['hadir'] }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-center text-sm font-medium text-amber-600">{{ $stat['terlambat'] }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-center text-sm font-medium text-blue-600">{{ $stat['izin'] }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-center text-sm font-medium text-purple-600">{{ $stat['sakit'] }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-center text-sm font-medium text-red-600">{{ $stat['alpha'] }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-center text-sm font-medium
                            {{ $stat['percentage'] >= 80 ? 'text-green-600' : ($stat['percentage'] >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $stat['percentage'] }}%
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-sm text-gray-500">Belum ada siswa di kelas ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5 flex flex-wrap gap-4 text-xs text-gray-500">
        <span>H: Hadir</span>
        <span>T: Terlambat</span>
        <span>I: Izin</span>
        <span>S: Sakit</span>
        <span>A: Alpha</span>
    </div>
</div>
@endsection
