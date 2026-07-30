@extends('layouts.cetak')

{{-- Judul halaman dipakai peramban sebagai nama berkas bawaan saat disimpan jadi PDF. --}}
@section('title', $judul)
@section('judul-cetak', $judul)

@php
    $th = 'border border-slate-300 px-2 py-1.5 text-left';
    $td = 'border border-slate-300 px-2 py-1.5 align-top';
@endphp

@section('content')
<header class="mb-6 border-b border-slate-200 pb-4">
    <h1 class="text-xl font-bold text-slate-900">{{ $judul }}</h1>
    <p class="mt-1 text-xs text-slate-400">
        Dicetak {{ now()->locale('id')->translatedFormat('d F Y H:i') }}
    </p>
</header>

<section class="mb-8">
    <h2 class="mb-2 text-sm font-bold text-slate-800">Pelanggaran</h2>
    <table class="w-full border-collapse text-xs">
        <thead>
            <tr class="bg-slate-100">
                <th class="{{ $th }}">Tanggal</th>
                <th class="{{ $th }}">NIS</th>
                <th class="{{ $th }}">Siswa</th>
                <th class="{{ $th }}">Kelas</th>
                <th class="{{ $th }}">Pelanggaran</th>
                <th class="{{ $th }}">Kategori</th>
                <th class="{{ $th }}">Poin</th>
                <th class="{{ $th }}">Dicatat Oleh</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($violations as $violation)
                <tr>
                    <td class="{{ $td }}">{{ $violation->tanggal_pelanggaran->locale('id')->translatedFormat('d F Y') }}</td>
                    <td class="{{ $td }}">{{ $violation->profilSiswa?->nis ?? '-' }}</td>
                    <td class="{{ $td }}">{{ $violation->profilSiswa?->pengguna?->nama ?? '-' }}</td>
                    <td class="{{ $td }}">{{ $violation->profilSiswa?->kelas?->nama ?? '-' }}</td>
                    <td class="{{ $td }}">{{ $violation->nama_pelanggaran }}</td>
                    <td class="{{ $td }}">{{ $categoryLabels[$violation->kategori_pelanggaran] ?? $violation->kategori_pelanggaran }}</td>
                    <td class="{{ $td }}">-{{ $violation->pengurangan_poin }}</td>
                    <td class="{{ $td }}">{{ $violation->dicatatOleh?->nama ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td class="{{ $td }} text-center text-slate-500" colspan="8">Tidak ada data laporan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</section>

<section>
    <h2 class="mb-2 text-sm font-bold text-slate-800">Penambahan Poin</h2>
    <table class="w-full border-collapse text-xs">
        <thead>
            <tr class="bg-slate-100">
                <th class="{{ $th }}">Tanggal Disetujui</th>
                <th class="{{ $th }}">NIS</th>
                <th class="{{ $th }}">Siswa</th>
                <th class="{{ $th }}">Kelas</th>
                <th class="{{ $th }}">Alasan</th>
                <th class="{{ $th }}">Poin</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pengajuanPoin as $pengajuan)
                <tr>
                    <td class="{{ $td }}">{{ $pengajuan->disetujui_pada?->locale('id')->translatedFormat('d F Y') ?? '-' }}</td>
                    <td class="{{ $td }}">{{ $pengajuan->profilSiswa?->nis ?? '-' }}</td>
                    <td class="{{ $td }}">{{ $pengajuan->profilSiswa?->pengguna?->nama ?? '-' }}</td>
                    <td class="{{ $td }}">{{ $pengajuan->profilSiswa?->kelas?->nama ?? '-' }}</td>
                    <td class="{{ $td }}">{{ $pengajuan->alasan }}</td>
                    <td class="{{ $td }}">+{{ $pengajuan->jumlah_poin }}</td>
                </tr>
            @empty
                <tr>
                    <td class="{{ $td }} text-center text-slate-500" colspan="6">
                        Tidak ada penambahan poin disetujui pada rentang ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</section>
@endsection
