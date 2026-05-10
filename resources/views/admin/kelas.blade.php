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
            <thead><tr><th class="table-header">Nama Kelas</th><th class="table-header">Tingkat</th><th class="table-header">Wali Kelas</th><th class="table-header">Jumlah Siswa</th><th class="table-header text-center">Aksi</th></tr></thead>
            <tbody>
                <tr><td class="table-cell font-semibold">XII IPA 1</td><td class="table-cell">XII</td><td class="table-cell">Budi Santoso, S.Pd</td><td class="table-cell">30</td><td class="table-cell-aksi"><div class="flex items-center justify-center gap-1"><button onclick="alert('Lihat detail kelas')" class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold border border-[#c3c6d1] text-[#43474f] hover:bg-[#edeeef] transition cursor-pointer"><i class="bi bi-eye"></i> Lihat</button><button onclick="alert('Edit kelas')" class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold bg-blue-600 text-white hover:bg-blue-700 transition cursor-pointer"><i class="bi bi-pencil"></i> Ubah</button><button onclick="alert('Hapus kelas?')" class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold bg-red-600 text-white hover:bg-red-700 transition cursor-pointer"><i class="bi bi-trash"></i> Hapus</button></div></td></tr>
                <tr><td class="table-cell font-semibold">XII IPA 2</td><td class="table-cell">XII</td><td class="table-cell">Siti Nurhaliza, M.Pd</td><td class="table-cell">32</td><td class="table-cell-aksi"><div class="flex items-center justify-center gap-1"><button onclick="alert('Lihat detail kelas')" class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold border border-[#c3c6d1] text-[#43474f] hover:bg-[#edeeef] transition cursor-pointer"><i class="bi bi-eye"></i> Lihat</button><button onclick="alert('Edit kelas')" class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold bg-blue-600 text-white hover:bg-blue-700 transition cursor-pointer"><i class="bi bi-pencil"></i> Ubah</button><button onclick="alert('Hapus kelas?')" class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold bg-red-600 text-white hover:bg-red-700 transition cursor-pointer"><i class="bi bi-trash"></i> Hapus</button></div></td></tr>
                <tr><td class="table-cell font-semibold">XI IPA 1</td><td class="table-cell">XI</td><td class="table-cell">Ahmad Fauzi, S.Pd</td><td class="table-cell">30</td><td class="table-cell-aksi"><div class="flex items-center justify-center gap-1"><button onclick="alert('Lihat detail kelas')" class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold border border-[#c3c6d1] text-[#43474f] hover:bg-[#edeeef] transition cursor-pointer"><i class="bi bi-eye"></i> Lihat</button><button onclick="alert('Edit kelas')" class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold bg-blue-600 text-white hover:bg-blue-700 transition cursor-pointer"><i class="bi bi-pencil"></i> Ubah</button><button onclick="alert('Hapus kelas?')" class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold bg-red-600 text-white hover:bg-red-700 transition cursor-pointer"><i class="bi bi-trash"></i> Hapus</button></div></td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection