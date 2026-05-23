@extends('layouts.app')

@section('title', 'Siswa')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Siswa</h1>
        <p class="mt-2 text-base text-gray-500">Kelola data siswa</p>
    </div>
    <div class="flex items-center gap-3">
        <button type="button"
                onclick="window.dispatchEvent(new CustomEvent('open-confirm-modal', {
                    detail: {
                        title: 'Hapus Semua Siswa',
                        message: 'Anda akan menghapus semua data siswa. Tindakan ini akan menghapus semua siswa beserta data profilnya.',
                        secondMessage: 'PERINGATAN: Tindakan ini tidak dapat diurungkan! Semua data siswa akan hilang permanen.',
                        formId: 'delete-all-students-form'
                    }
                }))"
                class="inline-flex items-center gap-2 rounded-lg border border-red-200 px-5 py-3 text-base font-medium text-red-600 shadow-sm
                       hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-300 transition-colors cursor-pointer">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Hapus Semua
        </button>
        <a href="{{ route('admin.students.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-3 text-base font-semibold text-white shadow-sm
                  hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Siswa
        </a>
    </div>
</div>

<x-alert type="success" :message="session('success')" />

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
    <table class="min-w-full text-left text-base">
        <thead class="border-b border-gray-200 bg-gray-50">
            <tr>
                <th class="px-6 py-5 font-semibold text-gray-600">NISN</th>
                <th class="px-6 py-5 font-semibold text-gray-600">NIS</th>
                <th class="px-6 py-5 font-semibold text-gray-600">Nama</th>
                <th class="px-6 py-5 font-semibold text-gray-600">Kelas</th>
                <th class="px-6 py-5 font-semibold text-gray-600">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($students as $student)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-5 text-gray-700">{{ $student->studentProfile?->nisn ?? '-' }}</td>
                    <td class="px-6 py-5 text-gray-700">{{ $student->studentProfile?->nis ?? '-' }}</td>
                    <td class="px-6 py-5 font-medium text-gray-900">{{ $student->name }}</td>
                    <td class="px-6 py-5 text-gray-700">
                        @if ($student->studentProfile?->class)
                            <a href="{{ route('admin.classes.show', $student->studentProfile->class) }}" class="text-primary hover:underline">
                                {{ $student->studentProfile->class->name }}
                            </a>
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.students.show', $student) }}"
                               class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Lihat
                            </a>
                            <a href="{{ route('admin.students.edit', $student) }}"
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
                                            title: 'Hapus Siswa',
                                            message: 'Yakin ingin menghapus siswa {{ $student->name }}?',
                                            formId: 'delete-student-{{ $student->id }}'
                                        }
                                    }))"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors cursor-pointer">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Hapus
                            </button>
                            <form id="delete-student-{{ $student->id }}" method="POST" action="{{ route('admin.students.destroy', $student) }}" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center text-base text-gray-500">Belum ada data siswa.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<form id="delete-all-students-form" method="POST" action="{{ route('admin.students.delete-all') }}" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endsection
