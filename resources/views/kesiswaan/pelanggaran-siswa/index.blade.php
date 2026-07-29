@extends('layouts.app')

@section('title', 'Pelanggaran Siswa')

@section('content')
@php
    $categoryBadgeClasses = \App\Models\JenisPelanggaran::categoryBadgeClasses();

    $hasActiveFilters = filled($search) || filled($filterClass) || filled($filterCategory) || filled($filterViolationType) || filled($filterDate);
@endphp

<div class="mb-5 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Pelanggaran Siswa</h1>
        <p class="mt-2 text-sm text-gray-500">Catat dan kelola pelanggaran siswa.</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        <a href="{{ route('kesiswaan.pelanggaran-siswa.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm
                  hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Catat Pelanggaran
        </a>
    </div>
</div>

<x-alert type="success" :message="session('success')" />
<x-alert type="error" :message="session('error')" />

{{-- Pilihan jenis mengikuti kategori yang dipilih. Tanpa itu, penyaringnya menawarkan
     kombinasi yang mustahil - kategori Sedang berpasangan dengan jenis yang kategorinya
     Ringan - dan hasilnya kosong tanpa penjelasan.

     Penyempitannya dikerjakan dua kali. Di sini, supaya daftarnya menyempit seketika
     begitu kategorinya diganti, tanpa memuat ulang halaman. Dan di sisi server, supaya
     daftar yang dikirim ke halaman ini memang sudah bersih sejak awal - jadi kombinasi
     yang mustahil itu tidak pernah sampai ke layar, bahkan lewat alamat yang diketik
     langsung. --}}
<form method="GET" action="{{ route('kesiswaan.pelanggaran-siswa.index') }}"
      class="mb-4 rounded-xl border border-gray-200 bg-white p-4"
      x-data="{
          kategori: @js($filterCategory),
          jenis: @js((string) $filterViolationType),
          semuaJenis: @js($violationTypes->map(fn ($jenis) => [
              'id' => (string) $jenis->id,
              'kategori' => $jenis->kategori,
              'label' => $jenis->nama.' ('.$jenis->pengurangan_poin.' poin)',
          ])->values()),
          get jenisTersedia() {
              return this.kategori
                  ? this.semuaJenis.filter(j => j.kategori === this.kategori)
                  : this.semuaJenis;
          },
          gantiKategori() {
              // Jenis yang tadi dipilih bisa jadi bukan milik kategori yang baru.
              if (! this.jenisTersedia.some(j => j.id === this.jenis)) {
                  this.jenis = '';
              }
          },
      }">
    <div class="grid grid-cols-1 gap-3 lg:grid-cols-6">
        <div class="relative lg:col-span-2">
            <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari siswa, NIS, NISN, pelanggaran..."
                   class="w-full rounded-lg border border-gray-300 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder-gray-400
                          focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-colors">
        </div>

        <select name="kelas_id"
                class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-700 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-colors">
            <option value="">Semua Kelas</option>
            @foreach ($classes as $class)
                <option value="{{ $class->id }}" {{ (string) $filterClass === (string) $class->id ? 'selected' : '' }}>{{ $class->nama }}</option>
            @endforeach
        </select>

        <select name="kategori" x-model="kategori" @change="gantiKategori()"
                class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-700 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-colors">
            <option value="">Semua Kategori</option>
            @foreach ($categoryLabels as $category => $label)
                <option value="{{ $category }}">{{ $label }}</option>
            @endforeach
        </select>

        <select name="jenis_pelanggaran_id" x-model="jenis"
                class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-700 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-colors">
            <option value="">Semua Jenis</option>
            <template x-for="jenisPelanggaran in jenisTersedia" :key="jenisPelanggaran.id">
                <option :value="jenisPelanggaran.id" x-text="jenisPelanggaran.label"></option>
            </template>
        </select>

        <input type="date" name="tanggal_pelanggaran" value="{{ $filterDate }}" min="2025-01-01"
               class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-700 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-colors">
    </div>

    <div class="mt-3 flex flex-wrap items-center gap-2">
        <input type="hidden" name="sort" value="{{ $sort }}">
        <input type="hidden" name="direction" value="{{ $direction }}">
        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-dark transition-colors">
            Terapkan
        </button>
        @if ($hasActiveFilters)
            <a href="{{ route('kesiswaan.pelanggaran-siswa.index') }}"
               class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                Reset
            </a>
        @endif
    </div>
</form>

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
    <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
            <thead class="border-b border-gray-200 bg-gray-50">
                <tr>
                    <th class="px-4 py-3"><x-sort-link label="Tanggal" column="tanggal_pelanggaran" :sort="$sort" :direction="$direction" /></th>
                    <th class="px-4 py-3 text-gray-600 font-semibold">Siswa</th>
                    <th class="px-4 py-3 text-gray-600 font-semibold">Kelas</th>
                    <th class="px-4 py-3"><x-sort-link label="Pelanggaran" column="nama_pelanggaran" :sort="$sort" :direction="$direction" /></th>
                    <th class="px-4 py-3"><x-sort-link label="Kategori" column="kategori_pelanggaran" :sort="$sort" :direction="$direction" /></th>
                    <th class="px-4 py-3"><x-sort-link label="Poin" column="pengurangan_poin" :sort="$sort" :direction="$direction" /></th>
                    <th class="px-4 py-3 text-gray-600 font-semibold">Sisa Poin</th>
                    <th class="px-4 py-3 text-gray-600 font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($studentViolations as $studentViolation)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-700">{{ $studentViolation->tanggal_pelanggaran?->translatedFormat('d F Y') }}</td>
                        <td class="px-4 py-3">
                            <p class="font-medium text-gray-900">{{ $studentViolation->profilSiswa?->pengguna?->nama ?? '-' }}</p>
                            <p class="mt-1 text-sm text-gray-500">NISN: {{ $studentViolation->profilSiswa?->nisn ?? '-' }} · NIS: {{ $studentViolation->profilSiswa?->nis ?? '-' }}</p>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $studentViolation->profilSiswa?->kelas?->nama ?? '-' }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $studentViolation->nama_pelanggaran }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium {{ $categoryBadgeClasses[$studentViolation->kategori_pelanggaran] ?? 'bg-gray-50 text-gray-700' }}">
                                {{ $categoryLabels[$studentViolation->kategori_pelanggaran] ?? '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-700">{{ $studentViolation->pengurangan_poin }} poin</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $studentViolation->profilSiswa?->poin ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('kesiswaan.pelanggaran-siswa.show', $studentViolation) }}"
                                   class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 transition-colors">
                                    Lihat
                                </a>
                                <button type="button"
                                        onclick="window.dispatchEvent(new CustomEvent('open-confirm-modal', {
                                            detail: {
                                                title: 'Hapus Pelanggaran Siswa',
                                                message: 'Yakin ingin menghapus catatan pelanggaran {{ $studentViolation->profilSiswa?->pengguna?->nama ?? 'siswa ini' }}?',
                                                formId: 'delete-student-violation-{{ $studentViolation->id }}'
                                            }
                                        }))"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors cursor-pointer">
                                    Hapus
                                </button>
                                <form id="delete-student-violation-{{ $studentViolation->id }}" method="POST" action="{{ route('kesiswaan.pelanggaran-siswa.destroy', $studentViolation) }}" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center text-sm text-gray-500">
                            {{ $hasActiveFilters ? 'Tidak ada pelanggaran siswa yang sesuai dengan filter.' : 'Belum ada catatan pelanggaran siswa.' }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($studentViolations->hasPages())
        <div class="border-t border-gray-200 px-4 py-3">
            <x-pagination :paginator="$studentViolations" />
        </div>
    @endif
</div>
@endsection
