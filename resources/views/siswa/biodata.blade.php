@extends('layouts.app')

@section('title', 'Biodata - SentriSiswa')

@section('page-title', 'Biodata')

@section('content')
<!-- Foto + Identitas Ringkas -->
<div class="card anim-up mb-5">
    <div class="flex flex-col sm:flex-row gap-6 items-start">
        <!-- Pas Photo 3x4 -->
        <div class="shrink-0">
            <div class="w-28 h-36 bg-slate-100 border-2 border-slate-300 rounded flex items-center justify-center">
                <div class="text-center">
                    <i class="bi bi-person-fill text-4xl text-slate-300"></i>
                    <p class="text-[9px] text-slate-400 mt-1 font-medium">3 x 4</p>
                </div>
            </div>
            <p class="text-[10px] text-slate-400 mt-1 text-center">Pas Photo</p>
        </div>
        <!-- Info Ringkas -->
        <div class="flex-1 space-y-2">
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nama Lengkap</label>
                <p class="text-base font-bold text-slate-900">Ahmad Fauzi</p>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">NIS</label>
                    <p class="text-sm font-semibold text-slate-900">S001</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">NISN</label>
                    <p class="text-sm font-semibold text-slate-900">0087654321</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kelas</label>
                    <p class="text-sm font-semibold text-slate-900">XII IPA 1</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-5">
    <!-- Identitas Peserta Didik -->
    <div class="card anim-up" style="animation-delay:.08s">
        <div class="flex items-center gap-2 mb-5">
            <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="bi bi-person-fill text-blue-600"></i>
            </div>
            <h3 class="font-bold text-slate-900">Identitas</h3>
        </div>
        <div class="space-y-3">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tempat Lahir</label>
                    <p class="text-sm font-semibold text-slate-900 mt-0.5">Tangerang</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tanggal Lahir</label>
                    <p class="text-sm font-semibold text-slate-900 mt-0.5">17 Agustus 2008</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Jenis Kelamin</label>
                    <p class="text-sm font-semibold text-slate-900 mt-0.5">Laki-laki</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Agama</label>
                    <p class="text-sm font-semibold text-slate-900 mt-0.5">Islam</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status dalam Keluarga</label>
                    <p class="text-sm font-semibold text-slate-900 mt-0.5">Anak Kandung</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Anak ke</label>
                    <p class="text-sm font-semibold text-slate-900 mt-0.5">2 dari 3 bersaudara</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Alamat & Kontak -->
    <div class="card anim-up" style="animation-delay:.16s">
        <div class="flex items-center gap-2 mb-5">
            <div class="w-9 h-9 bg-emerald-100 rounded-lg flex items-center justify-center">
                <i class="bi bi-geo-alt-fill text-emerald-600"></i>
            </div>
            <h3 class="font-bold text-slate-900">Alamat & Kontak</h3>
        </div>
        <div class="space-y-3">
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Alamat Peserta Didik</label>
                <p class="text-sm font-semibold text-slate-900 mt-0.5">Jl. Merdeka No. 42, RT 003 RW 005, Kel. Sukamaju, Kec. Cikupa, Kab. Tangerang, Banten 15710</p>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nomor Telepon</label>
                    <p class="text-sm font-semibold text-slate-900 mt-0.5">021-5551234</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Sekolah Asal</label>
                    <p class="text-sm font-semibold text-slate-900 mt-0.5">SMP Negeri 3 Cikupa</p>
                </div>
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Diterima di Sekolah Ini</label>
                <p class="text-sm font-semibold text-slate-900 mt-0.5">Kelas X &mdash; 15 Juli 2023</p>
            </div>
        </div>
    </div>

    <!-- Orang Tua -->
    <div class="card anim-up" style="animation-delay:.24s">
        <div class="flex items-center gap-2 mb-5">
            <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="bi bi-people-fill text-purple-600"></i>
            </div>
            <h3 class="font-bold text-slate-900">Orang Tua</h3>
        </div>
        <div class="space-y-4">
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                <p class="text-[10px] font-bold text-purple-500 uppercase tracking-wider mb-2">Ayah</p>
                <p class="text-sm font-bold text-slate-900">H. Supriyanto, S.Pd.</p>
                <div class="flex items-center justify-between text-xs text-slate-500 mt-1">
                    <span>Pekerjaan: Guru PNS</span>
                    <span class="text-emerald-600"><i class="bi bi-whatsapp"></i> 081234567891</span>
                </div>
            </div>
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                <p class="text-[10px] font-bold text-purple-500 uppercase tracking-wider mb-2">Ibu</p>
                <p class="text-sm font-bold text-slate-900">Hj. Siti Rahmawati</p>
                <div class="flex items-center justify-between text-xs text-slate-500 mt-1">
                    <span>Pekerjaan: Ibu Rumah Tangga</span>
                    <span class="text-emerald-600"><i class="bi bi-whatsapp"></i> 081398765432</span>
                </div>
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Alamat Orang Tua</label>
                <p class="text-sm font-semibold text-slate-900 mt-0.5">Jl. Merdeka No. 42, Kab. Tangerang</p>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Telepon</label>
                    <p class="text-sm font-semibold text-slate-900 mt-0.5">021-5551234</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Wali -->
    <div class="card anim-up" style="animation-delay:.32s">
        <div class="flex items-center gap-2 mb-5">
            <div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center">
                <i class="bi bi-person-badge text-amber-600"></i>
            </div>
            <h3 class="font-bold text-slate-900">Wali Peserta Didik</h3>
        </div>
        <div class="space-y-3">
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                <p class="text-sm text-slate-400 italic">Tidak ada wali (orang tua masih lengkap)</p>
            </div>
            <div>
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Alamat Wali</label>
                <p class="text-sm text-slate-400 mt-0.5">-</p>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Telepon</label>
                    <p class="text-sm text-slate-400 mt-0.5">-</p>
                </div>
                <div>
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pekerjaan Wali</label>
                    <p class="text-sm text-slate-400 mt-0.5">-</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection