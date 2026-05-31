@extends('layouts.app')

@section('title', 'Rekap Absensi')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Rekap Absensi</h1>
    <p class="mt-1 text-base text-gray-500">Kelola absensi siswa kelas {{ $class->name }}</p>
</div>

<x-alert type="success" :message="session('success')" />
<x-alert type="info" :message="session('info')" />

<div class="rounded-xl border border-gray-200 bg-white p-6">
    {{-- Mode Toggle --}}
    <div class="mb-6 flex items-center gap-2">
        <a href="{{ route('guru.absensi.index', ['mode' => 'daily', 'date' => $date]) }}"
           class="rounded-lg px-4 py-2 text-sm font-medium transition-colors
                  {{ $mode === 'daily' ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Harian
        </a>
        <a href="{{ route('guru.absensi.index', ['mode' => 'monthly', 'month' => $month]) }}"
           class="rounded-lg px-4 py-2 text-sm font-medium transition-colors
                  {{ $mode === 'monthly' ? 'bg-primary text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Bulanan
        </a>
    </div>

    {{-- Filters --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <form method="GET" action="{{ route('guru.absensi.index') }}" class="flex gap-2">
            <input type="hidden" name="mode" value="{{ $mode }}">

            @if($mode === 'daily')
                <input type="date" name="date" value="{{ $date }}"
                       class="rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
            @else
                <input type="month" name="month" value="{{ $month }}"
                       class="rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
            @endif

            <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark">
                Terapkan
            </button>
        </form>

        <div class="flex gap-2">
            <a href="{{ route('guru.absensi.export-excel', ['mode' => $mode, 'date' => $date, 'month' => $month]) }}"
               class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Export Excel
            </a>
            <a href="{{ route('guru.absensi.export-pdf', ['mode' => $mode, 'date' => $date, 'month' => $month]) }}"
               class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                Export PDF
            </a>
        </div>
    </div>

    {{-- Stats --}}
    @if($mode === 'daily')
        <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-center">
                <p class="text-2xl font-bold text-green-700">{{ $stats['hadir'] }}</p>
                <p class="mt-1 text-xs font-medium text-green-600">Hadir</p>
            </div>
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-center">
                <p class="text-2xl font-bold text-amber-700">{{ $stats['terlambat'] }}</p>
                <p class="mt-1 text-xs font-medium text-amber-600">Terlambat</p>
            </div>
            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 text-center">
                <p class="text-2xl font-bold text-blue-700">{{ $stats['izin'] }}</p>
                <p class="mt-1 text-xs font-medium text-blue-600">Izin</p>
            </div>
            <div class="rounded-lg border border-purple-200 bg-purple-50 p-4 text-center">
                <p class="text-2xl font-bold text-purple-700">{{ $stats['sakit'] }}</p>
                <p class="mt-1 text-xs font-medium text-purple-600">Sakit</p>
            </div>
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-center">
                <p class="text-2xl font-bold text-red-700">{{ $stats['alpha'] }}</p>
                <p class="mt-1 text-xs font-medium text-red-600">Alpha</p>
            </div>
            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 text-center">
                <p class="text-2xl font-bold text-gray-700">{{ $stats['belum_absen'] }}</p>
                <p class="mt-1 text-xs font-medium text-gray-600">Belum Absen</p>
            </div>
        </div>
    @endif

    {{-- Table --}}
    <div class="overflow-x-auto">
        @if($mode === 'daily')
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">NISN</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Jam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($students as $index => $student)
                        @php
                            $attendance = $attendances->get($student->id);
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ $student->user->name }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ $student->nisn ?? '-' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                @if($attendance)
                                    @php
                                        $statusConfig = [
                                            'hadir' => ['bg-green-100 text-green-800', 'Hadir'],
                                            'terlambat' => ['bg-amber-100 text-amber-800', 'Terlambat'],
                                            'izin' => ['bg-blue-100 text-blue-800', 'Izin'],
                                            'sakit' => ['bg-purple-100 text-purple-800', 'Sakit'],
                                            'alpha' => ['bg-red-100 text-red-800', 'Alpha'],
                                        ];
                                        [$badgeClass, $statusLabel] = $statusConfig[$attendance->status] ?? ['bg-gray-100 text-gray-800', $attendance->status];
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $badgeClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">
                                        Belum Absen
                                    </span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                {{ $attendance?->check_in_time?->format('H:i') ?? '-' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm">
                                @if($attendance)
                                    <button onclick="openEditModal({{ $attendance->id }}, '{{ $attendance->status }}', '{{ $student->user->name }}')"
                                            class="text-primary hover:text-primary-dark font-medium">
                                        Edit
                                    </button>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">NISN</th>
                        <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">H</th>
                        <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">T</th>
                        <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">I</th>
                        <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">S</th>
                        <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">A</th>
                        <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">%</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($students as $index => $student)
                        @php
                            $stat = $stats[$student->id] ?? ['hadir' => 0, 'terlambat' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0, 'percentage' => 0];
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">{{ $index + 1 }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">{{ $student->user->name }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">{{ $student->nisn ?? '-' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-center text-sm text-green-600 font-medium">{{ $stat['hadir'] }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-center text-sm text-amber-600 font-medium">{{ $stat['terlambat'] }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-center text-sm text-blue-600 font-medium">{{ $stat['izin'] }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-center text-sm text-purple-600 font-medium">{{ $stat['sakit'] }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-center text-sm text-red-600 font-medium">{{ $stat['alpha'] }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-center text-sm font-medium
                                {{ $stat['percentage'] >= 80 ? 'text-green-600' : ($stat['percentage'] >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                                {{ $stat['percentage'] }}%
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

{{-- Edit Modal --}}
<div id="editModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-gray-900/60 p-4">
    <div class="w-full max-w-md rounded-2xl bg-white shadow-xl">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900">Edit Status Absensi</h3>
            <p id="modalStudentName" class="mt-1 text-sm text-gray-500"></p>
        </div>

        <form id="editForm" method="POST" class="px-6 py-5">
            @csrf
            @method('PUT')

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select id="status" name="status"
                        class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
                    <option value="hadir">Hadir</option>
                    <option value="terlambat">Terlambat</option>
                    <option value="izin">Izin</option>
                    <option value="sakit">Sakit</option>
                    <option value="alpha">Alpha</option>
                </select>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeEditModal()"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                        class="rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white hover:bg-primary-dark">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openEditModal(attendanceId, currentStatus, studentName) {
        document.getElementById('editForm').action = `/guru/absensi/${attendanceId}`;
        document.getElementById('status').value = currentStatus;
        document.getElementById('modalStudentName').textContent = studentName;
        document.getElementById('editModal').classList.remove('hidden');
        document.getElementById('editModal').classList.add('flex');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.getElementById('editModal').classList.remove('flex');
    }

    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditModal();
        }
    });
</script>
@endpush
@endsection
