@extends('layouts.app')

@section('title', 'Dashboard Kesiswaan')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Dashboard Kesiswaan</h1>
        <p class="text-sm text-slate-500 mt-0.5">Super Admin &middot; {{ now()->translatedFormat('l, d F Y') }}</p>
    </div>
    <span class="badge badge-blue self-start">Admin</span>
</div>

<!-- Stats -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="card anim-up"><p class="text-xs font-bold text-slate-400 uppercase">Total Guru</p><p class="stat-value mt-1">42</p></div>
    <div class="card anim-up"><p class="text-xs font-bold text-slate-400 uppercase">Total Siswa</p><p class="stat-value mt-1">360</p></div>
    <div class="card anim-up"><p class="text-xs font-bold text-slate-400 uppercase">Total Kelas</p><p class="stat-value mt-1">12</p></div>
    <div class="card anim-up"><p class="text-xs font-bold text-slate-400 uppercase">Hadir Hari Ini</p><p class="stat-value text-emerald-600 mt-1">342</p></div>
</div>

<!-- Quick Actions + Upload -->
<div class="grid lg:grid-cols-3 gap-5 mb-6">
    <div class="card lg:col-span-2 anim-up" style="animation-delay:.1s">
        <h3 class="font-bold text-slate-900 mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <button onclick="showModal('addGuruModal')" class="flex flex-col items-center gap-2 p-4 border-2 border-slate-200 rounded-xl hover:border-blue-300 hover:bg-blue-50/50 transition-all group">
                <i class="bi bi-person-plus-fill text-2xl text-blue-500 group-hover:scale-110 transition-transform"></i>
                <span class="text-xs font-semibold">Tambah Guru</span>
            </button>
            <button onclick="showModal('addSiswaModal')" class="flex flex-col items-center gap-2 p-4 border-2 border-slate-200 rounded-xl hover:border-emerald-300 hover:bg-emerald-50/50 transition-all group">
                <i class="bi bi-person-plus-fill text-2xl text-emerald-500 group-hover:scale-110 transition-transform"></i>
                <span class="text-xs font-semibold">Tambah Siswa</span>
            </button>
            <button onclick="showModal('addClassModal')" class="flex flex-col items-center gap-2 p-4 border-2 border-slate-200 rounded-xl hover:border-purple-300 hover:bg-purple-50/50 transition-all group">
                <i class="bi bi-plus-circle-fill text-2xl text-purple-500 group-hover:scale-110 transition-transform"></i>
                <span class="text-xs font-semibold">Tambah Kelas</span>
            </button>
            <button onclick="showModal('uploadCSVModal')" class="flex flex-col items-center gap-2 p-4 border-2 border-slate-200 rounded-xl hover:border-amber-300 hover:bg-amber-50/50 transition-all group">
                <i class="bi bi-cloud-upload-fill text-2xl text-amber-500 group-hover:scale-110 transition-transform"></i>
                <span class="text-xs font-semibold">Upload CSV</span>
            </button>
        </div>
    </div>

    <div class="card anim-up" style="animation-delay:.15s">
        <h3 class="font-bold text-slate-900 mb-4">Upload Tata Tertib</h3>
        <div class="border-2 border-dashed border-slate-200 rounded-xl p-6 text-center">
            <div class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <i class="bi bi-file-earmark-pdf-fill text-red-400 text-2xl"></i>
            </div>
            <p class="text-sm font-medium text-slate-600 mb-3">Belum ada file PDF</p>
            <button class="btn-outline !py-2 !text-xs w-full"><i class="bi bi-upload"></i> Pilih PDF</button>
        </div>
    </div>
</div>

<!-- Aktivitas Terbaru -->
<div class="card mb-6 anim-up" style="animation-delay:.2s">
    <h3 class="font-bold text-slate-900 mb-4">Aktivitas Terbaru</h3>
    <div class="space-y-2">
        <div class="flex items-center gap-3 p-3 hover:bg-slate-50 rounded-xl">
            <div class="w-9 h-9 bg-emerald-100 rounded-full flex items-center justify-center shrink-0"><i class="bi bi-check-lg text-emerald-500"></i></div>
            <div class="flex-1"><p class="text-sm font-semibold">Presensi pagi selesai - XII IPA 1</p><p class="text-xs text-slate-500">Wali Kelas: Budi Santoso &middot; 07:30</p></div>
            <span class="badge badge-green">Selesai</span>
        </div>
        <div class="flex items-center gap-3 p-3 hover:bg-slate-50 rounded-xl">
            <div class="w-9 h-9 bg-red-100 rounded-full flex items-center justify-center shrink-0"><i class="bi bi-exclamation-triangle-fill text-red-500 text-sm"></i></div>
            <div class="flex-1"><p class="text-sm font-semibold">Poin pelanggaran: Ahmad Fauzi (-10)</p><p class="text-xs text-slate-500">Kesiswaan &middot; 08:15</p></div>
            <span class="badge badge-red">Pelanggaran</span>
        </div>
        <div class="flex items-center gap-3 p-3 hover:bg-slate-50 rounded-xl">
            <div class="w-9 h-9 bg-blue-100 rounded-full flex items-center justify-center shrink-0"><i class="bi bi-person-plus-fill text-blue-500"></i></div>
            <div class="flex-1"><p class="text-sm font-semibold">Guru baru: Siti Nurhaliza, M.Pd</p><p class="text-xs text-slate-500">Kesiswaan &middot; 09:00</p></div>
            <span class="badge badge-blue">Data Baru</span>
        </div>
    </div>
</div>

<!-- Data Guru Preview -->
<div class="card anim-up" style="animation-delay:.25s">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <div><h3 class="font-bold text-slate-900">Data Guru</h3><p class="text-xs text-slate-500">Kelola data master guru</p></div>
        <button onclick="showModal('addGuruModal')" class="btn-brand !text-xs"><i class="bi bi-plus-lg"></i> Tambah</button>
    </div>
    <div class="table-container">
        <table class="w-full">
            <thead><tr><th class="table-header">NIP</th><th class="table-header">Nama</th><th class="table-header">Role</th><th class="table-header">Kelas</th><th class="table-header">Aksi</th></tr></thead>
            <tbody>
                <tr>
                    <td class="table-cell">1234567890</td>
                    <td class="table-cell font-semibold">Budi Santoso, S.Pd</td>
                    <td class="table-cell"><span class="badge badge-blue">Wali Kelas</span> <span class="badge badge-green ml-1">Mapel</span></td>
                    <td class="table-cell">XII IPA 1</td>
                    <td class="table-cell"><button class="text-blue-600 hover:text-blue-800 mr-2"><i class="bi bi-pencil"></i></button><button class="text-red-500 hover:text-red-700"><i class="bi bi-trash"></i></button></td>
                </tr>
                <tr>
                    <td class="table-cell">0987654321</td>
                    <td class="table-cell font-semibold">Siti Nurhaliza, M.Pd</td>
                    <td class="table-cell"><span class="badge badge-red">BK</span> <span class="badge badge-green ml-1">Mapel</span></td>
                    <td class="table-cell">-</td>
                    <td class="table-cell"><button class="text-blue-600 hover:text-blue-800 mr-2"><i class="bi bi-pencil"></i></button><button class="text-red-500 hover:text-red-700"><i class="bi bi-trash"></i></button></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Overlay -->
<div id="modalOverlay" class="hidden fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4" onclick="if(event.target===this)closeModal()"></div>

<!-- Shared Modal Wrapper -->
<div id="modalContent" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-50 bg-white rounded-2xl shadow-2xl w-full max-w-md p-6"></div>
</div>

@push('scripts')
<script>
let currentModal = '';

function showModal(id) {
    currentModal = id;
    let title='', content='';
    if (id==='addGuruModal') {
        title='Tambah Guru';
        content=`<form class="space-y-4"><div><label class="text-xs font-bold text-slate-500 uppercase mb-1 block">NIP</label><input class="input-field" placeholder="Masukkan NIP"></div><div><label class="text-xs font-bold text-slate-500 uppercase mb-1 block">Nama</label><input class="input-field" placeholder="Nama lengkap"></div><div><label class="text-xs font-bold text-slate-500 uppercase mb-1 block">Role</label><div class="flex gap-3 text-sm"><label class="flex items-center gap-1.5"><input type="checkbox" class="rounded"> Wali Kelas</label><label class="flex items-center gap-1.5"><input type="checkbox" class="rounded"> Guru Mapel</label><label class="flex items-center gap-1.5"><input type="checkbox" class="rounded"> BK</label></div></div><div><label class="text-xs font-bold text-slate-500 uppercase mb-1 block">Kelas</label><select class="input-field"><option>Pilih</option><option>XII IPA 1</option></select></div><div class="flex gap-3 pt-2"><button type="button" onclick="closeModal()" class="btn-outline flex-1 !py-2.5">Batal</button><button type="button" onclick="alert('UI Only: Data tersimpan!');closeModal()" class="btn-brand flex-1 !py-2.5">Simpan</button></div></form>`;
    } else if (id==='addSiswaModal') {
        title='Tambah Siswa';
        content=`<form class="space-y-4"><div><label class="text-xs font-bold text-slate-500 uppercase mb-1 block">NIS/NISN</label><input class="input-field" placeholder="Masukkan NIS"></div><div><label class="text-xs font-bold text-slate-500 uppercase mb-1 block">Nama</label><input class="input-field" placeholder="Nama lengkap"></div><div><label class="text-xs font-bold text-slate-500 uppercase mb-1 block">Kelas</label><select class="input-field"><option>Pilih</option><option>XII IPA 1</option></select></div><div class="flex gap-3 pt-2"><button type="button" onclick="closeModal()" class="btn-outline flex-1 !py-2.5">Batal</button><button type="button" onclick="alert('UI Only: Data tersimpan!');closeModal()" class="btn-brand flex-1 !py-2.5">Simpan</button></div></form>`;
    } else if (id==='addClassModal') {
        title='Tambah Kelas';
        content=`<form class="space-y-4"><div><label class="text-xs font-bold text-slate-500 uppercase mb-1 block">Nama Kelas</label><input class="input-field" placeholder="Contoh: XII IPA 1"></div><div><label class="text-xs font-bold text-slate-500 uppercase mb-1 block">Wali Kelas</label><select class="input-field"><option>Pilih</option><option>Budi Santoso, S.Pd</option></select></div><div class="flex gap-3 pt-2"><button type="button" onclick="closeModal()" class="btn-outline flex-1 !py-2.5">Batal</button><button type="button" onclick="alert('UI Only: Kelas ditambahkan!');closeModal()" class="btn-brand flex-1 !py-2.5">Simpan</button></div></form>`;
    } else if (id==='uploadCSVModal') {
        title='Upload CSV/Excel';
        content=`<div class="space-y-4"><div><label class="text-xs font-bold text-slate-500 uppercase mb-2 block">Tipe Data</label><div class="flex gap-3"><label class="flex-1 cursor-pointer"><input type="radio" name="type" checked class="peer hidden"><div class="border-2 border-slate-200 rounded-xl p-3 text-center peer-checked:border-blue-500 peer-checked:bg-blue-50"><i class="bi bi-person-badge text-xl"></i><p class="text-xs font-semibold mt-1">Guru</p></div></label><label class="flex-1 cursor-pointer"><input type="radio" name="type" class="peer hidden"><div class="border-2 border-slate-200 rounded-xl p-3 text-center peer-checked:border-blue-500 peer-checked:bg-blue-50"><i class="bi bi-mortarboard text-xl"></i><p class="text-xs font-semibold mt-1">Siswa</p></div></label></div></div><div class="border-2 border-dashed border-slate-200 rounded-xl p-6 text-center"><i class="bi bi-cloud-upload text-3xl text-slate-300 mb-2 block"></i><p class="text-sm text-slate-600">Drag & drop atau klik</p><p class="text-xs text-slate-400">.csv / .xlsx (max 5MB)</p><button class="btn-outline !py-2 !text-xs mt-3">Pilih File</button></div><div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-xs text-blue-700">Format CSV Guru: <strong>Nama, NIP</strong><br>Format CSV Siswa: <strong>Nama, NIS, Kelas</strong></div><div class="flex gap-3"><button type="button" onclick="closeModal()" class="btn-outline flex-1 !py-2.5">Batal</button><button type="button" onclick="alert('UI Only: File diupload!');closeModal()" class="btn-brand flex-1 !py-2.5">Upload</button></div></div>`;
    }
    const mc = document.getElementById('modalContent');
    mc.innerHTML = `<div class="flex justify-between items-center mb-4"><h3 class="font-bold text-lg">${title}</h3><button onclick="closeModal()" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg text-xl"></i></button></div>${content}`;
    document.getElementById('modalOverlay').classList.remove('hidden');
    mc.classList.remove('hidden');
}

function closeModal() {
    document.getElementById('modalOverlay').classList.add('hidden');
    document.getElementById('modalContent').classList.add('hidden');
}
</script>
@endpush
@endsection