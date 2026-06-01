@extends('layouts.app')

@section('title', 'Detail Guru')

@section('content')
<div class="mb-5 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
    <a href="{{ route('admin.guru.index') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
    <a href="{{ route('admin.guru.edit', $teacher) }}"
       class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm
              hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Edit
    </a>
</div>

<div class="rounded-xl border border-gray-200 bg-white p-6">
    <div class="mb-6 flex items-center gap-5">
        <div class="flex h-20 w-20 items-center justify-center rounded-full bg-primary/10 text-2xl font-bold text-primary">
            {{ strtoupper(substr($teacher->name, 0, 1)) }}
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $teacher->name }}</h1>
            <p class="mt-1 text-sm text-gray-500">
                @if ($teacher->isHomeroom())
                    Wali Kelas
                @elseif ($teacher->isCounselor())
                    BK
                @else
                    Guru
                @endif
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Email</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $teacher->email }}</p>
        </div>

        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">NIP</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $teacher->teacherProfile?->nip ?? '-' }}</p>
        </div>

        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Tipe</p>
            <p class="mt-1.5">
                @if ($teacher->teacherProfile?->teacher_type === 'homeroom')
                    <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-3 py-1 text-sm font-medium text-blue-700">Wali Kelas</span>
                @elseif ($teacher->teacherProfile?->teacher_type === 'counselor')
                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-3 py-1 text-sm font-medium text-amber-700">BK</span>
                @elseif ($teacher->teacherProfile?->teacher_type === 'student_affairs')
                    <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-3 py-1 text-sm font-medium text-green-700">Kesiswaan</span>
                @else
                    <span class="text-sm text-gray-400">-</span>
                @endif
            </p>
        </div>

        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Telepon</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $teacher->teacherProfile?->phone ?? '-' }}</p>
        </div>

        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Kelas yang Diampu</p>
            <p class="mt-1.5 text-sm">
                @if ($teacher->homeroomClass)
                    <a href="{{ route('admin.kelas.show', $teacher->homeroomClass) }}" class="text-primary hover:underline">
                        {{ $teacher->homeroomClass->name }}
                    </a>
                @elseif ($teacher->teacherProfile?->grade)
                    <span class="inline-flex items-center gap-1 rounded-full bg-teal-50 px-3 py-1 text-sm font-medium text-teal-700">
                        Tingkat {{ $teacher->teacherProfile->grade }}
                    </span>
                @else
                    <span class="text-gray-400">-</span>
                @endif
            </p>
        </div>
    </div>
</div>
@endsection
