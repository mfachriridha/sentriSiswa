@extends('layouts.app')

@section('title', 'Master Data - SentriSiswa')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Master Data</h1>
        <p class="text-sm text-slate-500 mt-0.5">Kelola data guru, siswa, dan kelas</p>
    </div>
    <div class="flex gap-2">
        <button onclick="showModal('uploadCSVModal')" class="btn-outline !text-xs"><i class="bi bi-upload"></i> Upload CSV</button>
    </div>
</div>

<!-- Tabs -->
<div class="flex gap-1 p-1 bg-slate-100 rounded-xl mb-6 w-fit">
    <button onclick="switchTab('guru')" id="tab-guru" class="px-4 py-2 rounded-lg text-sm font-semibold bg-white shadow-sm text-slate-900 transition">Guru</button>
    <button onclick="switchTab('siswa')" id="tab-siswa" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-500 hover:text-slate-700 transition">Siswa</button>
    <button onclick="switchTab('kelas')" id="tab-kelas" class="px-4 py-2 rounded-lg text-sm font-semibold text-slate-500 hover:text-slate-700 transition">Kelas</button>
</div>

<!-- Guru Tab -->
<div id="panel-guru" class="card anim-up">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <h3 class="font-bold text-slate-900">Data Guru</h3>
        <button onclick="alert('UI Only: Tambah guru!')" class="btn-brand !text-xs"><i class="bi bi-plus-lg"></i> Tambah Guru</button>
    </div>
    <div class="table-container">
        <table class="w-full">
            <thead><tr><th class="table-header">NIP</th><th class="table-header">Nama</th><th class="table-header">Role</th><th class="table-header">Kelas</th><th class="table-header">WA</th><th class="table-header">Aksi</th></tr></thead>
            <tbody>
                <tr><td class="table-cell">1234567890</td><td class="table-cell font-semibold">Budi Santoso, S.Pd</td><td class="table-cell"><span class="badge badge-blue">Wali Kelas</span></td><td class="table-cell">XII IPA 1</td><td class="table-cell">081234567890</td><td class="table-cell"><button class="text-blue-600 hover:text-blue-800 mr-2"><i class="bi bi-pencil"></i></button><button class="text-red-500 hover:text-red-700"><i class="bi bi-trash"></i></button></td></tr>
                <tr><td class="table-cell">0987654321</td><td class="table-cell font-semibold">Siti Nurhaliza, M.Pd</td><td class="table-cell"><span class="badge badge-red">BK</span> <span class="badge badge-green ml-1">Mapel</span></td><td class="table-cell">-</td><td class="table-cell">081298765432</td><td class="table-cell"><button class="text-blue-600 hover:text-blue-800 mr-2"><i class="bi bi-pencil"></i></button><button class="text-red-500 hover:text-red-700"><i class="bi bi-trash"></i></button></td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Siswa Tab -->
<div id="panel-siswa" class="card hidden anim-up">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <h3 class="font-bold text-slate-900">Data Siswa</h3>
        <button onclick="alert('UI Only: Tambah siswa!')" class="btn-brand !text-xs"><i class="bi bi-plus-lg"></i> Tambah Siswa</button>
    </div>
    <div class="table-container">
        <table class="w-full">
            <thead><tr><th class="table-header">NIS</th><th class="table-header">Nama</th><th class="table-header">Kelas</th><th class="table-header">WA Siswa</th><th class="table-header">WA Ortu</th><th class="table-header">Aksi</th></tr></thead>
            <tbody>
                <tr><td class="table-cell">S001</td><td class="table-cell font-semibold">Ahmad Fauzi</td><td class="table-cell">XII IPA 1</td><td class="table-cell">081312345678</td><td class="table-cell">081398765432</td><td class="table-cell"><button class="text-blue-600 hover:text-blue-800 mr-2"><i class="bi bi-pencil"></i></button><button class="text-red-500 hover:text-red-700"><i class="bi bi-trash"></i></button></td></tr>
                <tr><td class="table-cell">S002</td><td class="table-cell font-semibold">Nurul Hidayah</td><td class="table-cell">XII IPA 1</td><td class="table-cell">081323456789</td><td class="table-cell">081387654321</td><td class="table-cell"><button class="text-blue-600 hover:text-blue-800 mr-2"><i class="bi bi-pencil"></i></button><button class="text-red-500 hover:text-red-700"><i class="bi bi-trash"></i></button></td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Kelas Tab -->
<div id="panel-kelas" class="card hidden anim-up">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <h3 class="font-bold text-slate-900">Data Kelas</h3>
        <button onclick="alert('UI Only: Tambah kelas!')" class="btn-brand !text-xs"><i class="bi bi-plus-lg"></i> Tambah Kelas</button>
    </div>
    <div class="table-container">
        <table class="w-full">
            <thead><tr><th class="table-header">Nama Kelas</th><th class="table-header">Tingkat</th><th class="table-header">Wali Kelas</th><th class="table-header">Jumlah Siswa</th><th class="table-header">Aksi</th></tr></thead>
            <tbody>
                <tr><td class="table-cell font-semibold">XII IPA 1</td><td class="table-cell">XII</td><td class="table-cell">Budi Santoso, S.Pd</td><td class="table-cell">30</td><td class="table-cell"><button class="text-blue-600 hover:text-blue-800 mr-2"><i class="bi bi-pencil"></i></button><button class="text-red-500 hover:text-red-700"><i class="bi bi-trash"></i></button></td></tr>
                <tr><td class="table-cell font-semibold">XII IPA 2</td><td class="table-cell">XII</td><td class="table-cell">Siti Nurhaliza, M.Pd</td><td class="table-cell">32</td><td class="table-cell"><button class="text-blue-600 hover:text-blue-800 mr-2"><i class="bi bi-pencil"></i></button><button class="text-red-500 hover:text-red-700"><i class="bi bi-trash"></i></button></td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
function switchTab(tab) {
    ['guru','siswa','kelas'].forEach(t => {
        document.getElementById('panel-'+t).classList.add('hidden');
        document.getElementById('tab-'+t).classList.remove('bg-white','shadow-sm','text-slate-900');
        document.getElementById('tab-'+t).classList.add('text-slate-500');
    });
    document.getElementById('panel-'+tab).classList.remove('hidden');
    document.getElementById('tab-'+tab).classList.add('bg-white','shadow-sm','text-slate-900');
    document.getElementById('tab-'+tab).classList.remove('text-slate-500');
}
</script>
@endsection