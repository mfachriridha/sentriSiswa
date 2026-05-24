@extends('layouts.app')

@section('title', 'Dashboard Siswa')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard Siswa</h1>
    <p class="mt-1 text-base text-gray-500">Selamat datang, {{ auth()->user()->name }}!</p>
</div>

<div class="rounded-xl border border-gray-200 bg-white p-8 text-center">
    <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
    </svg>
    <h3 class="mt-4 text-lg font-semibold text-gray-900">Segera Hadir</h3>
    <p class="mt-2 text-base text-gray-500">Fitur dashboard siswa sedang dalam pengembangan.</p>
</div>
@endsection