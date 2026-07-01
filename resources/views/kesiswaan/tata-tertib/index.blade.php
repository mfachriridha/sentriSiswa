@extends('layouts.app')

@section('title', 'Tata Tertib')

@section('content')
<x-page-header title="Tata Tertib" description="Upload PDF tata tertib dan pilih satu file yang aktif untuk siswa." />

<x-alert type="success" :message="session('success')" />
<x-alert type="error" :message="session('error')" />

<div class="mb-5 rounded-xl border border-gray-200 bg-white p-4 lg:p-6">
    <form method="POST" action="{{ route('kesiswaan.tata-tertib.store') }}" enctype="multipart/form-data" class="grid gap-4 lg:grid-cols-4">
        @csrf
        <div class="lg:col-span-2">
            <label for="judul" class="block text-sm font-medium text-gray-700">Judul</label>
            <input id="judul" name="judul" type="text" value="{{ old('judul', 'Tata Tertib Sekolah') }}"
                   class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20">
            @error('judul') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="file_pdf" class="block text-sm font-medium text-gray-700">File PDF</label>
            <input id="file_pdf" name="file_pdf" type="file" accept="application/pdf"
                   class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            @error('file_pdf') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div class="flex items-end gap-3">
            <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" name="dipublikasikan" value="1" class="rounded border-gray-300 text-primary focus:ring-primary">
                Publikasikan
            </label>
            <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
                Upload
            </button>
        </div>
    </form>
</div>

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-4 py-3">Judul</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Upload</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($schoolRules as $rule)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900">{{ $rule->judul }}</div>
                            <a href="{{ asset('storage/'.$rule->path_file) }}" target="_blank" class="inline-flex items-center gap-1 rounded-lg border border-gray-300 px-2.5 py-1 text-xs font-medium text-gray-600 hover:bg-gray-50 transition-colors">Preview PDF</a>
                        </td>
                        <td class="px-4 py-3">
                            <x-badge :variant="$rule->dipublikasikan ? 'success' : 'neutral'">
                                {{ $rule->dipublikasikan ? 'Aktif' : 'Draft' }}
                            </x-badge>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $rule->diunggahOleh?->nama ?? '-' }} · {{ $rule->created_at->translatedFormat('d F Y') }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-end gap-2">
                                @if ($rule->dipublikasikan)
                                    <form method="POST" action="{{ route('kesiswaan.tata-tertib.unpublish', $rule) }}">
                                        @csrf
                                        @method('PUT')
                                        <button class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">Nonaktifkan</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('kesiswaan.tata-tertib.publish', $rule) }}">
                                        @csrf
                                        @method('PUT')
                                        <button class="rounded-lg border border-primary px-3 py-1.5 text-xs font-medium text-primary hover:bg-primary/5">Publikasikan</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('kesiswaan.tata-tertib.destroy', $rule) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-16 text-center text-sm text-gray-500">Belum ada file tata tertib.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($schoolRules->hasPages())
        <div class="border-t border-gray-200 px-4 py-3">{{ $schoolRules->links() }}</div>
    @endif
</div>
@endsection
