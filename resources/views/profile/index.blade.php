@extends('layouts.app')

@section('title', 'Profil - SentriSiswa')

@section('page-title', 'Profil')

@section('content')
<?php $role = \Illuminate\Support\Facades\Session::get('user_role', request()->query('role', 'kesiswaan')); ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <!-- Profile Card -->
    <div class="card text-center anim-up">
        <div class="relative inline-block mb-4">
            <div class="w-28 h-28 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center mx-auto border-4 border-white shadow-lg">
                <i class="bi bi-person-fill text-5xl text-blue-500"></i>
            </div>
            <button onclick="showToast('UI Only: Upload foto!', 'info')" class="absolute bottom-1 right-1/2 translate-x-8 w-9 h-9 bg-blue-600 text-white rounded-full flex items-center justify-center shadow-lg hover:bg-blue-700 transition">
                <i class="bi bi-camera-fill text-sm"></i>
            </button>
        </div>
        @if($role === 'siswa')
            <h3 class="text-lg font-bold text-slate-900">Ahmad Fauzi</h3>
            <p class="text-sm text-slate-500">XII IPA 1 &middot; NIS: S001</p>
            <div class="mt-4 flex justify-center gap-2">
                <span class="badge badge-green">Aktif</span>
                <span class="badge badge-blue">95 Poin</span>
            </div>
            <div class="mt-5 pt-5 border-t border-slate-100 text-sm space-y-2 text-left">
                <div class="flex justify-between"><span class="text-slate-500">Poin Pelanggaran</span><span class="font-bold text-emerald-600">95 / 100</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Kehadiran</span><span class="font-bold">92%</span></div>
            </div>
        @else
            <h3 class="text-lg font-bold text-slate-900">Budi Santoso, S.Pd</h3>
            <p class="text-sm text-slate-500">Wali Kelas XII IPA 1</p>
            <div class="mt-4 flex justify-center gap-2">
                <span class="badge badge-blue">Wali Kelas</span>
                <span class="badge badge-green">Guru Mapel</span>
            </div>
            <div class="mt-5 pt-5 border-t border-slate-100 text-sm space-y-2 text-left">
                <div class="flex justify-between"><span class="text-slate-500">NIP</span><span class="font-semibold">1234567890</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Bergabung</span><span class="font-semibold">Jan 2024</span></div>
            </div>
        @endif
    </div>

    <!-- Form -->
    <div class="lg:col-span-2 space-y-5">
        <div class="card anim-up" style="animation-delay:.1s">
            <h3 class="font-bold text-slate-900 mb-4">Informasi Pribadi</h3>
            <form class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Nama Lengkap</label>
                        <input type="text" value="{{ $role === 'siswa' ? 'Ahmad Fauzi' : 'Budi Santoso, S.Pd' }}" class="input-field">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">{{ $role === 'siswa' ? 'NIS/NISN' : 'NIP' }}</label>
                        <input type="text" value="{{ $role === 'siswa' ? 'S001' : '1234567890' }}" class="input-field bg-slate-50" disabled>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Email</label>
                        <input type="email" value="{{ $role === 'siswa' ? 'ahmad@siswa.sch.id' : 'budi.santoso@sman11tng.sch.id' }}" class="input-field">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Nomor WhatsApp</label>
                        <input type="tel" value="081234567890" class="input-field">
                    </div>
                    @if($role === 'siswa')
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Kelas</label>
                        <input type="text" value="XII IPA 1" class="input-field bg-slate-50" disabled>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Jenis Kelamin</label>
                        <select class="input-field"><option>Laki-laki</option><option>Perempuan</option></select>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">
                            <i class="bi bi-whatsapp text-emerald-600"></i> WhatsApp Orang Tua / Wali
                        </label>
                        <input type="tel" value="081398765432" class="input-field" placeholder="08xx-xxxx-xxxx">
                        <p class="text-[10px] text-slate-400 mt-1">Digunakan untuk notifikasi presensi & pelanggaran</p>
                    </div>
                    @endif
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Tempat Lahir</label>
                        <input type="text" placeholder="Masukkan tempat lahir" class="input-field">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Tanggal Lahir</label>
                        <input type="date" class="input-field">
                    </div>
                </div>
            </form>
        </div>

        @if($role !== 'siswa')
        <div class="card anim-up" style="animation-delay:.15s">
            <h3 class="font-bold text-slate-900 mb-4">Informasi Kepegawaian</h3>
            <form class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Pangkat/Golongan</label>
                        <select class="input-field"><option>Pilih</option><option>IIIa</option><option>IIIb</option><option>IVa</option></select>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Jabatan</label>
                        <input type="text" value="Wali Kelas" class="input-field">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Mata Pelajaran</label>
                        <input type="text" value="Matematika" class="input-field">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Pendidikan Terakhir</label>
                        <input type="text" value="S1 Pendidikan Matematika" class="input-field">
                    </div>
                </div>
            </form>
        </div>
        @endif

        <div class="card anim-up" style="animation-delay:.2s">
            <h3 class="font-bold text-slate-900 mb-4">Keamanan</h3>
            <form class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Password Baru</label>
                        <input type="password" class="input-field" placeholder="••••••••">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Konfirmasi Password</label>
                        <input type="password" class="input-field" placeholder="••••••••">
                    </div>
                </div>
            </form>
        </div>

        <div class="flex gap-3">
            <button type="button" onclick="showToast('UI Only: Profil diperbarui!', 'success')" class="btn-brand">
                <i class="bi bi-check-circle"></i> Simpan Perubahan
            </button>
        </div>
    </div>
</div>
@endsection