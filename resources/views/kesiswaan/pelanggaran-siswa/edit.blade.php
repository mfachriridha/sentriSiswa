@extends('layouts.app')

@section('title', 'Edit Pelanggaran Siswa')

@section('content')
<div class="mb-6">
    <a href="{{ route('kesiswaan.pelanggaran-siswa.index') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
</div>

<div class="rounded-xl border border-gray-200 bg-white p-6">
    <h1 class="mb-6 text-2xl font-bold text-gray-900">Edit Pelanggaran Siswa</h1>

    <form method="POST" action="{{ route('kesiswaan.pelanggaran-siswa.update', $studentViolation) }}" class="space-y-6" x-data="{ loading: false }" @submit="loading = true">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label for="student_profile_id" class="block text-sm font-medium text-gray-700">Siswa <span class="text-red-500">*</span></label>
                <select id="student_profile_id" name="student_profile_id" required
                        class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    <option value="">Pilih siswa</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}" {{ (string) old('student_profile_id', $studentViolation->student_profile_id) === (string) $student->id ? 'selected' : '' }}>
                            {{ $student->user?->name }} - {{ $student->class?->name ?? 'Tanpa kelas' }} - NISN {{ $student->nisn ?? '-' }}
                        </option>
                    @endforeach
                </select>
                @error('student_profile_id')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="violation_date" class="block text-sm font-medium text-gray-700">Tanggal Pelanggaran <span class="text-red-500">*</span></label>
                <input id="violation_date" type="date" name="violation_date" value="{{ old('violation_date', $studentViolation->violation_date?->toDateString()) }}" max="{{ now()->toDateString() }}" required
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                @error('violation_date')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="violation_type_id" class="block text-sm font-medium text-gray-700">Jenis Pelanggaran <span class="text-red-500">*</span></label>
                <select id="violation_type_id" name="violation_type_id" required
                        class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    <option value="">Pilih jenis pelanggaran</option>
                    @foreach ($violationTypes->groupBy('category') as $category => $groupedViolationTypes)
                        <optgroup label="{{ $categoryLabels[$category] ?? 'Kategori' }}">
                            @foreach ($groupedViolationTypes as $violationType)
                                <option value="{{ $violationType->id }}" {{ (string) old('violation_type_id', $studentViolation->violation_type_id) === (string) $violationType->id ? 'selected' : '' }}>
                                    {{ $violationType->name }} ({{ $violationType->point_deduction }} poin){{ $violationType->is_active ? '' : ' - Nonaktif' }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                @error('violation_type_id')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="md:col-span-2">
                <label for="notes" class="block text-sm font-medium text-gray-700">Catatan <span class="text-sm font-normal text-gray-400">(opsional)</span></label>
                <textarea id="notes" name="notes" rows="4"
                          class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">{{ old('notes', $studentViolation->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" :disabled="loading"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors disabled:opacity-60">
                <span x-cloak x-show="loading">
                    <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </span>
                Perbarui
            </button>
            <a href="{{ route('kesiswaan.pelanggaran-siswa.index') }}"
               class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
