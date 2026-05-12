@extends('layouts.app')

@section('title', 'Pengaturan - SentriSiswa')

@section('page-title', 'Pengaturan')

@section('content')
<?php $role = \Illuminate\Support\Facades\Session::get('user_role', 'siswa'); ?>

<!-- Tab Buttons -->
<div class="flex gap-1 p-1 bg-[#edeeef] rounded-lg w-fit mb-6">
    <button onclick="switchTab('profil')" id="tab-profil" class="px-4 py-2 rounded-lg text-sm font-bold bg-white shadow-sm text-[#1c6880] transition">Profil</button>
    <button onclick="switchTab('biodata')" id="tab-biodata" class="px-4 py-2 rounded-lg text-sm font-bold text-muted hover:text-[#191c1d] transition">Biodata</button>
</div>

<!-- Panel: Profil -->
<div id="panel-profil">
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
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Email</label>
                            <input type="email" value="ahmad@siswa.sch.id" class="input-field">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Nomor WhatsApp</label>
                            <input type="tel" value="081234567890" class="input-field">
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
                        <div class="md:col-span-2">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">
                                <i class="bi bi-whatsapp text-emerald-600"></i> WhatsApp Orang Tua / Wali
                            </label>
                            <input type="tel" value="081398765432" class="input-field" placeholder="08xx-xxxx-xxxx">
                            <p class="text-[10px] text-slate-400 mt-1">Digunakan untuk notifikasi presensi & pelanggaran</p>
                        </div>
                    </div>
                </form>
            </div>

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

            <button type="button" onclick="showToast('UI Only: Profil diperbarui!', 'success')" class="btn-brand">
                <i class="bi bi-check-circle"></i> Simpan Perubahan
            </button>
        </div>
    </div>
</div>

<!-- Panel: Biodata -->
<div id="panel-biodata" class="hidden">
    <div class="grid lg:grid-cols-2 gap-5">
        <!-- Foto + Identitas Ringkas -->
        <div class="card lg:col-span-2 anim-up">
            <div class="flex flex-col sm:flex-row gap-6 items-start">
                <div class="shrink-0">
                    <div class="w-28 h-36 bg-slate-100 border-2 border-slate-300 rounded flex items-center justify-center">
                        <div class="text-center">
                            <i class="bi bi-person-fill text-4xl text-slate-300"></i>
                            <p class="text-[9px] text-slate-400 mt-1 font-medium">3 x 4</p>
                        </div>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1 text-center">Pas Photo</p>
                </div>
                <div class="flex-1 space-y-2">
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nama Lengkap</label>
                        <p class="text-base font-bold text-slate-900">Ahmad Fauzi</p>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <div><label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">NIS</label><p class="text-sm font-semibold text-slate-900">S001</p></div>
                        <div><label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">NISN</label><p class="text-sm font-semibold text-slate-900">0087654321</p></div>
                        <div><label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kelas</label><p class="text-sm font-semibold text-slate-900">XII IPA 1</p></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Identitas -->
        <div class="card anim-up" style="animation-delay:.08s">
            <div class="flex items-center gap-2 mb-5"><div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center"><i class="bi bi-person-fill text-blue-600"></i></div><h3 class="font-bold text-slate-900">Identitas</h3></div>
            <div class="space-y-3">
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tempat Lahir</label><p class="text-sm font-semibold text-slate-900 mt-0.5">Tangerang</p></div>
                    <div><label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tanggal Lahir</label><p class="text-sm font-semibold text-slate-900 mt-0.5">17 Agustus 2008</p></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Jenis Kelamin</label><p class="text-sm font-semibold text-slate-900 mt-0.5">Laki-laki</p></div>
                    <div><label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Agama</label><p class="text-sm font-semibold text-slate-900 mt-0.5">Islam</p></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status Keluarga</label><p class="text-sm font-semibold text-slate-900 mt-0.5">Anak Kandung</p></div>
                    <div><label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Anak ke</label><p class="text-sm font-semibold text-slate-900 mt-0.5">2 dari 3</p></div>
                </div>
            </div>
        </div>

        <!-- Alamat & Kontak -->
        <div class="card anim-up" style="animation-delay:.16s">
            <div class="flex items-center gap-2 mb-5"><div class="w-9 h-9 bg-emerald-100 rounded-lg flex items-center justify-center"><i class="bi bi-geo-alt-fill text-emerald-600"></i></div><h3 class="font-bold text-slate-900">Alamat & Kontak</h3></div>
            <div class="space-y-3">
                <div><label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Alamat</label><p class="text-sm font-semibold text-slate-900 mt-0.5">Jl. Merdeka No. 42, Kab. Tangerang</p></div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Telepon</label><p class="text-sm font-semibold text-slate-900 mt-0.5">021-5551234</p></div>
                    <div><label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Sekolah Asal</label><p class="text-sm font-semibold text-slate-900 mt-0.5">SMP Negeri 3</p></div>
                </div>
                <div><label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Diterima</label><p class="text-sm font-semibold text-slate-900 mt-0.5">Kelas X &mdash; 15 Juli 2023</p></div>
            </div>
        </div>

        <!-- Orang Tua -->
        <div class="card anim-up" style="animation-delay:.24s">
            <div class="flex items-center gap-2 mb-5"><div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center"><i class="bi bi-people-fill text-purple-600"></i></div><h3 class="font-bold text-slate-900">Orang Tua</h3></div>
            <div class="space-y-3">
                <div class="p-3 bg-slate-50 rounded-xl"><p class="text-[10px] font-bold text-purple-500 uppercase">Ayah</p><p class="text-sm font-bold mt-1">H. Supriyanto, S.Pd.</p><p class="text-xs text-slate-500">Pekerjaan: Guru PNS &middot; <span class="text-[#43474f]"><i class="bi bi-telephone"></i> 081234567891</span></p></div>
                <div class="p-3 bg-slate-50 rounded-xl"><p class="text-[10px] font-bold text-purple-500 uppercase">Ibu</p><p class="text-sm font-bold mt-1">Hj. Siti Rahmawati</p><p class="text-xs text-slate-500">Pekerjaan: IRT &middot; <span class="text-[#43474f]"><i class="bi bi-telephone"></i> 081398765432</span></p></div>
                <div><label class="text-[10px] font-bold text-slate-400 uppercase">Alamat</label><p class="text-sm font-semibold mt-0.5">Jl. Merdeka No. 42, Kab. Tangerang</p></div>
            </div>
        </div>

        <!-- Wali -->
        <div class="card anim-up" style="animation-delay:.32s">
            <div class="flex items-center gap-2 mb-5"><div class="w-9 h-9 bg-amber-100 rounded-lg flex items-center justify-center"><i class="bi bi-person-badge text-amber-600"></i></div><h3 class="font-bold text-slate-900">Wali</h3></div>
            <div class="p-4 bg-slate-50 rounded-xl"><p class="text-sm text-slate-400 italic">Tidak ada wali (orang tua masih lengkap)</p></div>
        </div>
    </div>
</div>

<script>
function switchTab(tab) {
    ['profil','biodata'].forEach(t => {
        document.getElementById('panel-'+t).classList.add('hidden');
        const btn = document.getElementById('tab-'+t);
        btn.classList.remove('bg-white','shadow-sm','text-[#1c6880]');
        btn.classList.add('text-muted');
    });
    document.getElementById('panel-'+tab).classList.remove('hidden');
    const btn = document.getElementById('tab-'+tab);
    btn.classList.add('bg-white','shadow-sm','text-[#1c6880]');
    btn.classList.remove('text-muted');
}
</script>
@endsection