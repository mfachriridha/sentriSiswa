@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
@php
    $profile = $teacher->teacherProfile;
    $teacherTypeLabels = [
        'homeroom' => 'Wali Kelas',
        'counselor' => 'BK',
        'student_affairs' => 'Kesiswaan',
    ];

    $teacherScope = match ($profile?->teacher_type) {
        'homeroom' => $teacher->homeroomClass?->name,
        'counselor' => $profile?->grade ? 'Tingkat '.$profile->grade : null,
        'student_affairs' => 'Seluruh sekolah',
        default => null,
    };
@endphp

<div class="mb-6 flex items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Profil Saya</h1>
        <p class="mt-1 text-base text-gray-500">Data guru dan akun yang digunakan untuk masuk ke sistem.</p>
    </div>
    <a href="{{ route('guru.profil.edit') }}"
       class="inline-flex items-center gap-2 rounded-lg border border-primary/30 px-5 py-3 text-base font-medium text-primary
              hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Edit Profil
    </a>
</div>

<div class="rounded-xl border border-gray-200 bg-white p-8">
    <div class="mb-6 flex items-center gap-5">
        @if ($profile?->photo)
            <img src="{{ asset('storage/'.$profile->photo) }}" alt="{{ $teacher->name }}"
                 class="h-20 w-20 rounded-full object-cover border-2 border-gray-200">
        @else
            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-primary/10 text-2xl font-bold text-primary">
                {{ strtoupper(substr($teacher->name, 0, 1)) }}
            </div>
        @endif
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $teacher->name }}</h2>
            <p class="mt-1 text-base text-gray-500">{{ $teacherTypeLabels[$profile?->teacher_type] ?? 'Guru Umum' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Nama</p>
            <p class="mt-1.5 text-base text-gray-900">{{ $teacher->name }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">NIP</p>
            <p class="mt-1.5 text-base text-gray-900">{{ $profile?->nip ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Tipe Guru</p>
            <p class="mt-1.5 text-base text-gray-900">{{ $teacherTypeLabels[$profile?->teacher_type] ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Tingkat/Kelas Binaan</p>
            <p class="mt-1.5 text-base text-gray-900">{{ $teacherScope ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Email</p>
            <p class="mt-1.5 text-base text-gray-900">{{ $teacher->email ?? '-' }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Nomor HP</p>
            <p class="mt-1.5 text-base text-gray-900">{{ $profile?->phone ?? '-' }}</p>
        </div>
    </div>
</div>
@endsection
