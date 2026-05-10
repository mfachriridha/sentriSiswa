@extends('layouts.app')

@section('title', 'Manajemen Guru - Admin')

@section('page-title', 'Manajemen Guru')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div class="flex gap-2">
        <a href="{{ route('admin.guru.tambah') }}" class="btn-brand !text-xs"><i class="bi bi-plus-lg"></i> Tambah Guru</a>
        <button onclick="showModal()" class="btn-outline !text-xs"><i class="bi bi-upload"></i> Upload CSV</button>
        <a href="#" onclick="alert('UI Only: Download template CSV')" class="btn-outline !text-xs"><i class="bi bi-download"></i> Template CSV</a>
    </div>
</div>

<div class="card anim-up">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <h3 class="font-bold text-slate-900">Data Guru <span class="text-sm font-medium text-muted">(42)</span></h3>
        <input type="text" class="input-field !w-48 !py-1.5 !text-xs" placeholder="Cari nama / NIP...">
    </div>
    <div class="table-container">
        <table class="w-full">
            <thead>
                <tr><th class="table-header">NIP</th><th class="table-header">Nama</th><th class="table-header">JK</th><th class="table-header">Mapel</th><th class="table-header">Wali Kelas</th><th class="table-header">WA</th><th class="table-header">Aksi</th></tr>
            </thead>
            <tbody>
                <tr><td class="table-cell">1234567890</td><td class="table-cell font-semibold"><a href="{{ route('admin.guru.edit') }}" class="text-[#0062a0] hover:underline">Budi Santoso, S.Pd</a></td><td class="table-cell">L</td><td class="table-cell">Matematika</td><td class="table-cell">XII IPA 1</td><td class="table-cell">081234567890</td><td class="table-cell"><a href="{{ route('admin.guru.edit') }}" class="text-blue-600 hover:text-blue-800 mr-2"><i class="bi bi-pencil"></i></a><button onclick="alert('Hapus?')" class="text-red-500 hover:text-red-700"><i class="bi bi-trash"></i></button></td></tr>
                <tr><td class="table-cell">0987654321</td><td class="table-cell font-semibold"><a href="{{ route('admin.guru.edit') }}" class="text-[#0062a0] hover:underline">Siti Nurhaliza, M.Pd</a></td><td class="table-cell">P</td><td class="table-cell">Biologi, BK</td><td class="table-cell">—</td><td class="table-cell">081298765432</td><td class="table-cell"><a href="{{ route('admin.guru.edit') }}" class="text-blue-600 hover:text-blue-800 mr-2"><i class="bi bi-pencil"></i></a><button onclick="alert('Hapus?')" class="text-red-500 hover:text-red-700"><i class="bi bi-trash"></i></button></td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- CSV Upload Modal -->
<div id="modalOverlay" class="hidden fixed inset-0 z-50 bg-black/40" onclick="closeModal()"></div>
<div id="modalBox" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-50 bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
    <div class="flex justify-between items-center mb-4"><h3 class="font-bold text-lg">Upload CSV Guru</h3><button onclick="closeModal()" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg text-xl"></i></button></div>
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
    <div class="flex gap-3 pt-2"><button type="button" onclick="closeModal()" class="btn-outline flex-1 !py-2.5">Batal</button><button type="button" onclick="alert('UI Only: CSV diupload!');closeModal()" class="btn-brand flex-1 !py-2.5">Upload</button></div>
</div>

@push('scripts')
<script>
function showModal() {
    document.getElementById('modalOverlay').classList.remove('hidden');
    document.getElementById('modalBox').classList.remove('hidden');
}
function closeModal() {
    document.getElementById('modalOverlay').classList.add('hidden');
    document.getElementById('modalBox').classList.add('hidden');
}
</script>
@endpush
@endsection