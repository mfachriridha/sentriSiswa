@extends('layouts.app')

@section('title', 'Impor Guru')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.guru.index') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
</div>

<div class="rounded-xl border border-gray-200 bg-white p-6">
    <div class="mb-6 text-center">
        <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-primary/10">
            <svg class="h-8 w-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900">Impor Guru</h1>
        <p class="mt-2 text-sm text-gray-500">Unggah file Excel berisi data guru</p>
    </div>

    <div class="mb-6 flex items-center justify-center">
        <a href="{{ route('admin.guru.impor.template') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-primary/30 px-4 py-2.5 text-sm font-medium text-primary
                  hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Unduh Template Excel
        </a>
    </div>

    <form method="POST" action="{{ route('admin.guru.impor.unggah') }}" enctype="multipart/form-data" class="space-y-6"
          x-data="{ loading: false }" @submit="loading = true">
        @csrf

        <div class="rounded-lg border-2 border-dashed border-gray-300 p-6 text-center">
            <input type="file" name="file" id="file" accept=".xlsx,.xls"
                   class="block w-full text-sm text-gray-600 file:mr-4 file:rounded-lg file:border-0 file:bg-primary file:px-5 file:py-3 file:text-sm file:font-semibold file:text-white file:hover:bg-primary-dark file:transition-colors">
            <p class="mt-4 text-sm text-gray-500">Format: .xlsx atau .xls</p>
            @error('file')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-center">
            <button type="submit" :disabled="loading"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-8 py-3 text-sm font-semibold text-white shadow-sm
                           hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50
                           transition-colors disabled:opacity-60">
                <span x-cloak x-show="loading">
                    <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </span>
                Unggah & Pratinjau
            </button>
        </div>
    </form>
</div>
@endsection
