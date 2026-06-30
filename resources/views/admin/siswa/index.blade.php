@extends('layouts.app')

@section('title', 'Siswa')

@section('content')
<div class="mb-5 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Siswa</h1>
        <p class="mt-2 text-sm text-gray-500">Kelola data siswa</p>
    </div>
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.siswa.impor') }}"
           class="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm
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
                        title: 'Hapus Semua Siswa',
                        message: 'Anda akan menghapus semua data siswa. Tindakan ini akan menghapus semua siswa beserta data profilnya.',
                        secondMessage: 'PERINGATAN: Tindakan ini tidak dapat diurungkan! Semua data siswa akan hilang permanen.',
                        formId: 'delete-all-students-form'
                    }
                }))"
                @disabled($students->isEmpty())
                class="inline-flex items-center gap-2 rounded-lg border border-red-200 px-4 py-2.5 text-sm font-medium text-red-600 shadow-sm
                       hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-300 transition-colors cursor-pointer
                       disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-transparent">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Hapus Semua
        </button>
        <a href="{{ route('admin.siswa.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm
                  hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Siswa
        </a>
    </div>
</div>

<x-alert type="success" :message="session('success')" />

<x-search-filter-form
    :action="route('admin.siswa.index')"
    :search="$search"
    placeholder="Cari nama, NISN, atau NIS..."
    :filters="[
        ['name' => 'tingkat', 'label' => 'Tingkat', 'value' => $filterGrade, 'options' => ['' => 'Semua Tingkat', '10' => '10', '11' => '11', '12' => '12']],
        ['name' => 'status', 'label' => 'Status', 'value' => $filterStatus, 'options' => ['' => 'Semua Status', 'registered' => 'Terdaftar', 'unregistered' => 'Belum Terdaftar']],
    ]"
    :sort="$sort"
    :direction="$direction"
/>

@if (session('import_result'))
    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm">
        <p class="font-semibold text-green-800">Impor berhasil!</p>
        <ul class="mt-2 list-disc pl-5 space-y-1 text-green-700">
            <li>{{ session('import_result.students_created') }} siswa baru dibuat</li>
            @if (session('import_result.students_existing'))
                <li class="text-amber-700">{{ session('import_result.students_existing') }} siswa sudah ada</li>
            @endif
            @if (session('import_result.duration'))
                <li>Selesai dalam {{ session('import_result.duration') }} detik</li>
            @endif
        </ul>
        @if (!empty(session('import_result.error_details')))
            <div class="mt-3">
                <p class="font-medium text-red-800">Detail baris yang dilewati/diabaikan:</p>
                <div class="mt-2 max-h-48 overflow-y-auto rounded border border-red-200 bg-red-50 p-3">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-red-700">
                                <th class="py-1 pr-4">Baris</th>
                                <th class="py-1 pr-4">Nama</th>
                                <th class="py-1">Alasan</th>
                            </tr>
                        </thead>
                        <tbody class="text-red-600">
                            @foreach (session('import_result.error_details') as $detail)
                                <tr>
                                    <td class="py-1 pr-4">{{ $detail['row'] }}</td>
                                    <td class="py-1 pr-4">{{ $detail['name'] }}</td>
                                    <td class="py-1">{{ $detail['reason'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@endif

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
        <thead class="border-b border-gray-200 bg-gray-50">
            <tr>
                <th class="hidden px-4 py-3 md:table-cell"><x-sort-link label="NISN" column="nisn" :sort="$sort" :direction="$direction" /></th>
                <th class="px-4 py-3"><x-sort-link label="NIS" column="nis" :sort="$sort" :direction="$direction" /></th>
                <th class="px-4 py-3"><x-sort-link label="Nama" column="name" :sort="$sort" :direction="$direction" /></th>
                <th class="px-4 py-3"><x-sort-link label="Kelas" column="class_name" :sort="$sort" :direction="$direction" /></th>
                <th class="px-4 py-3 text-gray-600 font-semibold">Status</th>
                <th class="px-4 py-3 text-gray-600 font-semibold">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($students as $student)
                <tr class="hover:bg-gray-50">
                    <td class="hidden px-4 py-3 text-gray-700 md:table-cell">{{ $student->profilSiswa?->nisn ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-700">{{ $student->profilSiswa?->nis ?? '-' }}</td>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $student->nama }}</td>
                    <td class="px-4 py-3 text-gray-700">
                        @if ($student->profilSiswa?->kelas)
                            <a href="{{ route('admin.kelas.show', $student->profilSiswa->kelas) }}" class="text-primary hover:underline">
                                {{ $student->profilSiswa->kelas->nama }}
                            </a>
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @if ($student->isRegistered())
                            <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700">Terdaftar</span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-500">Belum</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.siswa.show', $student) }}"
                               class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Lihat
                            </a>
                            <a href="{{ route('admin.siswa.edit', $student) }}"
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
                                            message: 'Yakin ingin menghapus siswa {{ $student->nama }}?',
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
                            <form id="delete-student-{{ $student->id }}" method="POST" action="{{ route('admin.siswa.destroy', $student) }}" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center text-sm text-gray-500">
                    {{ $search || $filterGrade || $filterStatus ? 'Tidak ada siswa yang sesuai dengan pencarian.' : 'Belum ada data siswa.' }}
                </td>
                </tr>
            @endforelse
        </tbody>
        </table>
    </div>

    @if ($students->hasPages())
        <div class="border-t border-gray-200 px-4 py-3">
            <x-pagination :paginator="$students" />
        </div>
    @endif
</div>

<form id="delete-all-students-form" method="POST" action="{{ route('admin.siswa.hapus-semua') }}" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endsection
