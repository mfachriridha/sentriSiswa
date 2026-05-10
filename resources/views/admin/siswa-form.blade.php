@extends('layouts.app')

@section('title', 'Form Siswa - Admin')

@section('page-title', 'Form Siswa')

@section('content')
<?php $edit = request()->routeIs('admin.siswa.edit'); ?>
<div class="mb-6">
    <a href="{{ route('admin.siswa') }}" class="text-sm text-[#0062a0] hover:text-[#001e40] font-semibold">&larr; Kembali</a>
</div>

<div class="card anim-up">
    <h3 class="font-bold text-slate-900 mb-1">{{ $edit ? 'Edit' : 'Tambah' }} Siswa</h3>
    <p class="text-xs text-muted mb-5">Lengkapi data siswa di bawah ini. Field boleh dikosongi.</p>

    <form class="space-y-5">
        <!-- Pas Photo + Nama Ringkas -->
        <div class="flex flex-col sm:flex-row gap-6 items-start p-5 bg-slate-50 rounded-xl border border-slate-200">
            <div class="shrink-0">
                <div class="w-28 h-36 bg-white border-2 border-dashed border-slate-300 rounded flex items-center justify-center">
                    <div class="text-center">
                        <i class="bi bi-person-fill text-4xl text-slate-300"></i>
                        <p class="text-[9px] text-slate-400 mt-1 font-medium">3 x 4</p>
                    </div>
                </div>
                <button onclick="alert('UI Only: Upload foto')" class="mt-2 btn-outline !py-1 !px-3 !text-xs w-full"><i class="bi bi-camera"></i> Upload</button>
                <p class="text-[10px] text-muted mt-1 text-center">Pas Photo berwarna</p>
            </div>
            <div class="flex-1">
                <p class="text-sm font-semibold text-slate-600 mb-3">Upload pas photo 3x4 siswa (opsional)</p>
                <p class="text-xs text-muted">Format: JPG/PNG, maks 500KB</p>
            </div>
        </div>

        <!-- Identitas -->
        <div>
            <p class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-3">Identitas</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">NIS <span class="text-red-500">*</span></label>
                    <input type="text" class="input-field" value="{{ $edit ? 'S001' : '' }}" placeholder="Nomor Induk Siswa">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">NISN</label>
                    <input type="text" class="input-field" value="{{ $edit ? '0087654321' : '' }}" placeholder="Nomor Induk Siswa Nasional">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" class="input-field" value="{{ $edit ? 'Ahmad Fauzi' : '' }}" placeholder="Nama lengkap">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Jenis Kelamin</label>
                    <select class="input-field"><option {{ $edit ? 'selected' : '' }}>Laki-laki</option><option>Perempuan</option></select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Kelas</label>
                    <input type="text" class="input-field" value="{{ $edit ? 'XII IPA 1' : '' }}" placeholder="Cth: XII IPA 1">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Tempat Lahir</label>
                    <input type="text" class="input-field" value="{{ $edit ? 'Tangerang' : '' }}" placeholder="Kota lahir">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Tanggal Lahir</label>
                    <input type="date" class="input-field">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Agama</label>
                    <input type="text" class="input-field" value="{{ $edit ? 'Islam' : '' }}" placeholder="Agama">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Anak ke</label>
                    <input type="text" class="input-field" value="{{ $edit ? '2' : '' }}" placeholder="Anak ke berapa">
                </div>
                <div class="md:col-span-2 lg:col-span-3">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Status dalam Keluarga</label>
                    <input type="text" class="input-field" value="{{ $edit ? 'Anak Kandung' : '' }}" placeholder="Anak Kandung / Anak Angkat">
                </div>
            </div>
        </div>

        <hr class="border-slate-200">

        <!-- Alamat & Kontak -->
        <div>
            <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-3">Alamat & Kontak</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="md:col-span-2 lg:col-span-3">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Alamat</label>
                    <textarea class="input-field" rows="2" placeholder="Alamat lengkap">{{ $edit ? 'Jl. Merdeka No. 42, Kab. Tangerang' : '' }}</textarea>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Telepon / No. Rumah</label>
                    <input type="text" class="input-field" value="{{ $edit ? '021-5551234' : '' }}" placeholder="021-xxxx">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Sekolah Asal</label>
                    <input type="text" class="input-field" value="{{ $edit ? 'SMP Negeri 3 Cikupa' : '' }}" placeholder="Nama sekolah sebelumnya">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Diterima (Kelas)</label>
                    <input type="text" class="input-field" value="{{ $edit ? 'Kelas X' : '' }}" placeholder="Cth: Kelas X">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Tanggal Diterima</label>
                    <input type="text" class="input-field" value="{{ $edit ? '15 Juli 2023' : '' }}" placeholder="Tanggal">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block"><i class="bi bi-telephone text-[#43474f]"></i> Nomor Telepon Siswa</label>
                    <input type="tel" class="input-field" value="{{ $edit ? '081312345678' : '' }}" placeholder="08xx-xxxx-xxxx">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block"><i class="bi bi-telephone text-[#43474f]"></i> Nomor Telepon Orang Tua</label>
                    <input type="tel" class="input-field" value="{{ $edit ? '081398765432' : '' }}" placeholder="08xx-xxxx-xxxx">
                    <p class="text-[10px] text-muted mt-1">Digunakan untuk notifikasi presensi</p>
                </div>
            </div>
        </div>

        <hr class="border-slate-200">

        <!-- Orang Tua -->
        <div>
            <p class="text-xs font-bold text-purple-600 uppercase tracking-wider mb-3">Orang Tua</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Nama Ayah</label>
                    <input type="text" class="input-field" value="{{ $edit ? 'H. Supriyanto, S.Pd.' : '' }}" placeholder="Nama ayah">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Pekerjaan Ayah</label>
                    <input type="text" class="input-field" value="{{ $edit ? 'Guru PNS' : '' }}" placeholder="Pekerjaan">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Telepon Ayah</label>
                    <input type="tel" class="input-field" value="{{ $edit ? '081234567891' : '' }}" placeholder="08xx-xxxx-xxxx">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Nama Ibu</label>
                    <input type="text" class="input-field" value="{{ $edit ? 'Hj. Siti Rahmawati' : '' }}" placeholder="Nama ibu">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Pekerjaan Ibu</label>
                    <input type="text" class="input-field" value="{{ $edit ? 'Ibu Rumah Tangga' : '' }}" placeholder="Pekerjaan">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Telepon Ibu</label>
                    <input type="tel" class="input-field" value="{{ $edit ? '081398765432' : '' }}" placeholder="08xx-xxxx-xxxx">
                </div>
                <div class="md:col-span-2 lg:col-span-3">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Alamat Orang Tua (jika beda dengan siswa)</label>
                    <input type="text" class="input-field" value="{{ $edit ? 'Jl. Merdeka No. 42, Kab. Tangerang' : '' }}" placeholder="Alamat orang tua">
                </div>
            </div>
        </div>

        <hr class="border-slate-200">

        <!-- Wali -->
        <div>
            <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-3">Wali Peserta Didik</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Nama Wali</label>
                    <input type="text" class="input-field" placeholder="Nama wali (jika ada)">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Pekerjaan Wali</label>
                    <input type="text" class="input-field" placeholder="Pekerjaan wali">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Telepon Wali</label>
                    <input type="text" class="input-field" placeholder="Nomor telepon wali">
                </div>
                <div class="md:col-span-2 lg:col-span-3">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Alamat Wali</label>
                    <input type="text" class="input-field" placeholder="Alamat wali">
                </div>
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <a href="{{ route('admin.siswa') }}" class="btn-outline flex-1 !py-2.5">Batal</a>
            <button type="button" onclick="alert('UI Only: Data tersimpan!');window.location='{{ route('admin.siswa') }}'" class="btn-brand flex-1 !py-2.5">Simpan</button>
        </div>
    </form>
</div>
@endsection