@extends('layouts.app')

@section('title', 'Guru')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Guru</h1>
        <p class="mt-1 text-base text-gray-500">Kelola data guru</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.teachers.import') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-5 py-3 text-base font-medium text-gray-700 shadow-sm
                  hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Impor Excel
        </a>
        <button type="button"
                onclick="window.dispatchEvent(new CustomEvent('open-confirm-modal', {
                    detail: {
                        title: 'Hapus Semua Guru',
                        message: 'Anda akan menghapus semua data guru. Tindakan ini akan menghapus semua guru beserta data profilnya.',
                        secondMessage: 'PERINGATAN: Tindakan ini tidak dapat diurungkan! Semua data guru akan hilang permanen.',
                        formId: 'delete-all-form'
                    }
                }))"
                @disabled($teachers->isEmpty())
                class="inline-flex items-center gap-2 rounded-lg border border-red-200 px-5 py-3 text-base font-medium text-red-600 shadow-sm
                       hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-300 transition-colors cursor-pointer
                       disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-transparent">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Hapus Semua
        </button>
        <a href="{{ route('admin.teachers.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-3 text-base font-semibold text-white shadow-sm
                  hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Guru
        </a>
    </div>
</div>

<x-alert type="success" :message="session('success')" />

<x-search-filter-form
    :action="route('admin.teachers.index')"
    :search="$search"
    placeholder="Cari nama atau NIP..."
    :filters="[
        ['name' => 'teacher_type', 'label' => 'Tipe', 'value' => $filterType, 'options' => ['' => 'Semua Tipe', 'homeroom' => 'Wali Kelas', 'counselor' => 'BK']],
        ['name' => 'grade', 'label' => 'Tingkat', 'value' => $filterGrade, 'options' => ['' => 'Semua Tingkat', '10' => '10', '11' => '11', '12' => '12']],
    ]"
    :sort="$sort"
    :direction="$direction"
/>

@if (session('import_result'))
    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-5 py-4 text-base text-green-700">
        <p class="font-semibold">Impor berhasil!</p>
        <ul class="mt-2 list-disc pl-5 space-y-1">
            <li>{{ session('import_result.teachers_created') }} guru baru dibuat</li>
            <li>{{ session('import_result.teachers_existing') }} guru sudah ada</li>
            <li>{{ session('import_result.classes_created') }} kelas dibuat</li>
            @if (session('import_result.duration'))
                <li>Selesai dalam {{ session('import_result.duration') }} detik</li>
            @endif
            @if (session('import_result.errors'))
                <li>{{ session('import_result.errors') }} baris dilewati</li>
            @endif
        </ul>
    </div>
@endif

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-base">
        <thead class="border-b border-gray-200 bg-gray-50">
            <tr>
                <th class="px-6 py-5"><x-sort-link label="NIP" column="nip" :sort="$sort" :direction="$direction" /></th>
                <th class="px-6 py-5"><x-sort-link label="Nama" column="name" :sort="$sort" :direction="$direction" /></th>
                <th class="px-6 py-5"><x-sort-link label="Email" column="email" :sort="$sort" :direction="$direction" /></th>
                <th class="px-6 py-5"><x-sort-link label="Tipe" column="teacher_type" :sort="$sort" :direction="$direction" /></th>
                <th class="px-6 py-5"><x-sort-link label="Kelas" column="class_name" :sort="$sort" :direction="$direction" /></th>
                <th class="px-6 py-5"><x-sort-link label="Telepon" column="phone" :sort="$sort" :direction="$direction" /></th>
                <th class="px-6 py-5 text-gray-600 font-semibold">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($teachers as $teacher)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-5 text-gray-700">{{ $teacher->teacherProfile?->nip ?? '-' }}</td>
                    <td class="px-6 py-5 font-medium text-gray-900">{{ $teacher->name }}</td>
                    <td class="px-6 py-5 text-gray-700">{{ $teacher->email }}</td>
                    <td class="px-6 py-5">
                        @if ($teacher->teacherProfile?->teacher_type === 'homeroom')
                            <span class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700">
                                Wali Kelas
                            </span>
                        @elseif ($teacher->teacherProfile?->teacher_type === 'counselor')
                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-700">
                                BK
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-5 text-gray-700">
                        @if ($teacher->homeroomClass)
                            <a href="{{ route('admin.classes.show', $teacher->homeroomClass) }}" class="text-primary hover:underline">
                                {{ $teacher->homeroomClass->name }}
                            </a>
                        @elseif ($teacher->teacherProfile?->grade)
                            <span class="inline-flex items-center gap-1 rounded-full bg-teal-50 px-2.5 py-0.5 text-xs font-medium text-teal-700">
                                BK {{ $teacher->teacherProfile->grade }}
                            </span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-5 text-gray-700">{{ $teacher->teacherProfile?->phone ?? '-' }}</td>
                    <td class="px-6 py-5">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.teachers.show', $teacher) }}"
                               class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Lihat
                            </a>
                            <a href="{{ route('admin.teachers.edit', $teacher) }}"
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
                                            title: 'Hapus Guru',
                                            message: 'Yakin ingin menghapus guru {{ $teacher->name }}?',
                                            formId: 'delete-teacher-{{ $teacher->id }}'
                                        }
                                    }))"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors cursor-pointer">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Hapus
                            </button>
                            <form id="delete-teacher-{{ $teacher->id }}" method="POST" action="{{ route('admin.teachers.destroy', $teacher) }}" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center text-base text-gray-500">
                    {{ $search || $filterType || $filterGrade ? 'Tidak ada guru yang sesuai dengan pencarian.' : 'Belum ada data guru.' }}
                </td>
                </tr>
            @endforelse
        </tbody>
        </table>
    </div>

    @if ($teachers->hasPages())
        <div class="border-t border-gray-200 px-6 py-4">
            <x-pagination :paginator="$teachers" />
        </div>
    @endif
</div>

<form id="delete-all-form" method="POST" action="{{ route('admin.teachers.delete-all') }}" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endsection
