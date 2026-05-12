@extends('layouts.app')

@section('title', 'Profil Guru')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Profil Guru</h1>
        <p class="text-sm text-slate-500 mt-0.5">Lengkapi data profil Anda</p>
    </div>
</div>

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
        <h3 class="text-lg font-bold text-slate-900">Budi Santoso, S.Pd</h3>
        <p class="text-sm text-slate-500">Wali Kelas XII IPA 1</p>
        <div class="mt-4 flex justify-center gap-2">
            <span class="badge badge-blue">Wali Kelas</span>
            <span class="badge badge-green">Guru Mapel</span>
        </div>
        <div class="mt-6 pt-5 border-t border-slate-100 text-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-slate-500">NIP</span>
                <span class="font-semibold">1234567890</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-slate-500">Bergabung</span>
                <span class="font-semibold">Januari 2024</span>
            </div>
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
                        <input type="text" value="Budi Santoso, S.Pd" class="input-field">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">NIP</label>
                        <input type="text" value="1234567890" class="input-field bg-slate-50" disabled>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Email</label>
                        <input type="email" value="budi.santoso@sman11tng.sch.id" class="input-field">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Nomor WhatsApp</label>
                        <input type="tel" value="081234567890" class="input-field">
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
            <h3 class="font-bold text-slate-900 mb-4">Informasi Kepegawaian</h3>
            <form class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Pangkat/Golongan</label>
                        <select class="input-field">
                            <option>Pilih Golongan</option><option>IIIa</option><option>IIIb</option><option>IVa</option>
                        </select>
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
                <div class="pt-2">
                    <button type="button" onclick="showToast('UI Only: Profil diperbarui!', 'success')" class="btn-brand">
                        <i class="bi bi-check-circle"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection