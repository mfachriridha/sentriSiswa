@extends('layouts.app')

@section('title', 'Rekap Absensi')

@section('content')
<div class="flex flex-col items-center justify-center py-20 text-center">
    <svg class="mb-4 h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
    </svg>
    <h2 class="text-lg font-semibold text-gray-700">Data Tingkat Tidak Tersedia</h2>
    <p class="mt-2 text-sm text-gray-500">Akun BK Anda belum dikaitkan dengan tingkat kelas. Hubungi admin.</p>
</div>
@endsection
