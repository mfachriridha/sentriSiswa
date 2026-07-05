@extends('layouts.app')

@section('title', 'Detail Siswa')

@section('content')
<div class="mb-5 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
    <a href="{{ route('admin.siswa.index') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.siswa.edit', $siswa) }}"
           class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm
                  hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit
        </a>
    </div>
</div>

<div class="rounded-xl border border-gray-200 bg-white p-6">
    <div class="mb-6 flex items-center gap-5">
        @if ($siswa->profilSiswa?->foto)
            <img src="{{ asset('storage/'.$siswa->profilSiswa->foto) }}" alt="{{ $siswa->nama }}"
                 class="h-20 w-20 rounded-full object-cover border-2 border-gray-200">
        @else
            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-primary/10 text-2xl font-bold text-primary">
                {{ strtoupper(substr($siswa->nama, 0, 1)) }}
            </div>
        @endif
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $siswa->nama }}</h1>
            <p class="mt-1 text-sm text-gray-500">
                @if ($siswa->profilSiswa?->kelas)
                    <a href="{{ route('admin.kelas.show', $siswa->profilSiswa->kelas) }}" class="text-primary hover:underline">
                        {{ $siswa->profilSiswa->kelas->nama }}
                    </a>
                @else
                    Belum ada kelas
                @endif
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Email</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $siswa->email ?? '-' }}</p>
        </div>

        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">NISN</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $siswa->profilSiswa?->nisn ?? '-' }}</p>
        </div>

        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">NIS</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $siswa->profilSiswa?->nis ?? '-' }}</p>
        </div>

        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Kelas</p>
            <p class="mt-1.5 text-sm">
                @if ($siswa->profilSiswa?->kelas)
                    <a href="{{ route('admin.kelas.show', $siswa->profilSiswa->kelas) }}" class="text-primary hover:underline">
                        {{ $siswa->profilSiswa->kelas->nama }}
                    </a>
                @else
                    <span class="text-gray-400">-</span>
                @endif
            </p>
        </div>

        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Telepon</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $siswa->profilSiswa?->telepon ?? '-' }}</p>
        </div>

        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Alamat</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $siswa->profilSiswa?->alamat ?? '-' }}</p>
        </div>
    </div>
</div>

@endsection
