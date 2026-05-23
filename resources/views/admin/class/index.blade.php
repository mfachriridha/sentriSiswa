@extends('layouts.app')

@section('title', 'Kelas')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Kelas</h1>
        <p class="mt-2 text-base text-gray-500">Kelola data kelas</p>
    </div>
    <a href="{{ route('admin.classes.create') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-3 text-base font-semibold text-white shadow-sm
              hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Kelas
    </a>
</div>

<x-alert type="success" :message="session('success')" />

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
    <table class="min-w-full text-left text-base">
        <thead class="border-b border-gray-200 bg-gray-50">
            <tr>
                <th class="px-6 py-5 font-semibold text-gray-600">Nama</th>
                <th class="px-6 py-5 font-semibold text-gray-600">Tingkat</th>
                <th class="px-6 py-5 font-semibold text-gray-600">Wali Kelas</th>
                <th class="px-6 py-5 font-semibold text-gray-600">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($classes as $class)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-5 font-medium text-gray-900">{{ $class->name }}</td>
                    <td class="px-6 py-5">
                        <span class="inline-flex items-center rounded-full bg-primary/10 px-3 py-1 text-sm font-medium text-primary">
                            Tingkat {{ $class->grade }}
                        </span>
                    </td>
                    <td class="px-6 py-5 text-gray-700">{{ $class->homeroomTeacher?->name ?? '-' }}</td>
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.classes.show', $class) }}"
                               class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Lihat
                            </a>
                            <a href="{{ route('admin.classes.edit', $class) }}"
                               class="inline-flex items-center gap-1.5 rounded-lg border border-primary/30 px-3 py-2 text-sm font-medium text-primary hover:bg-primary/5 transition-colors">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </a>
                            <button type="button"
                                    onclick="window.dispatchEvent(new CustomEvent('open-confirm-modal', {
                                        detail: {
                                            title: 'Hapus Kelas',
                                            message: 'Yakin ingin menghapus kelas {{ $class->name }}?',
                                            formId: 'delete-class-{{ $class->id }}'
                                        }
                                    }))"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors cursor-pointer">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Hapus
                            </button>
                            <form id="delete-class-{{ $class->id }}" method="POST" action="{{ route('admin.classes.destroy', $class) }}" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-16 text-center text-base text-gray-500">Belum ada data kelas.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
