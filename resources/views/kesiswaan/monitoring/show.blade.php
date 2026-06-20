@extends('layouts.app')

@section('title', 'Detail Siswa: ' . $student->user->name)

@section('content')
@php
    $biodata = $student->biodata;
    $backLabel = $backLabel ?? 'Kembali ke Monitoring';
    $statusLabels = [
        'hadir' => ['Hadir', 'bg-green-50 text-green-700'],
        'terlambat' => ['Terlambat', 'bg-amber-50 text-amber-700'],
        'sakit' => ['Sakit', 'bg-blue-50 text-blue-700'],
        'izin' => ['Izin', 'bg-indigo-50 text-indigo-700'],
        'alpha' => ['Alpha', 'bg-red-50 text-red-700'],
        'belum_absen' => ['Belum Absen', 'bg-gray-100 text-gray-600'],
    ];
    $biodataRows = [
        ['Tempat Lahir', $biodata?->place_of_birth],
        ['Tanggal Lahir', $biodata?->date_of_birth?->translatedFormat('d F Y')],
        ['Jenis Kelamin', $biodata?->gender === 'L' ? 'Laki-laki' : ($biodata?->gender === 'P' ? 'Perempuan' : null)],
        ['Agama', $biodata?->religion],
        ['Status Keluarga', $biodata?->family_status],
        ['Anak Ke', $biodata?->child_number],
        ['Asal Sekolah', $biodata?->school_of_origin],
        ['Tanggal Masuk', $biodata?->admission_date?->translatedFormat('d F Y')],
        ['Ayah', $biodata?->father_name],
        ['Pekerjaan Ayah', $biodata?->father_occupation],
        ['Ibu', $biodata?->mother_name],
        ['Pekerjaan Ibu', $biodata?->mother_occupation],
        ['Alamat Orang Tua', $biodata?->parent_address],
        ['Telepon Orang Tua', $biodata?->parent_phone],
        ['Wali', $biodata?->guardian_name],
        ['Pekerjaan Wali', $biodata?->guardian_occupation],
        ['Alamat Wali', $biodata?->guardian_address],
        ['Telepon Wali', $biodata?->guardian_phone],
    ];
@endphp

<div class="mb-6">
    <a href="{{ $backRoute ?? route('kesiswaan.monitoring.index') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        {{ $backLabel }}
    </a>
</div>

<x-alert type="success" :message="session('success')" />
<x-alert type="error" :message="session('error')" />

<div class="mb-5 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Detail Siswa</h1>
        <p class="mt-2 text-sm text-gray-500">{{ $student->user->name }} · {{ $student->class->name ?? '-' }}</p>
        @if(($alphaWarningCount ?? 0) >= ($warningThreshold ?? 3))
            <p class="mt-2 inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">
                Peringatan alpha {{ $alphaWarningCount }} kali pada semester berjalan
            </p>
        @endif
    </div>
    @if(! empty($createViolationRoute))
        <a href="{{ $createViolationRoute }}" class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Catat Pelanggaran
        </a>
    @endif
</div>

<div class="space-y-6">
    <div class="rounded-xl border border-gray-200 bg-white p-6">
        <div class="flex flex-col gap-6 md:flex-row md:items-center">
            @if ($student->photo)
                <img src="{{ asset('storage/'.$student->photo) }}" alt="{{ $student->user->name }}" class="h-24 w-24 rounded-full border border-gray-200 object-cover">
            @else
                <div class="flex h-24 w-24 shrink-0 items-center justify-center rounded-full bg-primary/10 text-3xl font-bold text-primary">
                    {{ strtoupper(substr($student->user->name, 0, 1)) }}
                </div>
            @endif

            <div class="grid flex-1 grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <span class="block text-sm font-medium text-gray-500">Nama Lengkap</span>
                    <span class="mt-1 block text-sm font-medium text-gray-900">{{ $student->user->name }}</span>
                </div>
                <div>
                    <span class="block text-sm font-medium text-gray-500">NISN / NIS</span>
                    <span class="mt-1 block text-sm font-medium text-gray-900">{{ $student->nisn ?? '-' }} / {{ $student->nis ?? '-' }}</span>
                </div>
                <div>
                    <span class="block text-sm font-medium text-gray-500">Kelas</span>
                    <span class="mt-1 block text-sm font-medium text-gray-900">{{ $student->class->name ?? '-' }}</span>
                </div>
                <div>
                    <span class="block text-sm font-medium text-gray-500">Sisa Poin</span>
                    @php $points = $student->points; @endphp
                    <span class="mt-1 block text-2xl font-bold {{ $points > 75 ? 'text-green-600' : ($points > 50 ? 'text-amber-600' : 'text-red-600') }}">{{ $points }}</span>
                </div>
                <div>
                    <span class="block text-sm font-medium text-gray-500">Telepon</span>
                    <span class="mt-1 block text-sm text-gray-900">{{ $student->phone ?? '-' }}</span>
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <span class="block text-sm font-medium text-gray-500">Alamat</span>
                    <span class="mt-1 block text-sm text-gray-900">{{ $student->address ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-6">
        <h2 class="text-lg font-semibold text-gray-900">Biodata Lengkap</h2>
        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($biodataRows as [$label, $value])
                <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ $label }}</p>
                    <p class="mt-1 text-sm text-gray-900">{{ filled($value) ? $value : '-' }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-gray-200 bg-white">
            <div class="border-b border-gray-200 p-5">
                <h3 class="text-lg font-semibold text-gray-900">Riwayat Pelanggaran</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Tanggal</th>
                            <th class="px-4 py-3 font-semibold">Pelanggaran</th>
                            <th class="px-4 py-3 text-center font-semibold">Poin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($student->studentViolations as $violation)
                            <tr>
                                <td class="whitespace-nowrap px-4 py-3">{{ $violation->violation_date->translatedFormat('d F Y') }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $violation->violation_name }}</div>
                                    @if($violation->notes)
                                        <div class="mt-1 text-xs text-gray-500">{{ $violation->notes }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center font-bold text-red-600">-{{ $violation->point_deduction }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-500">Belum ada catatan pelanggaran approved.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white">
            <div class="border-b border-gray-200 p-5">
                <h3 class="text-lg font-semibold text-gray-900">Riwayat Kehadiran</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Tanggal</th>
                            <th class="px-4 py-3 font-semibold">Status</th>
                            <th class="px-4 py-3 font-semibold">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($student->attendances as $attendance)
                            @php $statusMeta = $statusLabels[$attendance->status] ?? ['-', 'bg-gray-100 text-gray-600']; @endphp
                            <tr>
                                <td class="whitespace-nowrap px-4 py-3">{{ $attendance->date->translatedFormat('d F Y') }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusMeta[1] }}">{{ $statusMeta[0] }}</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    {{ $attendance->check_in_time ? $attendance->check_in_time->format('H:i') : '-' }}
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
