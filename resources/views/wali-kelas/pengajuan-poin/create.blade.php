@extends('layouts.app')

@section('title', 'Buat Pengajuan Poin')

@section('content')
<x-page-header title="Buat Pengajuan Poin" description="Ajukan penambahan poin prestasi untuk siswa di kelas binaan Anda berdasarkan kategori prestasi baku." />

<div class="rounded-xl border border-gray-200 bg-white p-4 lg:p-6">
    <form method="POST" action="{{ route('wali-kelas.pengajuan-poin.store') }}" class="space-y-5">
        @csrf
        @if($students->isEmpty())
            <div class="mb-5 flex items-center gap-3 rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900">
                <svg class="h-5 w-5 shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Seluruh siswa di kelas binaan Anda saat ini memiliki <strong>100 poin (maksimal)</strong>. Pengajuan penambahan poin hanya diperuntukkan bagi siswa yang poinnya di bawah 100.</span>
            </div>
        @endif

        <div>
            <label for="profil_siswa_id" class="block text-sm font-medium text-gray-700">Siswa <span class="text-red-500">*</span></label>
            <select id="profil_siswa_id" name="profil_siswa_id" required class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" {{ $students->isEmpty() ? 'disabled' : '' }}>
                <option value="">-- {{ $students->isEmpty() ? 'Tidak Ada Siswa yang Poinnya Di Bawah 100' : 'Pilih Siswa' }} --</option>
                @foreach ($students as $student)
                    <option value="{{ $student->nisn }}" {{ old('profil_siswa_id') == $student->nisn ? 'selected' : '' }}>
                        {{ $student->pengguna?->nama }} (NISN: {{ $student->nisn }}) &middot; Sisa Poin: {{ $student->poin }}/100
                    </option>
                @endforeach
            </select>
            @error('profil_siswa_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="kategori_pengajuan_poin_id" class="block text-sm font-medium text-gray-700">Kategori Prestasi / Alasan <span class="text-red-500">*</span></label>
            <select id="kategori_pengajuan_poin_id" name="kategori_pengajuan_poin_id" required class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                <option value="" data-poin="0">-- Pilih Kategori Prestasi --</option>
                @foreach ($categories as $groupName => $groupCategories)
                    <optgroup label="📌 {{ $groupName }}">
                        @foreach ($groupCategories as $cat)
                            <option value="{{ $cat->id }}" data-poin="{{ $cat->poin }}" {{ old('kategori_pengajuan_poin_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nama }} (+{{ $cat->poin }} Poin)
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
            @error('kategori_pengajuan_poin_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror

            <!-- Indikator Poin Baku -->
            <div id="poin_preview_box" class="mt-2.5 hidden items-center gap-2 rounded-lg border border-green-200 bg-green-50 p-3 text-sm text-green-900">
                <span>Nilai Tambahan Poin: <strong id="poin_amount_text" class="text-base font-bold text-green-700">+0 Poin</strong></span>
            </div>
        </div>

        <div>
            <label for="alasan" class="block text-sm font-medium text-gray-700">Keterangan Detail Prestasi / Event <span class="text-red-500">*</span></label>
            <textarea id="alasan" name="alasan" rows="3" required placeholder="Contoh: Juara 1 Lomba Catur O2SN Tingkat Provinsi Tahun 2026" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">{{ old('alasan') }}</textarea>
            @error('alasan') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end gap-2 pt-2">
            <a href="{{ route('wali-kelas.pengajuan-poin.index') }}" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Batal</a>
            <button type="submit" class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">Kirim Pengajuan</button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectCat = document.getElementById('kategori_pengajuan_poin_id');
        const previewBox = document.getElementById('poin_preview_box');
        const amountText = document.getElementById('poin_amount_text');

        function updatePreview() {
            const selectedOpt = selectCat.options[selectCat.selectedIndex];
            const poin = selectedOpt ? selectedOpt.getAttribute('data-poin') : 0;

            if (poin && parseInt(poin) > 0) {
                amountText.textContent = '+' + poin + ' Poin';
                previewBox.classList.remove('hidden');
                previewBox.classList.add('flex');
            } else {
                previewBox.classList.add('hidden');
                previewBox.classList.remove('flex');
            }
        }

        selectCat.addEventListener('change', updatePreview);
        updatePreview();
    });
</script>
@endsection
