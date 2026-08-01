@extends('layouts.app')

@section('title', 'Periode & Batas Alpha')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Periode & Batas Alpha</h1>
    <p class="mt-1 text-sm text-gray-500">Konfigurasi Tahun Pelajaran/Semester Aktif dan ambang batas maksimal Alpha sesuai tata tertib sekolah.</p>
</div>

<x-alert type="success" :message="session('success')" />

<div class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <p class="text-sm font-semibold text-green-800">Periode Aktif Tersimpan</p>
            <p class="mt-1 text-sm text-green-700">
                Tahun Pelajaran: <span class="font-bold">{{ $academicYear }}</span> ({{ $periodMode === 'tahun_ajaran' ? 'Hitungan Per Tahun Pelajaran' : 'Hitungan Per Semester ('.$semesterPeriod.')' }}).
            </p>
            <p class="mt-1 text-sm text-green-700">
                Rentang Aktif: {{ \Illuminate\Support\Carbon::parse($startDate)->translatedFormat('d M Y') }} s.d. {{ \Illuminate\Support\Carbon::parse($endDate)->translatedFormat('d M Y') }}.
            </p>
            <p class="mt-1 text-sm text-green-700">
                Batas Maksimal Alpha: <span class="font-bold">{{ $maxAlphaLimit }}x</span> (SP1: {{ $thresholds['sp1'] }}x | SP2: {{ $thresholds['sp2'] }}x | Wakasis: {{ $thresholds['wakasis'] }}x).
            </p>
        </div>
        <div class="text-sm text-green-700">
            @if($updatedAt)
                Terakhir disimpan {{ \Illuminate\Support\Carbon::parse($updatedAt)->translatedFormat('d F Y H:i') }}
            @else
                Menggunakan konfigurasi bawaan
            @endif
        </div>
    </div>
</div>

<div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4">
    <p class="text-sm font-semibold text-amber-800">Cakupan Perhitungan Absensi</p>
    <p class="mt-1 text-sm text-amber-700">
        Seluruh perhitungan akumulasi Alpha di Dashboard Siswa, Rekap Absensi Wali Kelas, BK, dan Kesiswaan akan secara otomatis disaring berdasarkan rentang tanggal periode aktif ini.
    </p>
</div>

<form method="POST" action="{{ route('admin.pengaturan.periode-absen.update') }}" class="space-y-6" x-data="{ loading: false }" @submit="loading = true">
    @csrf
    @method('PUT')

    {{-- Kartu 1: Periode & Tahun Ajaran --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-base font-semibold text-gray-900">Periode Akademik Aktif</h2>
        <p class="mt-0.5 text-sm text-gray-500">Tentukan tahun pelajaran dan rentang tanggal berlakunya absensi.</p>

        <div class="mt-4 grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label for="academic_year" class="block text-sm font-medium text-gray-700">
                    Tahun Pelajaran <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       id="academic_year"
                       name="academic_year"
                       value="{{ old('academic_year', $academicYear) }}"
                       placeholder="Contoh: 2025/2026"
                       required
                       class="mt-1.5 block w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20">
                @error('academic_year')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="period_mode" class="block text-sm font-medium text-gray-700">
                    Mode Hitungan Periode <span class="text-red-500">*</span>
                </label>
                <select id="period_mode"
                        name="period_mode"
                        required
                        class="mt-1.5 block w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20">
                    <option value="tahun_ajaran" {{ old('period_mode', $periodMode) === 'tahun_ajaran' ? 'selected' : '' }}>
                        Hitung Per Tahun Pelajaran (1 Tahun Penuh)
                    </option>
                    <option value="semester" {{ old('period_mode', $periodMode) === 'semester' ? 'selected' : '' }}>
                        Hitung Per Semester (6 Bulan)
                    </option>
                </select>
                @error('period_mode')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="semester_period" class="block text-sm font-medium text-gray-700">
                    Semester Aktif <span class="text-red-500">*</span>
                </label>
                <select id="semester_period"
                        name="semester_period"
                        required
                        class="mt-1.5 block w-full rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20">
                    <option value="ganjil" {{ old('semester_period', $semesterPeriod) === 'ganjil' ? 'selected' : '' }}>Semester Ganjil</option>
                    <option value="genap" {{ old('semester_period', $semesterPeriod) === 'genap' ? 'selected' : '' }}>Semester Genap</option>
                </select>
                @error('semester_period')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="period_start_date" class="block text-sm font-medium text-gray-700">
                        Tanggal Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           id="period_start_date"
                           name="period_start_date"
                           value="{{ old('period_start_date', $startDate) }}"
                           required
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20">
                    @error('period_start_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="period_end_date" class="block text-sm font-medium text-gray-700">
                        Tanggal Selesai <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           id="period_end_date"
                           name="period_end_date"
                           value="{{ old('period_end_date', $endDate) }}"
                           required
                           class="mt-1.5 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20">
                    @error('period_end_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- Kartu 2: Batas & Ambang Alpha --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-base font-semibold text-gray-900">Batas Maksimal & Ambang Peringatan Alpha</h2>
        <p class="mt-0.5 text-sm text-gray-500">Sesuaikan dengan poin tata tertib sekolah untuk memicu penanda peringatan bagi Wali Kelas, BK, dan Wakasis.</p>

        <div class="mt-4 grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <label for="max_alpha_limit" class="block text-sm font-medium text-gray-700">
                    Batas Maksimal Alpha (Jumlah Jatah) <span class="text-red-500">*</span>
                </label>
                <div class="mt-1.5 flex items-center gap-2">
                    <input type="number"
                           id="max_alpha_limit"
                           name="max_alpha_limit"
                           value="{{ old('max_alpha_limit', $maxAlphaLimit) }}"
                           min="1"
                           max="100"
                           required
                           class="block w-32 rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20">
                    <span class="text-sm font-medium text-gray-500">kali Alpha</span>
                </div>
                <p class="mt-1 text-xs text-gray-500">Batas default sesuai tata tertib adalah 6x Alpha.</p>
                @error('max_alpha_limit')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="alpha_sp1_threshold" class="block text-sm font-medium text-gray-700">
                    Ambang SP 1 (Wali Kelas & BK) <span class="text-red-500">*</span>
                </label>
                <div class="mt-1.5 flex items-center gap-2">
                    <input type="number"
                           id="alpha_sp1_threshold"
                           name="alpha_sp1_threshold"
                           value="{{ old('alpha_sp1_threshold', $thresholds['sp1']) }}"
                           min="1"
                           max="100"
                           required
                           class="block w-32 rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20">
                    <span class="text-sm font-medium text-gray-500">kali Alpha</span>
                </div>
                <p class="mt-1 text-xs text-gray-500">Aturan sekolah: 3x Alpha (Pemanggilan Ortu & SP 1).</p>
                @error('alpha_sp1_threshold')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="alpha_sp2_threshold" class="block text-sm font-medium text-gray-700">
                    Ambang SP 2 (Guru BK) <span class="text-red-500">*</span>
                </label>
                <div class="mt-1.5 flex items-center gap-2">
                    <input type="number"
                           id="alpha_sp2_threshold"
                           name="alpha_sp2_threshold"
                           value="{{ old('alpha_sp2_threshold', $thresholds['sp2']) }}"
                           min="1"
                           max="100"
                           required
                           class="block w-32 rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20">
                    <span class="text-sm font-medium text-gray-500">kali Alpha</span>
                </div>
                <p class="mt-1 text-xs text-gray-500">Aturan sekolah: 4x Alpha (Pemanggilan Ortu & SP 2 oleh BK).</p>
                @error('alpha_sp2_threshold')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="alpha_wakasis_threshold" class="block text-sm font-medium text-gray-700">
                    Ambang Wakasis (Dikembalikan ke Ortu) <span class="text-red-500">*</span>
                </label>
                <div class="mt-1.5 flex items-center gap-2">
                    <input type="number"
                           id="alpha_wakasis_threshold"
                           name="alpha_wakasis_threshold"
                           value="{{ old('alpha_wakasis_threshold', $thresholds['wakasis']) }}"
                           min="1"
                           max="100"
                           required
                           class="block w-32 rounded-lg border border-gray-300 bg-white px-3.5 py-2.5 text-sm text-gray-900 shadow-sm focus:border-primary focus:ring-2 focus:ring-primary/20">
                    <span class="text-sm font-medium text-gray-500">kali Alpha</span>
                </div>
                <p class="mt-1 text-xs text-gray-500">Aturan sekolah: 6x Alpha (Diproses Wakasis & Dikembalikan ke Ortu).</p>
                @error('alpha_wakasis_threshold')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-3">
        <button type="submit"
                :disabled="loading"
                class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-primary to-primary-dark px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-primary/20 transition-all hover:shadow-lg hover:shadow-primary/30 disabled:opacity-50">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span x-text="loading ? 'Menyimpan...' : 'Simpan Konfigurasi'">Simpan Konfigurasi</span>
        </button>
    </div>
</form>
@endsection
