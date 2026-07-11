@extends('layouts.app')

@section('title', 'Detail Siswa: ' . $student->pengguna->nama)

@section('content')
@php
    $backLabel = $backLabel ?? 'Kembali ke Monitoring';
    $statusLabels = [
        'hadir' => ['Hadir', 'bg-green-50 text-green-700'],
        'terlambat' => ['Terlambat', 'bg-amber-50 text-amber-700'],
        'sakit' => ['Sakit', 'bg-blue-50 text-blue-700'],
        'izin' => ['Izin', 'bg-indigo-50 text-indigo-700'],
        'alpha' => ['Alpha', 'bg-red-50 text-red-700'],
        'belum_absen' => ['Belum Absen', 'bg-gray-100 text-gray-600'],
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
        <p class="mt-2 text-sm text-gray-500">{{ $student->pengguna->nama }} · {{ $student->kelas->nama ?? '-' }}</p>
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
            @if ($student->foto)
                <img src="{{ asset('storage/'.$student->foto) }}" alt="{{ $student->pengguna->nama }}" class="h-24 w-24 rounded-full border border-gray-200 object-cover">
            @else
                <div class="flex h-24 w-24 shrink-0 items-center justify-center rounded-full bg-primary/10 text-3xl font-bold text-primary">
                    {{ strtoupper(substr($student->pengguna->nama, 0, 1)) }}
                </div>
            @endif

            <div class="grid flex-1 grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <span class="block text-sm font-medium text-gray-500">Nama Lengkap</span>
                    <span class="mt-1 block text-sm font-medium text-gray-900">{{ $student->pengguna->nama }}</span>
                </div>
                <div>
                    <span class="block text-sm font-medium text-gray-500">NISN / NIS</span>
                    <span class="mt-1 block text-sm font-medium text-gray-900">{{ $student->nisn ?? '-' }} / {{ $student->nis ?? '-' }}</span>
                </div>
                <div>
                    <span class="block text-sm font-medium text-gray-500">Kelas</span>
                    <span class="mt-1 block text-sm font-medium text-gray-900">{{ $student->kelas->nama ?? '-' }}</span>
                </div>
                <div>
                    <span class="block text-sm font-medium text-gray-500">Sisa Poin</span>
                    @php $points = $student->poin; @endphp
                    <span class="mt-1 block text-2xl font-bold {{ $points > 75 ? 'text-green-600' : ($points > 50 ? 'text-amber-600' : 'text-red-600') }}">{{ $points }}</span>
                </div>
                <div>
                    <span class="block text-sm font-medium text-gray-500">Telepon</span>
                    <span class="mt-1 block text-sm text-gray-900">{{ $student->telepon ?? '-' }}</span>
                </div>
                <div class="sm:col-span-2 lg:col-span-3">
                    <span class="block text-sm font-medium text-gray-500">Alamat</span>
                    <span class="mt-1 block text-sm text-gray-900">{{ $student->alamat ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-gray-200 bg-white">
            <div class="border-b border-gray-200 p-5">
                <h3 class="text-lg font-semibold text-gray-900">Riwayat Pelanggaran</h3>
            </div>
            <div class="max-h-96 overflow-y-auto overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="sticky top-0 z-10 bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Tanggal</th>
                            <th class="px-4 py-3 font-semibold">Pelanggaran</th>
                            <th class="px-4 py-3 text-center font-semibold">Poin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($student->pelanggaranSiswa as $violation)
                            <tr>
                                <td class="whitespace-nowrap px-4 py-3">{{ $violation->tanggal_pelanggaran->translatedFormat('d F Y') }}</td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $violation->nama_pelanggaran }}</div>
                                    @if($violation->catatan)
                                        <div class="mt-1 text-xs text-gray-500">{{ $violation->catatan }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center font-bold text-red-600">-{{ $violation->pengurangan_poin }}</td>
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
                <p class="mt-1 text-xs text-gray-500">30 catatan terakhir.</p>
            </div>
            <div class="max-h-96 overflow-y-auto overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="sticky top-0 z-10 bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Tanggal</th>
                            <th class="px-4 py-3 font-semibold">Status</th>
                            <th class="px-4 py-3 font-semibold">Waktu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($student->absensi as $attendance)
                            @php $statusMeta = $statusLabels[$attendance->status] ?? ['-', 'bg-gray-100 text-gray-600']; @endphp
                            <tr>
                                <td class="whitespace-nowrap px-4 py-3">{{ $attendance->tanggal->translatedFormat('d F Y') }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $statusMeta[1] }}">{{ $statusMeta[0] }}</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    {{ $attendance->waktu_masuk ? $attendance->waktu_masuk->format('H:i') : '-' }}
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

        <div class="rounded-xl border border-gray-200 bg-white lg:col-span-2">
            <div class="border-b border-gray-200 p-5">
                <h3 class="text-lg font-semibold text-gray-900">Riwayat Pengajuan Poin</h3>
            </div>
            <div class="max-h-96 overflow-y-auto overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="sticky top-0 z-10 bg-gray-50 text-xs uppercase text-gray-500">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Tanggal</th>
                            <th class="px-4 py-3 font-semibold">Alasan</th>
                            <th class="px-4 py-3 font-semibold">Diajukan Oleh</th>
                            <th class="px-4 py-3 text-center font-semibold">Poin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($student->pengajuanPoin as $pengajuan)
                            <tr>
                                <td class="whitespace-nowrap px-4 py-3">{{ $pengajuan->dibuat_pada->translatedFormat('d F Y') }}</td>
                                <td class="px-4 py-3">{{ $pengajuan->alasan }}</td>
                                <td class="px-4 py-3">{{ $pengajuan->diajukanOleh?->nama ?? '-' }}</td>
                                <td class="px-4 py-3 text-center font-bold text-green-600">{{ $pengajuan->jumlah_poin !== null ? '+'.$pengajuan->jumlah_poin : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada pengajuan poin.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
