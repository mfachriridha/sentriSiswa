@extends('layouts.app')

@section('title', 'Profil Siswa')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Profil Siswa</h1>
        <p class="text-sm text-slate-500 mt-0.5">Lengkapi data profil Anda</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
    <!-- Profile Card -->
    <div class="card text-center anim-up">
        <div class="relative inline-block mb-4">
            <div class="w-28 h-28 bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-full flex items-center justify-center mx-auto border-4 border-white shadow-lg">
                <i class="bi bi-person-fill text-5xl text-emerald-500"></i>
            </div>
            <button onclick="showToast('UI Only: Upload foto!', 'info')" class="absolute bottom-1 right-1/2 translate-x-8 w-9 h-9 bg-emerald-600 text-white rounded-full flex items-center justify-center shadow-lg hover:bg-emerald-700 transition">
                <i class="bi bi-camera-fill text-sm"></i>
            </button>
        </div>
        <h3 class="text-lg font-bold text-slate-900">Ahmad Fauzi</h3>
        <p class="text-sm text-slate-500">XII IPA 1</p>
        <div class="mt-4 flex justify-center gap-2">
            <span class="badge badge-green">Aktif</span>
            <span class="badge badge-blue">NIS: S001</span>
        </div>
        <div class="mt-6 pt-5 border-t border-slate-100">
            <div class="flex items-center justify-between text-sm mb-2">
                <span class="text-slate-500">Poin Pelanggaran</span>
                <span class="font-bold text-emerald-600">95 / 100</span>
            </div>
            <div class="w-full bg-slate-200 rounded-full h-2">
                <div class="bg-emerald-500 h-2 rounded-full" style="width:95%"></div>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Dari saldo awal 100 poin</p>
        </div>
    </div>

    <!-- Profile Form -->
    <div class="lg:col-span-2 space-y-5">
        <div class="card anim-up" style="animation-delay:.1s">
            <h3 class="font-bold text-slate-900 mb-4">Informasi Pribadi</h3>
            <form class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Nama Lengkap</label>
                        <input type="text" value="Ahmad Fauzi" class="input-field">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">NIS/NISN</label>
                        <input type="text" value="S001" class="input-field bg-slate-50" disabled>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Kelas</label>
                        <input type="text" value="XII IPA 1" class="input-field bg-slate-50" disabled>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Jenis Kelamin</label>
                        <select class="input-field"><option>Laki-laki</option><option>Perempuan</option></select>
                    </div>
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

        <div class="card anim-up" style="animation-delay:.15s">
            <h3 class="font-bold text-slate-900 mb-4">Kontak</h3>
            <form class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Email</label>
                        <input type="email" placeholder="email@siswa.sch.id" class="input-field">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">WhatsApp Siswa</label>
                        <input type="tel" placeholder="08xx-xxxx-xxxx" class="input-field">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">WhatsApp Orang Tua/Wali</label>
                        <input type="tel" placeholder="08xx-xxxx-xxxx" class="input-field">
                        <p class="text-[11px] text-slate-400 mt-1">Digunakan untuk notifikasi presensi</p>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Alamat</label>
                        <textarea class="input-field" rows="3" placeholder="Alamat lengkap"></textarea>
                    </div>
                </div>
                <div class="pt-2">
                    <button type="button" onclick="showToast('UI Only: Profil diperbarui!', 'success')" class="btn-brand">
                        <i class="bi bi-check-circle"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <div class="card anim-up" style="animation-delay:.2s">
            <h3 class="font-bold text-slate-900 mb-4">Data Raport</h3>
            <div class="grid grid-cols-3 gap-3">
                <div class="p-4 bg-blue-50 border border-blue-100 rounded-xl text-center">
                    <p class="text-[11px] font-bold text-blue-500 uppercase">Semester 1</p>
                    <p class="text-2xl font-extrabold text-blue-700 mt-1">85.5</p>
                </div>
                <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-xl text-center">
                    <p class="text-[11px] font-bold text-emerald-500 uppercase">Semester 2</p>
                    <p class="text-2xl font-extrabold text-emerald-700 mt-1">88.0</p>
                </div>
                <div class="p-4 bg-purple-50 border border-purple-100 rounded-xl text-center">
                    <p class="text-[11px] font-bold text-purple-500 uppercase">Rata-rata</p>
                    <p class="text-2xl font-extrabold text-purple-700 mt-1">86.75</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection