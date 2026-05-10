@extends('layouts.app')

@section('title', 'Manajemen Kelas - Admin')

@section('page-title', 'Manajemen Kelas')

@section('content')
<div class="flex justify-end mb-6">
    <button onclick="alert('UI Only: Tambah kelas!')" class="btn-brand !text-xs"><i class="bi bi-plus-lg"></i> Tambah Kelas</button>
</div>

<div class="card anim-up">
    <h3 class="font-bold text-slate-900 mb-4">Data Kelas</h3>
    <div class="table-container">
        <table class="w-full">
            <thead><tr><th class="table-header">Nama Kelas</th><th class="table-header">Tingkat</th><th class="table-header">Wali Kelas</th><th class="table-header">Jumlah Siswa</th><th class="table-header">Aksi</th></tr></thead>
            <tbody>
                <tr><td class="table-cell font-semibold">XII IPA 1</td><td class="table-cell">XII</td><td class="table-cell">Budi Santoso, S.Pd</td><td class="table-cell">30</td><td class="table-cell"><button onclick="alert('Edit kelas')" class="text-blue-600 hover:text-blue-800 mr-2"><i class="bi bi-pencil"></i></button><button onclick="alert('Hapus?')" class="text-red-500 hover:text-red-700"><i class="bi bi-trash"></i></button></td></tr>
                <tr><td class="table-cell font-semibold">XII IPA 2</td><td class="table-cell">XII</td><td class="table-cell">Siti Nurhaliza, M.Pd</td><td class="table-cell">32</td><td class="table-cell"><button onclick="alert('Edit kelas')" class="text-blue-600 hover:text-blue-800 mr-2"><i class="bi bi-pencil"></i></button><button onclick="alert('Hapus?')" class="text-red-500 hover:text-red-700"><i class="bi bi-trash"></i></button></td></tr>
                <tr><td class="table-cell font-semibold">XI IPA 1</td><td class="table-cell">XI</td><td class="table-cell">Ahmad Fauzi, S.Pd</td><td class="table-cell">30</td><td class="table-cell"><button onclick="alert('Edit kelas')" class="text-blue-600 hover:text-blue-800 mr-2"><i class="bi bi-pencil"></i></button><button onclick="alert('Hapus?')" class="text-red-500 hover:text-red-700"><i class="bi bi-trash"></i></button></td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection