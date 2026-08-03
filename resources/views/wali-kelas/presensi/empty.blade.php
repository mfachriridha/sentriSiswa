@extends('layouts.app')

@section('title', 'Rekap Presensi')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Rekap Presensi</h1>
    <p class="mt-1 text-sm text-gray-500">Kelola presensi siswa di kelas yang Anda ampu</p>
</div>

<div class="rounded-xl border border-gray-200 bg-white p-12 text-center">
    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
    </svg>
    <h3 class="mt-4 text-lg font-semibold text-gray-900">Belum Ada Kelas</h3>
    <p class="mt-2 text-sm text-gray-500">Anda belum ditugaskan sebagai wali kelas. Silakan hubungi administrator.</p>
</div>
@endsection
