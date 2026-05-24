@extends('layouts.app')

@section('title', 'Dashboard Guru')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard Guru</h1>
    <p class="mt-1 text-base text-gray-500">Selamat datang, {{ auth()->user()->name }}!</p>
</div>

<div class="rounded-xl border border-gray-200 bg-white p-8 text-center">
    <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
    </svg>
    <h3 class="mt-4 text-lg font-semibold text-gray-900">Segera Hadir</h3>
    <p class="mt-2 text-base text-gray-500">Fitur dashboard guru sedang dalam pengembangan.</p>
</div>
@endsection