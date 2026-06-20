@extends('layouts.app')

@section('title', 'Detail Siswa')

@section('content')
<div class="mb-5 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
    <a href="{{ route('admin.siswa.index') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('admin.siswa.biodata.edit', $student) }}"
           class="inline-flex items-center gap-2 rounded-lg border border-primary/30 px-4 py-2.5 text-sm font-medium text-primary
                  hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Biodata
        </a>
        <a href="{{ route('admin.siswa.edit', $student) }}"
           class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm
                  hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit
        </a>
    </div>
</div>

<div class="rounded-xl border border-gray-200 bg-white p-6">
    <div class="mb-6 flex items-center gap-5">
        @if ($student->studentProfile?->photo)
            <img src="{{ asset('storage/'.$student->studentProfile->photo) }}" alt="{{ $student->name }}"
                 class="h-20 w-20 rounded-full object-cover border-2 border-gray-200">
        @else
            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-primary/10 text-2xl font-bold text-primary">
                {{ strtoupper(substr($student->name, 0, 1)) }}
            </div>
        @endif
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $student->name }}</h1>
            <p class="mt-1 text-sm text-gray-500">
                @if ($student->studentProfile?->class)
                    <a href="{{ route('admin.kelas.show', $student->studentProfile->class) }}" class="text-primary hover:underline">
                        {{ $student->studentProfile->class->name }}
                    </a>
                @else
                    Belum ada kelas
                @endif
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Email</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $student->email ?? '-' }}</p>
        </div>

        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">NISN</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $student->studentProfile?->nisn ?? '-' }}</p>
        </div>

        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">NIS</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $student->studentProfile?->nis ?? '-' }}</p>
        </div>

        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Kelas</p>
            <p class="mt-1.5 text-sm">
                @if ($student->studentProfile?->class)
                    <a href="{{ route('admin.kelas.show', $student->studentProfile->class) }}" class="text-primary hover:underline">
                        {{ $student->studentProfile->class->name }}
                    </a>
                @else
                    <span class="text-gray-400">-</span>
                @endif
            </p>
        </div>

        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Telepon</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $student->studentProfile?->phone ?? '-' }}</p>
        </div>

        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Alamat</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $student->studentProfile?->address ?? '-' }}</p>
        </div>
    </div>
</div>

{{-- Biodata Section --}}
<div class="mt-6 rounded-xl border border-gray-200 bg-white p-6">
    <div class="mb-5 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
        <h2 class="text-xl font-bold text-gray-900">Biodata</h2>
        @php $biodata = $student->studentProfile?->biodata; @endphp
        @if ($biodata && $biodata->isComplete())
            <span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-3 py-1 text-sm font-medium text-green-700">
                ✓ Lengkap
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-sm font-medium text-amber-700">
                ⚠ Belum lengkap
            </span>
        @endif
    </div>

    @if ($biodata)
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
                <p class="text-sm font-medium text-gray-500">Tempat Lahir</p>
                <p class="mt-1.5 text-sm text-gray-900">{{ $biodata->place_of_birth ?? '-' }}</p>
            </div>

            <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
                <p class="text-sm font-medium text-gray-500">Tanggal Lahir</p>
                <p class="mt-1.5 text-sm text-gray-900">{{ $biodata->date_of_birth?->translatedFormat('d F Y') ?? '-' }}</p>
            </div>

            <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
                <p class="text-sm font-medium text-gray-500">Jenis Kelamin</p>
                <p class="mt-1.5 text-sm text-gray-900">{{ $biodata->gender === 'L' ? 'Laki-laki' : ($biodata->gender === 'P' ? 'Perempuan' : '-') }}</p>
            </div>

            <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
                <p class="text-sm font-medium text-gray-500">Agama</p>
                <p class="mt-1.5 text-sm text-gray-900">{{ $biodata->religion ?? '-' }}</p>
            </div>

            <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
                <p class="text-sm font-medium text-gray-500">Status dalam Keluarga</p>
                <p class="mt-1.5 text-sm text-gray-900">{{ $biodata->family_status ?? '-' }}</p>
            </div>

            <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
                <p class="text-sm font-medium text-gray-500">Anak Ke</p>
                <p class="mt-1.5 text-sm text-gray-900">{{ $biodata->child_number ?? '-' }}</p>
            </div>

            <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
                <p class="text-sm font-medium text-gray-500">Sekolah Asal</p>
                <p class="mt-1.5 text-sm text-gray-900">{{ $biodata->school_of_origin ?? '-' }}</p>
            </div>

            <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
                <p class="text-sm font-medium text-gray-500">Tanggal Diterima</p>
                <p class="mt-1.5 text-sm text-gray-900">{{ $biodata->admission_date?->translatedFormat('d F Y') ?? '-' }}</p>
            </div>
        </div>

        <h3 class="mb-4 mt-8 text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">Data Orang Tua</h3>
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
            <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
                <p class="text-sm font-medium text-gray-500">Nama Ayah</p>
                <p class="mt-1.5 text-sm text-gray-900">{{ $biodata->father_name ?? '-' }}</p>
            </div>

            <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
                <p class="text-sm font-medium text-gray-500">Pekerjaan Ayah</p>
                <p class="mt-1.5 text-sm text-gray-900">{{ $biodata->father_occupation ?? '-' }}</p>
            </div>

            <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
                <p class="text-sm font-medium text-gray-500">Nama Ibu</p>
                <p class="mt-1.5 text-sm text-gray-900">{{ $biodata->mother_name ?? '-' }}</p>
            </div>

            <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
                <p class="text-sm font-medium text-gray-500">Pekerjaan Ibu</p>
                <p class="mt-1.5 text-sm text-gray-900">{{ $biodata->mother_occupation ?? '-' }}</p>
            </div>

            <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
                <p class="text-sm font-medium text-gray-500">Alamat Orang Tua</p>
                <p class="mt-1.5 text-sm text-gray-900">{{ $biodata->parent_address ?? '-' }}</p>
            </div>

            <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
                <p class="text-sm font-medium text-gray-500">Telepon Orang Tua</p>
                <p class="mt-1.5 text-sm text-gray-900">{{ $biodata->parent_phone ?? '-' }}</p>
            </div>
        </div>

        @if ($biodata->guardian_name || $biodata->guardian_phone)
            <h3 class="mb-4 mt-8 text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">Data Wali</h3>
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
                    <p class="text-sm font-medium text-gray-500">Nama Wali</p>
                    <p class="mt-1.5 text-sm text-gray-900">{{ $biodata->guardian_name ?? '-' }}</p>
                </div>

                <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
                    <p class="text-sm font-medium text-gray-500">Pekerjaan Wali</p>
                    <p class="mt-1.5 text-sm text-gray-900">{{ $biodata->guardian_occupation ?? '-' }}</p>
                </div>

                <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
                    <p class="text-sm font-medium text-gray-500">Alamat Wali</p>
                    <p class="mt-1.5 text-sm text-gray-900">{{ $biodata->guardian_address ?? '-' }}</p>
                </div>

                <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
                    <p class="text-sm font-medium text-gray-500">Telepon Wali</p>
                    <p class="mt-1.5 text-sm text-gray-900">{{ $biodata->guardian_phone ?? '-' }}</p>
                </div>
            </div>
        @endif
    @else
        <p class="text-sm text-gray-500">Belum ada data biodata.</p>
        <a href="{{ route('admin.siswa.biodata.edit', $student) }}"
           class="mt-4 inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm
                  hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50 transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Lengkapi Biodata
        </a>
    @endif
</div>
@endsection
