@extends('layouts.app')

@section('title', 'Biodata Siswa')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.siswa.show', $student) }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
</div>

<div class="rounded-xl border border-gray-200 bg-white p-6">
    <div class="mb-6 flex items-center gap-5">
        <div class="flex h-20 w-20 items-center justify-center rounded-full bg-primary/10 text-2xl font-bold text-primary">
            {{ strtoupper(substr($student->name, 0, 1)) }}
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Biodata {{ $student->name }}</h1>
            <p class="mt-1 text-sm text-gray-500">Lengkapi data pribadi siswa</p>
        </div>
    </div>

    {{-- Photo Upload --}}
    <div class="mb-6">
        <h2 class="mb-4 text-lg font-semibold text-gray-900">Pas Foto 3×4</h2>
        <div class="flex items-start gap-6"
             x-data="{ photoUrl: '{{ $student->studentProfile?->photo ? asset('storage/'.$student->studentProfile->photo) : '' }}', uploading: false }">
            <div class="relative h-40 w-30 overflow-hidden rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 flex items-center justify-center"
                 style="aspect-ratio: 3/4;">
                <template x-if="photoUrl">
                    <img :src="photoUrl" alt="Foto" class="h-full w-full object-cover">
                </template>
                <template x-if="!photoUrl">
                    <svg class="h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </template>
            </div>
            <div class="flex flex-col gap-2">
                <label class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-primary/30 px-4 py-2 text-sm font-medium text-primary hover:bg-primary/5 transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span x-text="uploading ? 'Mengunggah...' : 'Pilih Foto'"></span>
                    <input type="file" accept="image/jpeg,image/png,image/webp" class="hidden"
                           :disabled="uploading"
                           @change="uploading = true; let formData = new FormData(); formData.append('photo', $event.target.files[0]); fetch('{{ route('admin.siswa.foto', $student) }}', {method: 'POST', headers: {'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json'}, body: formData}).then(r => r.json()).then(data => { photoUrl = data.url; uploading = false; }).catch(() => { uploading = false; })">
                </label>
                <template x-if="photoUrl">
                    <button type="button"
                            class="inline-flex items-center gap-2 rounded-lg border border-red-200 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors cursor-pointer"
                            @click="fetch('{{ route('admin.siswa.foto.hapus', $student) }}', {method: 'DELETE', headers: {'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json'}}).then(() => { photoUrl = ''; })">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Hapus Foto
                    </button>
                </template>
                <p class="text-sm text-gray-400">Format: JPG/PNG, maks 2MB. Akan di-resize ke 3:4.</p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.siswa.biodata.update', $student) }}" class="space-y-8"
          x-data="{ loading: false }" @submit="loading = true">
        @csrf
        @method('PUT')

        {{-- Section 1: Data Pribadi --}}
        <div>
            <h2 class="mb-4 text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">Data Pribadi</h2>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label for="place_of_birth" class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                    <input id="place_of_birth" type="text" name="place_of_birth" value="{{ old('place_of_birth', $student->studentProfile?->biodata?->place_of_birth) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('place_of_birth')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                    <input id="date_of_birth" type="date" name="date_of_birth" value="{{ old('date_of_birth', $student->studentProfile?->biodata?->date_of_birth?->format('Y-m-d')) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('date_of_birth')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
                    <div class="flex items-center gap-6 mt-1">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="gender" value="L" {{ old('gender', $student->studentProfile?->biodata?->gender) === 'L' ? 'checked' : '' }}
                                   class="h-4 w-4 text-primary border-gray-300 focus:ring-primary">
                            <span class="text-sm text-gray-700">Laki-laki</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="gender" value="P" {{ old('gender', $student->studentProfile?->biodata?->gender) === 'P' ? 'checked' : '' }}
                                   class="h-4 w-4 text-primary border-gray-300 focus:ring-primary">
                            <span class="text-sm text-gray-700">Perempuan</span>
                        </label>
                    </div>
                    @error('gender')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="religion" class="block text-sm font-medium text-gray-700">Agama</label>
                    <input id="religion" type="text" name="religion" value="{{ old('religion', $student->studentProfile?->biodata?->religion) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('religion')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="family_status" class="block text-sm font-medium text-gray-700">Status dalam Keluarga</label>
                    <select id="family_status" name="family_status"
                            class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                   focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                        <option value="">Pilih status</option>
                        <option value="Kandung" {{ old('family_status', $student->studentProfile?->biodata?->family_status) === 'Kandung' ? 'selected' : '' }}>Kandung</option>
                        <option value="Angkat" {{ old('family_status', $student->studentProfile?->biodata?->family_status) === 'Angkat' ? 'selected' : '' }}>Angkat</option>
                        <option value="Tiri" {{ old('family_status', $student->studentProfile?->biodata?->family_status) === 'Tiri' ? 'selected' : '' }}>Tiri</option>
                    </select>
                    @error('family_status')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="child_number" class="block text-sm font-medium text-gray-700">Anak Ke</label>
                    <input id="child_number" type="number" name="child_number" min="1" value="{{ old('child_number', $student->studentProfile?->biodata?->child_number) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('child_number')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="school_of_origin" class="block text-sm font-medium text-gray-700">Sekolah Asal</label>
                    <input id="school_of_origin" type="text" name="school_of_origin" value="{{ old('school_of_origin', $student->studentProfile?->biodata?->school_of_origin) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('school_of_origin')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="admission_date" class="block text-sm font-medium text-gray-700">Tanggal Diterima</label>
                    <input id="admission_date" type="date" name="admission_date" value="{{ old('admission_date', $student->studentProfile?->biodata?->admission_date?->format('Y-m-d')) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('admission_date')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Section 2: Data Orang Tua --}}
        <div>
            <h2 class="mb-4 text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">Data Orang Tua</h2>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label for="father_name" class="block text-sm font-medium text-gray-700">Nama Ayah</label>
                    <input id="father_name" type="text" name="father_name" value="{{ old('father_name', $student->studentProfile?->biodata?->father_name) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('father_name')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="father_occupation" class="block text-sm font-medium text-gray-700">Pekerjaan Ayah</label>
                    <input id="father_occupation" type="text" name="father_occupation" value="{{ old('father_occupation', $student->studentProfile?->biodata?->father_occupation) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('father_occupation')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="mother_name" class="block text-sm font-medium text-gray-700">Nama Ibu</label>
                    <input id="mother_name" type="text" name="mother_name" value="{{ old('mother_name', $student->studentProfile?->biodata?->mother_name) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('mother_name')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="mother_occupation" class="block text-sm font-medium text-gray-700">Pekerjaan Ibu</label>
                    <input id="mother_occupation" type="text" name="mother_occupation" value="{{ old('mother_occupation', $student->studentProfile?->biodata?->mother_occupation) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('mother_occupation')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="parent_address" class="block text-sm font-medium text-gray-700">Alamat Orang Tua</label>
                    <textarea id="parent_address" name="parent_address" rows="2"
                              class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                     placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">{{ old('parent_address', $student->studentProfile?->biodata?->parent_address) }}</textarea>
                    @error('parent_address')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="parent_phone" class="block text-sm font-medium text-gray-700">Telepon Orang Tua</label>
                    <input id="parent_phone" type="text" name="parent_phone" value="{{ old('parent_phone', $student->studentProfile?->biodata?->parent_phone) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('parent_phone')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Section 3: Data Wali --}}
        <div>
            <h2 class="mb-4 text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">Data Wali <span class="text-sm font-normal text-gray-400">(isi jika siswa tidak tinggal bersama orang tua)</span></h2>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label for="guardian_name" class="block text-sm font-medium text-gray-700">Nama Wali</label>
                    <input id="guardian_name" type="text" name="guardian_name" value="{{ old('guardian_name', $student->studentProfile?->biodata?->guardian_name) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('guardian_name')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="guardian_occupation" class="block text-sm font-medium text-gray-700">Pekerjaan Wali</label>
                    <input id="guardian_occupation" type="text" name="guardian_occupation" value="{{ old('guardian_occupation', $student->studentProfile?->biodata?->guardian_occupation) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('guardian_occupation')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="guardian_address" class="block text-sm font-medium text-gray-700">Alamat Wali</label>
                    <textarea id="guardian_address" name="guardian_address" rows="2"
                              class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                     placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">{{ old('guardian_address', $student->studentProfile?->biodata?->guardian_address) }}</textarea>
                    @error('guardian_address')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="guardian_phone" class="block text-sm font-medium text-gray-700">Telepon Wali</label>
                    <input id="guardian_phone" type="text" name="guardian_phone" value="{{ old('guardian_phone', $student->studentProfile?->biodata?->guardian_phone) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('guardian_phone')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4 pt-2 border-t border-gray-200">
            <button type="submit" :disabled="loading"
                    class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm
                           hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50
                           transition-colors disabled:opacity-60">
                <span x-show="loading">
                    <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                </span>
                Simpan Biodata
            </button>
            <a href="{{ route('admin.siswa.show', $student) }}"
               class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700
                      hover:bg-gray-50 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
