@extends('layouts.app')

@section('title', 'Poin Pelanggaran - SentriSiswa')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Poin Pelanggaran</h1>
        <p class="text-sm text-slate-500 mt-0.5">Setiap siswa memiliki saldo awal 100 poin</p>
    </div>
    <button onclick="alert('UI Only: Tambah pelanggaran!')" class="btn-danger !text-xs"><i class="bi bi-plus-circle"></i> Tambah Pelanggaran</button>
</div>

<!-- Stats Poin -->
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="card !bg-red-50 !border-red-200 anim-up"><p class="text-[11px] font-bold text-red-500 uppercase">Rendah &lt;50</p><p class="stat-value text-red-600 mt-1">5</p></div>
    <div class="card !bg-amber-50 !border-amber-200 anim-up"><p class="text-[11px] font-bold text-amber-500 uppercase">Sedang 50-80</p><p class="stat-value text-amber-600 mt-1">12</p></div>
    <div class="card !bg-emerald-50 !border-emerald-200 anim-up"><p class="text-[11px] font-bold text-emerald-500 uppercase">Baik &gt;80</p><p class="stat-value text-emerald-600 mt-1">343</p></div>
</div>

<!-- Form Tambah -->
<div class="card mb-6 anim-up" style="animation-delay:.1s">
    <h3 class="font-bold text-slate-900 mb-4">Tambah Pelanggaran</h3>
    <div class="grid md:grid-cols-3 gap-4">
        <div>
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Siswa</label>
            <select class="input-field"><option>Pilih Siswa</option><option>Ahmad Fauzi (S001)</option><option>Nurul Hidayah (S002)</option></select>
        </div>
        <div>
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Jenis Pelanggaran</label>
            <select class="input-field"><option>Pilih</option><option>Bolos (-10)</option><option>Terlambat (-5)</option><option>Tidak Seragam (-5)</option><option>Merokok (-20)</option></select>
        </div>
        <div>
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Keterangan</label>
            <input type="text" class="input-field" placeholder="Detail pelanggaran">
        </div>
    </div>
    <div class="mt-4 flex items-start gap-2 p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-700">
        <i class="bi bi-info-circle-fill shrink-0 mt-0.5"></i>
        <span>Poin siswa akan otomatis berkurang. Notifikasi dikirim ke orang tua via WhatsApp setiap minggu.</span>
    </div>
    <button onclick="alert('UI Only: Pelanggaran ditambahkan!')" class="btn-brand mt-4 !text-xs"><i class="bi bi-check-circle"></i> Simpan</button>
</div>

<!-- Riwayat -->
<div class="card anim-up" style="animation-delay:.15s">
    <h3 class="font-bold text-slate-900 mb-4">Riwayat Pelanggaran</h3>
    <div class="table-container">
        <table class="w-full">
            <thead><tr><th class="table-header">Tanggal</th><th class="table-header">Siswa</th><th class="table-header">Pelanggaran</th><th class="table-header">Poin</th><th class="table-header">Sisa</th><th class="table-header">Oleh</th></tr></thead>
            <tbody>
                <tr><td class="table-cell">5 Mei 2026</td><td class="table-cell font-semibold">Ahmad Fauzi</td><td class="table-cell">Bolos</td><td class="table-cell"><span class="badge badge-red">-10</span></td><td class="table-cell font-semibold">95</td><td class="table-cell">Siti Nurhaliza</td></tr>
                <tr><td class="table-cell">4 Mei 2026</td><td class="table-cell font-semibold">Nurul Hidayah</td><td class="table-cell">Terlambat</td><td class="table-cell"><span class="badge badge-amber">-5</span></td><td class="table-cell font-semibold">95</td><td class="table-cell">Siti Nurhaliza</td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection