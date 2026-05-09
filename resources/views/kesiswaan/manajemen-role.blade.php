@extends('layouts.app')

@section('title', 'Manajemen Role - SentriSiswa')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Manajemen Role</h1>
        <p class="text-sm text-slate-500 mt-0.5">Assign role guru ke kelas &amp; jabatan</p>
    </div>
</div>

<div class="card mb-6 anim-up">
    <h3 class="font-bold text-slate-900 mb-4">Assign Role Guru</h3>
    <div class="grid md:grid-cols-3 gap-4 mb-4">
        <div>
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Pilih Guru</label>
            <select class="input-field"><option>Pilih Guru</option><option>Budi Santoso, S.Pd</option><option>Siti Nurhaliza, M.Pd</option></select>
        </div>
        <div>
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Role</label>
            <select class="input-field" id="assignRole" onchange="toggleKelas()">
                <option value="">Pilih Role</option>
                <option value="walikelas">Wali Kelas</option>
                <option value="guru-mapel">Guru Mapel</option>
                <option value="bk">Guru BK</option>
            </select>
        </div>
        <div id="kelasField">
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Kelas (jika Wali Kelas)</label>
            <select class="input-field"><option>Pilih Kelas</option><option>XII IPA 1</option><option>XII IPA 2</option></select>
        </div>
    </div>
    <button onclick="alert('UI Only: Role berhasil di-assign!')" class="btn-brand !text-xs">
        <i class="bi bi-check-circle"></i> Simpan Assignment
    </button>
</div>

<div class="card anim-up" style="animation-delay:.1s">
    <h3 class="font-bold text-slate-900 mb-4">Daftar Assignment</h3>
    <div class="table-container">
        <table class="w-full">
            <thead><tr><th class="table-header">Nama Guru</th><th class="table-header">NIP</th><th class="table-header">Role</th><th class="table-header">Kelas</th><th class="table-header">Aksi</th></tr></thead>
            <tbody>
                <tr><td class="table-cell font-semibold">Budi Santoso, S.Pd</td><td class="table-cell">1234567890</td><td class="table-cell"><span class="badge badge-blue">Wali Kelas</span></td><td class="table-cell">XII IPA 1</td><td class="table-cell"><button class="text-blue-600 hover:text-blue-800 mr-2"><i class="bi bi-pencil"></i></button><button class="text-red-500 hover:text-red-700"><i class="bi bi-trash"></i></button></td></tr>
                <tr><td class="table-cell font-semibold">Siti Nurhaliza, M.Pd</td><td class="table-cell">0987654321</td><td class="table-cell"><span class="badge badge-red">BK</span> <span class="badge badge-green ml-1">Mapel</span></td><td class="table-cell">-</td><td class="table-cell"><button class="text-blue-600 hover:text-blue-800 mr-2"><i class="bi bi-pencil"></i></button><button class="text-red-500 hover:text-red-700"><i class="bi bi-trash"></i></button></td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleKelas() {
    const r = document.getElementById('assignRole').value;
    document.getElementById('kelasField').style.display = r === 'walikelas' ? 'block' : 'none';
}
</script>
@endsection