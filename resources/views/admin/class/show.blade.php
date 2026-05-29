@extends('layouts.app')

@section('title', 'Detail Kelas')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <a href="{{ route('admin.kelas.index') }}"
       class="inline-flex items-center gap-2 text-base text-gray-500 hover:text-gray-700 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
    <a href="{{ route('admin.kelas.edit', $class) }}"
       class="inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-3 text-base font-semibold text-white shadow-sm
              hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Edit
    </a>
</div>

<div class="rounded-xl border border-gray-200 bg-white p-8">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">{{ $class->name }}</h1>
        <p class="mt-1 text-base text-gray-500">
            @if ($class->grade === '10')
                <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-sm font-medium text-blue-700">Tingkat 10</span>
            @elseif ($class->grade === '11')
                <span class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-sm font-medium text-green-700">Tingkat 11</span>
            @else
                <span class="inline-flex items-center rounded-full bg-purple-50 px-3 py-1 text-sm font-medium text-purple-700">Tingkat 12</span>
            @endif
        </p>
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Tingkat</p>
            <p class="mt-1.5">
                @if ($class->grade === '10')
                    <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-sm font-medium text-blue-700">Tingkat 10</span>
                @elseif ($class->grade === '11')
                    <span class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-sm font-medium text-green-700">Tingkat 11</span>
                @else
                    <span class="inline-flex items-center rounded-full bg-purple-50 px-3 py-1 text-sm font-medium text-purple-700">Tingkat 12</span>
                @endif
            </p>
        </div>

        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Wali Kelas</p>
            <p class="mt-1.5 text-base text-gray-900">
                @if ($class->homeroomTeacher)
                    <a href="{{ route('admin.guru.show', $class->homeroomTeacher) }}"
                       class="text-primary hover:underline">
                        {{ $class->homeroomTeacher->name }}
                    </a>
                @else
                    -
                @endif
            </p>
        </div>

        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Jumlah Siswa</p>
            <p class="mt-1.5 text-base text-gray-900">{{ $class->students_count }}</p>
        </div>
    </div>
</div>
@endsection
