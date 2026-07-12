@extends('layouts.cetak')

{{-- Judul halaman dipakai peramban sebagai nama berkas bawaan saat disimpan jadi PDF. --}}
@section('title', $judul.' - '.$startDate.' sampai '.$endDate)
@section('judul-cetak', $judul)

@section('content')
<header class="mb-6 border-b border-slate-200 pb-4">
    <h1 class="text-xl font-bold text-slate-900">{{ $judul }}</h1>
    <p class="mt-1 text-sm text-slate-500">
        {{ $subjudul }} · {{ $startDate }} sampai {{ $endDate }}
    </p>
    <p class="mt-0.5 text-xs text-slate-400">
        Dicetak {{ now()->locale('id')->translatedFormat('d F Y H:i') }}
    </p>
</header>

<table class="w-full border-collapse text-xs">
    <thead>
        <tr class="bg-slate-100 text-left">
            <th class="border border-slate-300 px-2 py-1.5">No</th>
            <th class="border border-slate-300 px-2 py-1.5">NIS</th>
            <th class="border border-slate-300 px-2 py-1.5">Nama</th>
            {{-- Rekap wali kelas hanya memuat satu kelas, jadi kolomnya mubazir di sana. --}}
            @if ($tampilkanKelas)
                <th class="border border-slate-300 px-2 py-1.5">Kelas</th>
            @endif
            <th class="border border-slate-300 px-2 py-1.5 text-center">Hadir</th>
            <th class="border border-slate-300 px-2 py-1.5 text-center">Terlambat</th>
            <th class="border border-slate-300 px-2 py-1.5 text-center">Izin</th>
            <th class="border border-slate-300 px-2 py-1.5 text-center">Sakit</th>
            <th class="border border-slate-300 px-2 py-1.5 text-center">Alpha</th>
            <th class="border border-slate-300 px-2 py-1.5 text-center">Kehadiran</th>
            <th class="border border-slate-300 px-2 py-1.5">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($students as $index => $student)
            @php
                $stat = $stats[$student->nisn];
                $perluTindakLanjut = $stat['alpha'] >= $warningThreshold;
            @endphp
            <tr class="{{ $perluTindakLanjut ? 'bg-red-50' : '' }}">
                <td class="border border-slate-300 px-2 py-1.5">{{ $index + 1 }}</td>
                <td class="border border-slate-300 px-2 py-1.5">{{ $student->nis ?? '-' }}</td>
                <td class="border border-slate-300 px-2 py-1.5">{{ $student->pengguna->nama }}</td>
                @if ($tampilkanKelas)
                    <td class="border border-slate-300 px-2 py-1.5">{{ $student->kelas?->nama ?? '-' }}</td>
                @endif
                <td class="border border-slate-300 px-2 py-1.5 text-center">{{ $stat['hadir'] }}</td>
                <td class="border border-slate-300 px-2 py-1.5 text-center">{{ $stat['terlambat'] }}</td>
                <td class="border border-slate-300 px-2 py-1.5 text-center">{{ $stat['izin'] }}</td>
                <td class="border border-slate-300 px-2 py-1.5 text-center">{{ $stat['sakit'] }}</td>
                <td class="border border-slate-300 px-2 py-1.5 text-center">{{ $stat['alpha'] }}</td>
                <td class="border border-slate-300 px-2 py-1.5 text-center">{{ $stat['percentage'] }}%</td>
                <td class="border border-slate-300 px-2 py-1.5 {{ $perluTindakLanjut ? 'font-bold text-red-700' : '' }}">
                    {{ $perluTindakLanjut ? 'Perlu tindak lanjut' : '-' }}
                </td>
            </tr>
        @empty
            <tr>
                <td class="border border-slate-300 px-2 py-4 text-center text-slate-500" colspan="{{ $tampilkanKelas ? 11 : 10 }}">
                    Tidak ada data.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
@endsection
