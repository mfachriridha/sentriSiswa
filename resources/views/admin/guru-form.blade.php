@extends('layouts.app')

@section('title', 'Form Guru - Admin')

@section('page-title', 'Form Guru')

@section('content')
<?php $edit = request()->routeIs('admin.guru.edit'); ?>
<div class="mb-6">
    <a href="{{ route('admin.guru') }}" class="text-sm text-[#0062a0] hover:text-[#1c6880] font-semibold">&larr; Kembali</a>
</div>

<div class="card anim-up">
    <h3 class="font-bold text-slate-900 mb-1">{{ $edit ? 'Edit' : 'Tambah' }} Guru</h3>
    <p class="text-sm text-muted mb-5">Lengkapi data guru di bawah ini.</p>

    <form class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">NIP <span class="text-red-500">*</span></label>
                <input type="text" class="input-field" value="{{ $edit ? '1234567890' : '' }}" placeholder="1234567890">
            </div>
            <div>
                <label class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" class="input-field" value="{{ $edit ? 'Budi Santoso, S.Pd' : '' }}" placeholder="Nama lengkap">
            </div>
            <div>
                <label class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Jenis Kelamin</label>
                <select class="input-field"><option {{ $edit ? 'selected' : '' }}>Laki-laki</option><option>Perempuan</option></select>
            </div>
            <div>
                <label class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Mata Pelajaran <span class="text-blue-500 font-normal normal-case">(pilih &ge; 1)</span></label>
                <div class="border border-[#c3c6d1] rounded-lg p-3 space-y-2 max-h-40 overflow-y-auto">
                    <label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" class="rounded" {{ $edit ? 'checked' : '' }}> <span>Matematika</span></label>
                    <label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" class="rounded"> <span>Fisika</span></label>
                    <label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" class="rounded"> <span>Kimia</span></label>
                    <label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" class="rounded"> <span>Biologi</span></label>
                    <label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" class="rounded"> <span>Bahasa Indonesia</span></label>
                    <label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" class="rounded"> <span>Bahasa Inggris</span></label>
                    <label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" class="rounded"> <span>PPKN</span></label>
                    <label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" class="rounded"> <span>Sejarah</span></label>
                    <label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" class="rounded"> <span>Geografi</span></label>
                    <label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" class="rounded"> <span>Ekonomi</span></label>
                    <label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" class="rounded"> <span>Sosiologi</span></label>
                    <label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" class="rounded"> <span>Seni Budaya</span></label>
                    <label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" class="rounded"> <span>PJOK</span></label>
                    <label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" class="rounded"> <span>BK / Bimbingan Konseling</span></label>
                    <label class="flex items-center gap-2 text-sm cursor-pointer"><input type="checkbox" class="rounded"> <span>Prakarya</span></label>
                </div>
            </div>
            <div>
                <label class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Wali Kelas</label>
                <select class="input-field">
                    <option value="">— Tidak sebagai wali kelas —</option>
                    <option {{ $edit ? 'selected' : '' }}>XII IPA 1</option>
                    <option>XII IPA 2</option>
                    <option>XI IPA 1</option>
                </select>
                <p class="text-xs text-muted mt-1">Kosongkan jika hanya guru mapel</p>
            </div>
            <div>
                <label class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Nomor WhatsApp</label>
                <input type="tel" class="input-field" value="{{ $edit ? '081234567890' : '' }}" placeholder="08xx-xxxx-xxxx">
            </div>
            <div>
                <label class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Email</label>
                <input type="email" class="input-field" value="{{ $edit ? 'budi@sch.id' : '' }}" placeholder="email@domain.com">
            </div>
            <div>
                <label class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Tempat Lahir</label>
                <input type="text" class="input-field" value="{{ $edit ? 'Tangerang' : '' }}" placeholder="Kota lahir">
            </div>
            <div>
                <label class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Tanggal Lahir</label>
                <input type="date" class="input-field">
            </div>
            <div>
                <label class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Pendidikan Terakhir</label>
                <input type="text" class="input-field" value="{{ $edit ? 'S1 Pendidikan' : '' }}" placeholder="S1 / S2">
            </div>
        </div>
        <div>
            <label class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Alamat</label>
            <textarea class="input-field" rows="2" placeholder="Alamat lengkap">{{ $edit ? 'Jl. Contoh No. 1' : '' }}</textarea>
        </div>

        <div class="flex gap-3 pt-2">
            <a href="{{ route('admin.guru') }}" class="btn-outline flex-1 !py-2.5">Batal</a>
            <button type="button" onclick="showLoading('Menyimpan data...');setTimeout(()=>{window.location='{{ route('admin.guru') }}'},800)" class="btn-brand flex-1 !py-2.5">Simpan</button>
        </div>
    </form>
</div>
@endsection
