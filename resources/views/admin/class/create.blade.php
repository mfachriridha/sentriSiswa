@extends('layouts.app')

@section('title', 'Tambah Kelas')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.classes.index') }}"
       class="inline-flex items-center gap-2 text-base text-gray-500 hover:text-gray-700 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
</div>

<div class="rounded-xl border border-gray-200 bg-white p-8">
    <h1 class="mb-8 text-2xl font-bold text-gray-900">Tambah Kelas</h1>

    <form method="POST" action="{{ route('admin.classes.store') }}" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label for="grade" class="block text-base font-medium text-gray-700">Tingkat</label>
                <select id="grade" name="grade" required
                        class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-base text-gray-900 shadow-sm
                               focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    <option value="">Pilih tingkat</option>
                    <option value="10" {{ old('grade') === '10' ? 'selected' : '' }}>10</option>
                    <option value="11" {{ old('grade') === '11' ? 'selected' : '' }}>11</option>
                    <option value="12" {{ old('grade') === '12' ? 'selected' : '' }}>12</option>
                </select>
                @error('grade')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="identifier" class="block text-base font-medium text-gray-700">Nama Kelas</label>
                <input id="identifier" type="text" name="identifier" value="{{ old('identifier') }}" required
                       placeholder="contoh: 1, IPA 1, SAINS 1"
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-base text-gray-900 shadow-sm
                              placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20
                              transition-colors">
                @error('identifier')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="homeroom_teacher_id" class="block text-base font-medium text-gray-700">Wali Kelas</label>
                <select id="homeroom_teacher_id" name="homeroom_teacher_id"
                        class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-base text-gray-900 shadow-sm
                               focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    <option value="">Pilih wali kelas</option>
                    @foreach ($homeroomTeachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ old('homeroom_teacher_id') == $teacher->id ? 'selected' : '' }}>
                            {{ $teacher->name }}
                        </option>
                    @endforeach
                </select>
                @error('homeroom_teacher_id')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit"
                    class="rounded-lg bg-primary px-6 py-3 text-base font-semibold text-white shadow-sm
                           hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50
                           transition-colors">
                Simpan
            </button>
            <a href="{{ route('admin.classes.index') }}"
               class="rounded-lg border border-gray-300 px-6 py-3 text-base font-medium text-gray-700
                      hover:bg-gray-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
