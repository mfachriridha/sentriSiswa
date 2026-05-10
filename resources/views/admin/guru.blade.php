@extends('layouts.app')

@section('title', 'Manajemen Guru - Admin')

@section('page-title', 'Manajemen Guru')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div class="flex gap-2">
        <button onclick="showModal('tambah')" class="btn-brand !text-xs"><i class="bi bi-plus-lg"></i> Tambah Guru</button>
        <button onclick="showModal('csv')" class="btn-outline !text-xs"><i class="bi bi-upload"></i> Upload CSV</button>
        <a href="#" onclick="alert('UI Only: Download template CSV')" class="btn-outline !text-xs"><i class="bi bi-download"></i> Template CSV</a>
    </div>
</div>

<div class="card anim-up">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <h3 class="font-bold text-slate-900">Data Guru</h3>
        <input type="text" class="input-field !w-48 !py-1.5 !text-xs" placeholder="Cari nama / NIP...">
    </div>
    <div class="table-container">
        <table class="w-full">
            <thead><tr><th class="table-header">NIP</th><th class="table-header">Nama</th><th class="table-header">Jenis Kelamin</th><th class="table-header">WA</th><th class="table-header">Aksi</th></tr></thead>
            <tbody>
                <tr><td class="table-cell">1234567890</td><td class="table-cell font-semibold">Budi Santoso, S.Pd</td><td class="table-cell">Laki-laki</td><td class="table-cell">081234567890</td><td class="table-cell"><button onclick="showModal('edit')" class="text-blue-600 hover:text-blue-800 mr-2"><i class="bi bi-pencil"></i></button><button onclick="alert('Hapus?')" class="text-red-500 hover:text-red-700"><i class="bi bi-trash"></i></button></td></tr>
                <tr><td class="table-cell">0987654321</td><td class="table-cell font-semibold">Siti Nurhaliza, M.Pd</td><td class="table-cell">Perempuan</td><td class="table-cell">081298765432</td><td class="table-cell"><button onclick="showModal('edit')" class="text-blue-600 hover:text-blue-800 mr-2"><i class="bi bi-pencil"></i></button><button onclick="alert('Hapus?')" class="text-red-500 hover:text-red-700"><i class="bi bi-trash"></i></button></td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Overlay -->
<div id="modalOverlay" class="hidden fixed inset-0 z-50 bg-black/40" onclick="closeModal()"></div>
<div id="modalBox" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-50 bg-white rounded-2xl shadow-xl w-full max-w-md p-6 max-h-[90vh] overflow-y-auto"></div>

@push('scripts')
<script>
function showModal(type) {
    const box = document.getElementById('modalBox');
    let html = '';
    if (type === 'tambah' || type === 'edit') {
        html = `<div class="flex justify-between items-center mb-4"><h3 class="font-bold text-lg">${type==='tambah'?'Tambah':'Edit'} Guru</h3><button onclick="closeModal()" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg text-xl"></i></button></div>
        <form class="space-y-3">
            <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">NIP <span class="text-red-500">*</span></label><input type="text" class="input-field" placeholder="1234567890"></div>
            <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Nama Lengkap <span class="text-red-500">*</span></label><input type="text" class="input-field" placeholder="Nama lengkap"></div>
            <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Jenis Kelamin</label><select class="input-field"><option>Laki-laki</option><option>Perempuan</option></select></div>
            <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Tempat Lahir</label><input type="text" class="input-field" placeholder="Kota lahir"></div>
            <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Tanggal Lahir</label><input type="date" class="input-field"></div>
            <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Nomor WhatsApp</label><input type="tel" class="input-field" placeholder="08xx-xxxx-xxxx"></div>
            <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Email</label><input type="email" class="input-field" placeholder="email@domain.com"></div>
            <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Alamat</label><textarea class="input-field" rows="2" placeholder="Alamat"></textarea></div>
            <div class="flex gap-3 pt-2"><button type="button" onclick="closeModal()" class="btn-outline flex-1 !py-2.5">Batal</button><button type="button" onclick="alert('UI Only: Data tersimpan!');closeModal()" class="btn-brand flex-1 !py-2.5">Simpan</button></div>
        </form>`;
    } else if (type === 'csv') {
        html = `<div class="flex justify-between items-center mb-4"><h3 class="font-bold text-lg">Upload CSV Guru</h3><button onclick="closeModal()" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg text-xl"></i></button></div>
        <p class="text-xs text-slate-500 mb-4">Upload file CSV dengan kolom: <strong>Nama, Jenis Kelamin, NIP</strong></p>
        <div class="border-2 border-dashed border-slate-200 rounded-xl p-6 text-center mb-4">
            <i class="bi bi-cloud-upload text-3xl text-slate-300 mb-2 block"></i>
            <p class="text-sm text-slate-600">Klik atau drag file</p>
            <p class="text-xs text-slate-400">.csv (max 2MB)</p>
            <button class="btn-outline !py-2 !text-xs mt-3">Pilih File</button>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-xs text-blue-700">
            <strong>Template:</strong> "Nama","Jenis Kelamin","NIP"<br>
            <strong>Contoh:</strong> "Budi Santoso","Laki-laki","1234567890"
        </div>
        <div class="flex gap-3 pt-2"><button type="button" onclick="closeModal()" class="btn-outline flex-1 !py-2.5">Batal</button><button type="button" onclick="alert('UI Only: CSV diupload!');closeModal()" class="btn-brand flex-1 !py-2.5">Upload</button></div>`;
    }
    box.innerHTML = html;
    document.getElementById('modalOverlay').classList.remove('hidden');
    box.classList.remove('hidden');
}
function closeModal() {
    document.getElementById('modalOverlay').classList.add('hidden');
    document.getElementById('modalBox').classList.add('hidden');
}
</script>
@endpush
@endsection