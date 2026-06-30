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
            {{ strtoupper(substr($student->nama, 0, 1)) }}
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Biodata {{ $student->nama }}</h1>
            <p class="mt-1 text-sm text-gray-500">Lengkapi data pribadi siswa</p>
        </div>
    </div>

    {{-- Photo Upload --}}
    <div class="mb-6">
        <h2 class="mb-4 text-lg font-semibold text-gray-900">Pas Foto 3×4</h2>
        <div class="flex items-start gap-6"
             x-data="{ photoUrl: '{{ $student->profilSiswa?->foto ? asset('storage/'.$student->profilSiswa->foto) : '' }}', uploading: false, uploadError: '' }">
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
                           @change="uploading = true; uploadError = ''; let formData = new FormData(); formData.append('photo', $event.target.files[0]); fetch('{{ route('admin.siswa.foto', $student) }}', {method: 'POST', headers: {'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json'}, body: formData}).then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); }).then(data => { if (data.url) { photoUrl = data.url; } else { uploadError = data.message || 'Upload gagal. Coba lagi.'; } uploading = false; }).catch(() => { uploading = false; uploadError = 'Upload gagal. Periksa ukuran file (maks 2MB) dan coba lagi.'; })">
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
                <p x-cloak x-show="uploadError" x-text="uploadError" class="text-sm text-red-600 font-medium"></p>
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
                    <input id="place_of_birth" type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $student->profilSiswa?->biodata?->tempat_lahir) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('tempat_lahir')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                    <input id="date_of_birth" type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $student->profilSiswa?->biodata?->tanggal_lahir?->format('Y-m-d')) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('tanggal_lahir')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Kelamin</label>
                    <div class="flex items-center gap-6 mt-1">
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="jenis_kelamin" value="L" {{ old('jenis_kelamin', $student->profilSiswa?->biodata?->jenis_kelamin) === 'L' ? 'checked' : '' }}
                                   class="h-4 w-4 text-primary border-gray-300 focus:ring-primary">
                            <span class="text-sm text-gray-700">Laki-laki</span>
                        </label>
                        <label class="inline-flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="jenis_kelamin" value="P" {{ old('jenis_kelamin', $student->profilSiswa?->biodata?->jenis_kelamin) === 'P' ? 'checked' : '' }}
                                   class="h-4 w-4 text-primary border-gray-300 focus:ring-primary">
                            <span class="text-sm text-gray-700">Perempuan</span>
                        </label>
                    </div>
                    @error('jenis_kelamin')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="religion" class="block text-sm font-medium text-gray-700">Agama</label>
                    <input id="religion" type="text" name="agama" value="{{ old('agama', $student->profilSiswa?->biodata?->agama) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('agama')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="family_status" class="block text-sm font-medium text-gray-700">Status dalam Keluarga</label>
                    <select id="family_status" name="status_keluarga"
                            class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                   focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                        <option value="">Pilih status</option>
                        <option value="Kandung" {{ old('status_keluarga', $student->profilSiswa?->biodata?->status_keluarga) === 'Kandung' ? 'selected' : '' }}>Kandung</option>
                        <option value="Angkat" {{ old('status_keluarga', $student->profilSiswa?->biodata?->status_keluarga) === 'Angkat' ? 'selected' : '' }}>Angkat</option>
                        <option value="Tiri" {{ old('status_keluarga', $student->profilSiswa?->biodata?->status_keluarga) === 'Tiri' ? 'selected' : '' }}>Tiri</option>
                    </select>
                    @error('status_keluarga')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="child_number" class="block text-sm font-medium text-gray-700">Anak Ke</label>
                    <input id="child_number" type="number" name="anak_ke" min="1" value="{{ old('anak_ke', $student->profilSiswa?->biodata?->anak_ke) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('anak_ke')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="school_of_origin" class="block text-sm font-medium text-gray-700">Sekolah Asal</label>
                    <input id="school_of_origin" type="text" name="asal_sekolah" value="{{ old('asal_sekolah', $student->profilSiswa?->biodata?->asal_sekolah) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('asal_sekolah')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="admission_date" class="block text-sm font-medium text-gray-700">Tanggal Diterima</label>
                    <input id="admission_date" type="date" name="tanggal_masuk" value="{{ old('tanggal_masuk', $student->profilSiswa?->biodata?->tanggal_masuk?->format('Y-m-d')) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('tanggal_masuk')
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
                    <input id="father_name" type="text" name="nama_ayah" value="{{ old('nama_ayah', $student->profilSiswa?->biodata?->nama_ayah) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('nama_ayah')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="father_occupation" class="block text-sm font-medium text-gray-700">Pekerjaan Ayah</label>
                    <input id="father_occupation" type="text" name="pekerjaan_ayah" value="{{ old('pekerjaan_ayah', $student->profilSiswa?->biodata?->pekerjaan_ayah) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('pekerjaan_ayah')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="mother_name" class="block text-sm font-medium text-gray-700">Nama Ibu</label>
                    <input id="mother_name" type="text" name="nama_ibu" value="{{ old('nama_ibu', $student->profilSiswa?->biodata?->nama_ibu) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('nama_ibu')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="mother_occupation" class="block text-sm font-medium text-gray-700">Pekerjaan Ibu</label>
                    <input id="mother_occupation" type="text" name="pekerjaan_ibu" value="{{ old('pekerjaan_ibu', $student->profilSiswa?->biodata?->pekerjaan_ibu) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('pekerjaan_ibu')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="parent_address" class="block text-sm font-medium text-gray-700">Alamat Orang Tua</label>
                    <textarea id="parent_address" name="alamat_ortu" rows="2"
                              class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                     placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">{{ old('alamat_ortu', $student->profilSiswa?->biodata?->alamat_ortu) }}</textarea>
                    @error('alamat_ortu')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="parent_phone" class="block text-sm font-medium text-gray-700">Telepon Orang Tua</label>
                    <input id="parent_phone" type="text" name="telepon_ortu" value="{{ old('telepon_ortu', $student->profilSiswa?->biodata?->telepon_ortu) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('telepon_ortu')
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
                    <input id="guardian_name" type="text" name="nama_wali" value="{{ old('nama_wali', $student->profilSiswa?->biodata?->nama_wali) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('nama_wali')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="guardian_occupation" class="block text-sm font-medium text-gray-700">Pekerjaan Wali</label>
                    <input id="guardian_occupation" type="text" name="pekerjaan_wali" value="{{ old('pekerjaan_wali', $student->profilSiswa?->biodata?->pekerjaan_wali) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('pekerjaan_wali')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="guardian_address" class="block text-sm font-medium text-gray-700">Alamat Wali</label>
                    <textarea id="guardian_address" name="alamat_wali" rows="2"
                              class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                     placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">{{ old('alamat_wali', $student->profilSiswa?->biodata?->alamat_wali) }}</textarea>
                    @error('alamat_wali')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="guardian_phone" class="block text-sm font-medium text-gray-700">Telepon Wali</label>
                    <input id="guardian_phone" type="text" name="telepon_wali" value="{{ old('telepon_wali', $student->profilSiswa?->biodata?->telepon_wali) }}"
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 px-4 py-3 text-sm text-gray-900 shadow-sm
                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors">
                    @error('telepon_wali')
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
                <span x-cloak x-show="loading">
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
