@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="mb-5 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
    <h1 class="text-2xl font-bold text-gray-900">Profil Saya</h1>
    <a href="{{ route('siswa.profil.edit') }}"
       class="inline-flex items-center gap-2 rounded-lg border border-primary/30 px-4 py-2.5 text-sm font-medium text-primary
              hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Edit Profil
    </a>
</div>

{{-- Poin --}}
<div class="mb-6 rounded-xl border-2 border-primary/20 bg-primary/5 p-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-600">Poin Anda</p>
            <p class="mt-1 text-4xl font-bold {{ $student->profilSiswa?->poin <= 50 ? 'text-red-600' : ($student->profilSiswa?->poin <= 75 ? 'text-amber-600' : 'text-green-600') }}">
                {{ $student->profilSiswa?->poin ?? 100 }}
            </p>
        </div>
        <div class="text-right">
            <span class="inline-flex items-center gap-1.5 rounded-full {{ $student->profilSiswa?->poin <= 50 ? 'bg-red-50 text-red-700' : ($student->profilSiswa?->poin <= 75 ? 'bg-amber-50 text-amber-700' : 'bg-green-50 text-green-700') }} px-4 py-2 text-sm font-semibold">
                @if($student->profilSiswa?->poin <= 50)
                    Perhatian
                @elseif($student->profilSiswa?->poin <= 75)
                    Cukup
                @else
                    Baik
                @endif
            </span>
        </div>
    </div>
</div>

{{-- Data Dasar --}}
<div class="rounded-xl border border-gray-200 bg-white p-6">
    <div class="mb-6 flex items-center gap-5">
        @if ($student->profilSiswa?->foto)
            <img src="{{ asset('storage/'.$student->profilSiswa->foto) }}" alt="{{ $student->nama }}"
                 class="h-20 w-20 rounded-full object-cover border-2 border-gray-200">
        @else
            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-primary/10 text-2xl font-bold text-primary">
                {{ strtoupper(substr($student->nama, 0, 1)) }}
            </div>
        @endif
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $student->nama }}</h2>
            @if ($student->profilSiswa?->kelas)
                <p class="mt-1 text-sm text-gray-500">{{ $student->profilSiswa->kelas->nama }}</p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">NISN</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $student->profilSiswa?->nisn ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">NIS</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $student->profilSiswa?->nis ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Email</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $student->email ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Telepon</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $student->profilSiswa?->telepon ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Alamat</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $student->profilSiswa?->alamat ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Akun Google</p>
                <p class="mt-1.5 text-sm {{ $student->id_google ? 'text-green-600 font-medium' : 'text-gray-400' }}">
                    {{ $student->id_google ? 'Terhubung' : 'Belum terhubung' }}
                </p>
            </div>
            @if (! $student->id_google)
                <a href="{{ route('siswa.profil.edit') }}"
                   class="inline-flex items-center rounded-lg border border-primary/30 px-2.5 py-1 text-xs font-medium text-primary hover:bg-primary/5 transition-colors">Hubungkan</a>
            @endif
        </div>
    </div>
</div>

{{-- Biodata --}}
@php $biodata = $student->profilSiswa?->biodata; @endphp
@if ($biodata)
    <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6">
        <div class="mb-5 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
            <h2 class="text-xl font-bold text-gray-900">Biodata</h2>
            @if ($biodata->lengkap())
                <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-3 py-1 text-sm font-medium text-green-700">
                    ✓ Lengkap
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-sm font-medium text-amber-700">
                    ⚠ Belum lengkap
                </span>
            @endif
        </div>

        <h3 class="mb-4 text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">Data Pribadi</h3>
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                <p class="text-sm font-medium text-gray-500">Tempat Lahir</p>
                <p class="mt-1 text-sm text-gray-900">{{ $biodata->tempat_lahir ?? '-' }}</p>
            </div>
            <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                <p class="text-sm font-medium text-gray-500">Tanggal Lahir</p>
                <p class="mt-1 text-sm text-gray-900">{{ $biodata->tanggal_lahir?->translatedFormat('d F Y') ?? '-' }}</p>
            </div>
            <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                <p class="text-sm font-medium text-gray-500">Jenis Kelamin</p>
                <p class="mt-1 text-sm text-gray-900">{{ $biodata->jenis_kelamin === 'L' ? 'Laki-laki' : ($biodata->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</p>
            </div>
            <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                <p class="text-sm font-medium text-gray-500">Agama</p>
                <p class="mt-1 text-sm text-gray-900">{{ $biodata->agama ?? '-' }}</p>
            </div>
            <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                <p class="text-sm font-medium text-gray-500">Status dalam Keluarga</p>
                <p class="mt-1 text-sm text-gray-900">{{ $biodata->status_keluarga ?? '-' }}</p>
            </div>
            <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                <p class="text-sm font-medium text-gray-500">Anak Ke</p>
                <p class="mt-1 text-sm text-gray-900">{{ $biodata->anak_ke ?? '-' }}</p>
            </div>
            <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                <p class="text-sm font-medium text-gray-500">Sekolah Asal</p>
                <p class="mt-1 text-sm text-gray-900">{{ $biodata->asal_sekolah ?? '-' }}</p>
            </div>
            <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                <p class="text-sm font-medium text-gray-500">Tanggal Diterima</p>
                <p class="mt-1 text-sm text-gray-900">{{ $biodata->tanggal_masuk?->translatedFormat('d F Y') ?? '-' }}</p>
            </div>
        </div>

        <h3 class="mb-4 mt-8 text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">Data Orang Tua</h3>
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                <p class="text-sm font-medium text-gray-500">Nama Ayah</p>
                <p class="mt-1 text-sm text-gray-900">{{ $biodata->nama_ayah ?? '-' }}</p>
            </div>
            <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                <p class="text-sm font-medium text-gray-500">Pekerjaan Ayah</p>
                <p class="mt-1 text-sm text-gray-900">{{ $biodata->pekerjaan_ayah ?? '-' }}</p>
            </div>
            <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                <p class="text-sm font-medium text-gray-500">Nama Ibu</p>
                <p class="mt-1 text-sm text-gray-900">{{ $biodata->nama_ibu ?? '-' }}</p>
            </div>
            <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                <p class="text-sm font-medium text-gray-500">Pekerjaan Ibu</p>
                <p class="mt-1 text-sm text-gray-900">{{ $biodata->pekerjaan_ibu ?? '-' }}</p>
            </div>
            <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                <p class="text-sm font-medium text-gray-500">Alamat Orang Tua</p>
                <p class="mt-1 text-sm text-gray-900">{{ $biodata->alamat_ortu ?? '-' }}</p>
            </div>
            <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                <p class="text-sm font-medium text-gray-500">Telepon Orang Tua</p>
                <p class="mt-1 text-sm text-gray-900">{{ $biodata->telepon_ortu ?? '-' }}</p>
            </div>
        </div>

        @if ($biodata->nama_wali || $biodata->telepon_wali)
            <h3 class="mb-4 mt-8 text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">Data Wali</h3>
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                    <p class="text-sm font-medium text-gray-500">Nama Wali</p>
                    <p class="mt-1 text-sm text-gray-900">{{ $biodata->nama_wali ?? '-' }}</p>
                </div>
                <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                    <p class="text-sm font-medium text-gray-500">Pekerjaan Wali</p>
                    <p class="mt-1 text-sm text-gray-900">{{ $biodata->pekerjaan_wali ?? '-' }}</p>
                </div>
                <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                    <p class="text-sm font-medium text-gray-500">Alamat Wali</p>
                    <p class="mt-1 text-sm text-gray-900">{{ $biodata->alamat_wali ?? '-' }}</p>
                </div>
                <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                    <p class="text-sm font-medium text-gray-500">Telepon Wali</p>
                    <p class="mt-1 text-sm text-gray-900">{{ $biodata->telepon_wali ?? '-' }}</p>
                </div>
            </div>
        @endif
    </div>
@endif
@endsection
