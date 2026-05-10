@extends('layouts.app')

@section('title', 'Form Siswa - Admin')

@section('page-title', 'Form Siswa')

@section('content')
<?php $s = $student ?? null; $isEdit = $s !== null; ?>
<div class="mb-6">
    <a href="{{ route('admin.siswa') }}" class="text-sm text-[#0062a0] hover:text-[#001e40] font-semibold">&larr; Kembali</a>
</div>

<div class="card anim-up">
    <h3 class="font-bold text-slate-900 mb-1">{{ $isEdit ? 'Edit' : 'Tambah' }} Siswa</h3>
    <p class="text-xs text-muted mb-5">{{ $isEdit ? 'Edit data siswa ' . $s->name : 'Lengkapi data siswa.' }}</p>

    <form class="space-y-5" method="POST" action="{{ $isEdit ? route('admin.siswa.update', $s) : route('admin.siswa.store') }}">
        @csrf @if($isEdit) @method('PUT') @endif

        <div class="flex flex-col sm:flex-row gap-6 items-start p-5 bg-slate-50 rounded-xl border border-slate-200">
            <div class="shrink-0">
                <div class="w-28 h-36 bg-white border-2 border-dashed border-slate-300 rounded flex items-center justify-center"><div class="text-center"><i class="bi bi-person-fill text-4xl text-slate-300"></i><p class="text-[9px] text-slate-400 mt-1 font-medium">3 x 4</p></div></div>
            </div>
            <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">NIS *</label><input type="text" name="nis" class="input-field font-mono" value="{{ $s->nis ?? '' }}" required></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">NISN</label><input type="text" name="nisn" class="input-field font-mono" value="{{ $s->nisn ?? '' }}"></div>
            </div>
        </div>

        <!-- Identitas -->
        <div>
            <p class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-3">Identitas</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="md:col-span-2 lg:col-span-3"><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Nama Lengkap *</label><input type="text" name="name" class="input-field" value="{{ $s->name ?? '' }}" required></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Jenis Kelamin</label><select name="gender" class="input-field"><option {{ $isEdit && $s->gender === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option><option {{ $isEdit && $s->gender === 'Perempuan' ? 'selected' : '' }}>Perempuan</option></select></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Kelas</label><input type="text" name="kelas_name" class="input-field" value="{{ $s?->kelas?->name ?? '' }}"></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Tempat Lahir</label><input type="text" name="birth_place" class="input-field" value="{{ $s->birth_place ?? '' }}"></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Tanggal Lahir</label><input type="date" name="birth_date" class="input-field" value="{{ $s->birth_date ?? '' }}"></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Agama</label><input type="text" name="religion" class="input-field" value="{{ $s->religion ?? '' }}"></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Anak ke</label><input type="text" name="birth_order" class="input-field" value="{{ $s->birth_order ?? '' }}"></div>
                <div class="md:col-span-2 lg:col-span-3"><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Status Keluarga</label><input type="text" name="family_status" class="input-field" value="{{ $s->family_status ?? '' }}"></div>
            </div>
        </div>

        <hr class="border-slate-200">

        <div>
            <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-3">Alamat & Sekolah</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="md:col-span-2 lg:col-span-3"><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Alamat</label><textarea name="address" class="input-field" rows="2">{{ $s->address ?? '' }}</textarea></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Telepon</label><input type="text" name="home_phone" class="input-field" value="{{ $s->home_phone ?? '' }}"></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Sekolah Asal</label><input type="text" name="prev_school" class="input-field" value="{{ $s->prev_school ?? '' }}"></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Diterima di Kelas</label><input type="text" name="admission_class" class="input-field" value="{{ $s->admission_class ?? '' }}"></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Tanggal Diterima</label><input type="text" name="admission_date" class="input-field" value="{{ $s->admission_date ?? '' }}"></div>
            </div>
        </div>

        <hr class="border-slate-200">

        <div>
            <p class="text-xs font-bold text-purple-600 uppercase tracking-wider mb-3">Orang Tua</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Nama Ayah</label><input type="text" name="father_name" class="input-field" value="{{ $s->father->name ?? '' }}"></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Pekerjaan Ayah</label><input type="text" name="father_job" class="input-field" value="{{ $s->father->job ?? '' }}"></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Telepon Ayah</label><input type="tel" name="father_phone" class="input-field" value="{{ $s->father->phone ?? '' }}"></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Nama Ibu</label><input type="text" name="mother_name" class="input-field" value="{{ $s->mother->name ?? '' }}"></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Pekerjaan Ibu</label><input type="text" name="mother_job" class="input-field" value="{{ $s->mother->job ?? '' }}"></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Telepon Ibu</label><input type="tel" name="mother_phone" class="input-field" value="{{ $s->mother->phone ?? '' }}"></div>
            </div>
        </div>

        <hr class="border-slate-200">

        <div>
            <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-3">Wali</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Nama Wali</label><input type="text" name="guardian_name" class="input-field" value="{{ $s->guardian->name ?? '' }}"></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Pekerjaan Wali</label><input type="text" name="guardian_job" class="input-field" value="{{ $s->guardian->job ?? '' }}"></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Telepon Wali</label><input type="tel" name="guardian_phone" class="input-field" value="{{ $s->guardian->phone ?? '' }}"></div>
                <div class="md:col-span-2 lg:col-span-3"><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Alamat Wali</label><input type="text" name="guardian_address" class="input-field" value="{{ $s->guardian->address ?? '' }}"></div>
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <a href="{{ route('admin.siswa') }}" class="btn-outline flex-1 !py-2.5">Batal</a>
            <button type="submit" class="btn-brand flex-1 !py-2.5">Simpan</button>
        </div>
    </form>
</div>
@endsection