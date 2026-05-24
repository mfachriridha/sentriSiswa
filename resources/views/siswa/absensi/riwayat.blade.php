@extends('layouts.app')

@section('title', 'Riwayat Absensi')

@section('content')
<div class="mb-6">
    <a href="{{ route('siswa.absensi') }}"
       class="inline-flex items-center gap-2 text-base text-gray-500 hover:text-gray-700 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
</div>

<div class="rounded-xl border border-gray-200 bg-white p-8">
    <h1 class="text-2xl font-bold text-gray-900">Riwayat Absensi</h1>
    <p class="mt-1 text-base text-gray-500">Catatan kehadiran Anda</p>
</div>
@endsection