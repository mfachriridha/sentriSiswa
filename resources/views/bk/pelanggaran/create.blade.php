@extends('layouts.app')

@section('title', 'Buat Pengajuan Pelanggaran')

@section('content')
<x-page-header title="Buat Pengajuan Pelanggaran" description="Pengajuan akan masuk ke kesiswaan dan belum mengurangi poin sampai disetujui." />

<div class="rounded-xl border border-gray-200 bg-white p-4 lg:p-6">
    <form method="POST" action="{{ route('bk.pelanggaran.store') }}" class="space-y-5">
        @csrf
        <div>
            <label for="student_profile_id" class="block text-sm font-medium text-gray-700">Siswa</label>
            <select id="student_profile_id" name="student_profile_id" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <option value="">Pilih siswa</option>
                @foreach ($students as $student)
                    <option value="{{ $student->id }}" {{ old('student_profile_id', $selectedStudentId) == $student->id ? 'selected' : '' }}>{{ $student->user?->name }} - {{ $student->class?->name }}</option>
                @endforeach
            </select>
            @error('student_profile_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="violation_type_id" class="block text-sm font-medium text-gray-700">Jenis Pelanggaran</label>
            <select id="violation_type_id" name="violation_type_id" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <option value="">Pilih pelanggaran</option>
                @foreach ($violationTypes as $type)
                    <option value="{{ $type->id }}" {{ old('violation_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }} - {{ $categoryLabels[$type->category] ?? $type->category }} (-{{ $type->point_deduction }})</option>
                @endforeach
            </select>
            @error('violation_type_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="violation_date" class="block text-sm font-medium text-gray-700">Tanggal</label>
            <input id="violation_date" name="violation_date" type="date" value="{{ old('violation_date', now()->toDateString()) }}" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
            @error('violation_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>
        <div>
            <label for="notes" class="block text-sm font-medium text-gray-700">Catatan</label>
            <textarea id="notes" name="notes" rows="4" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">{{ old('notes') }}</textarea>
        </div>
        <div class="flex justify-end gap-2">
            <a href="{{ route('bk.pelanggaran.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700">Batal</a>
            <button class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">Kirim Pengajuan</button>
        </div>
    </form>
</div>
@endsection
