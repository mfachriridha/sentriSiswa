@extends('layouts.app')

@section('title', 'Tata Tertib')

@section('content')
<x-page-header title="Tata Tertib" description="Baca tata tertib sekolah yang sedang berlaku." />

<div class="rounded-xl border border-gray-200 bg-white p-4 lg:p-6">
    @if ($schoolRule)
        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">{{ $schoolRule->judul }}</h2>
                <p class="mt-1 text-sm text-gray-500">Diperbarui {{ $schoolRule->diperbarui_pada->translatedFormat('d F Y') }}</p>
            </div>
            <a href="{{ asset('storage/'.$schoolRule->path_file) }}" target="_blank"
               class="inline-flex items-center justify-center rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
                Buka PDF
            </a>
        </div>
        <iframe src="{{ asset('storage/'.$schoolRule->path_file) }}" class="h-[70vh] w-full rounded-lg border border-gray-200"></iframe>
    @else
        <div class="py-16 text-center">
            <h2 class="text-lg font-semibold text-gray-900">Tata tertib belum tersedia</h2>
            <p class="mt-2 text-sm text-gray-500">Silakan cek kembali setelah kesiswaan mengunggah tata tertib.</p>
        </div>
    @endif
</div>
@endsection
