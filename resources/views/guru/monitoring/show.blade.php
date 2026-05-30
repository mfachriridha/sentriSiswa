@extends('layouts.app')

@section('title', 'Detail Monitoring: ' . $student->user->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('guru.monitoring.index') }}"
       class="inline-flex items-center gap-2 text-base text-gray-500 hover:text-gray-700 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Monitoring
    </a>
</div>

<div class="mb-6 flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
    <h1 class="text-2xl font-bold text-gray-900">Detail Monitoring Siswa</h1>
    <a href="{{ route('guru.pelanggaran-siswa.create', ['student_profile_id' => $student->id]) }}" class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        Catat Pelanggaran
    </a>
</div>

<div class="space-y-6">
    <!-- Profil Ringkas -->
    <div class="rounded-xl border border-gray-200 bg-white p-6">
        <h3 class="mb-4 text-lg font-bold text-gray-900">Profil Ringkas</h3>
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 md:grid-cols-4">
            <div>
                <span class="block text-sm font-medium text-gray-500">Nama Lengkap</span>
                <span class="mt-1 block text-base font-medium text-gray-900">{{ $student->user->name }}</span>
            </div>
            <div>
                <span class="block text-sm font-medium text-gray-500">NISN / NIS</span>
                <span class="mt-1 block text-base font-medium text-gray-900">{{ $student->nisn ?? '-' }} / {{ $student->nis ?? '-' }}</span>
            </div>
            <div>
                <span class="block text-sm font-medium text-gray-500">Kelas</span>
                <span class="mt-1 block text-base font-medium text-gray-900">{{ $student->class->name ?? '-' }}</span>
            </div>
            <div>
                <span class="block text-sm font-medium text-gray-500">Sisa Poin</span>
                @php
                    $points = $student->points;
                    $colorClass = $points > 75 ? 'text-green-600' : ($points > 50 ? 'text-amber-600' : 'text-red-600');
                @endphp
                <span class="mt-1 block text-2xl font-bold {{ $colorClass }}">{{ $points }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Riwayat Pelanggaran -->
        <div class="rounded-xl border border-gray-200 bg-white">
            <div class="border-b border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900">Riwayat Pelanggaran</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50/50 text-xs uppercase text-gray-500">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold">Tanggal</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Pelanggaran</th>
                            <th scope="col" class="px-6 py-4 text-center font-semibold">Poin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($student->studentViolations as $violation)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="whitespace-nowrap px-6 py-4">{{ \Carbon\Carbon::parse($violation->violation_date)->translatedFormat('d M Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $violation->violation_name }}</div>
                                    @if($violation->notes)
                                        <div class="mt-1 text-xs text-gray-500">{{ $violation->notes }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-red-600">-{{ $violation->point_deduction }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-500">Siswa ini belum memiliki catatan pelanggaran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Riwayat Kehadiran -->
        <div class="rounded-xl border border-gray-200 bg-white">
            <div class="border-b border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900">Riwayat Kehadiran (30 Hari Terakhir)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50/50 text-xs uppercase text-gray-500">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold">Tanggal</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Status</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($student->attendances as $attendance)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="whitespace-nowrap px-6 py-4">{{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('d M Y') }}</td>
                                <td class="px-6 py-4">
                                    @if($attendance->status === 'present')
                                        <span class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700">Hadir</span>
                                    @elseif($attendance->status === 'late')
                                        <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-700">Terlambat</span>
                                    @elseif($attendance->status === 'sick')
                                        <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700">Sakit</span>
                                    @elseif($attendance->status === 'permission')
                                        <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700">Izin</span>
                                    @elseif($attendance->status === 'absent')
                                        <span class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-0.5 text-xs font-medium text-red-700">Alpa</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    {{ $attendance->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('H:i') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-500">Belum ada data kehadiran.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
