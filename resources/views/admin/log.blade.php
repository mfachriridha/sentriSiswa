@extends('layouts.app')

@section('title', 'Log Aktivitas - Admin')

@section('page-title', 'Log Aktivitas')

@section('content')
<div class="flex justify-end mb-6">
    <select class="input-field !w-auto !py-1.5 !text-xs"><option>Semua Aksi</option><option>Tambah</option><option>Edit</option><option>Hapus</option></select>
</div>

<div class="card anim-up">
    <h3 class="font-bold text-slate-900 mb-4">Riwayat Aktivitas</h3>
    <div class="space-y-2">
        <div class="flex items-start gap-3 p-3 hover:bg-slate-50 rounded-xl">
            <div class="w-8 h-8 bg-emerald-100 rounded-full flex items-center justify-center shrink-0"><i class="bi bi-plus-lg text-emerald-600 text-sm"></i></div>
            <div class="flex-1"><p class="text-sm font-semibold">Tambah siswa baru</p><p class="text-xs text-slate-500">Ahmad Fauzi (S001) &mdash; Kelas XII IPA 1</p></div>
            <span class="text-[10px] text-slate-400 shrink-0">10 Mei, 08:15</span>
        </div>
        <div class="flex items-start gap-3 p-3 hover:bg-slate-50 rounded-xl">
            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center shrink-0"><i class="bi bi-pencil text-blue-600 text-sm"></i></div>
            <div class="flex-1"><p class="text-sm font-semibold">Edit guru</p><p class="text-xs text-slate-500">NIP 1234567890 &mdash; update nomor WA</p></div>
            <span class="text-[10px] text-slate-400 shrink-0">9 Mei, 14:30</span>
        </div>
        <div class="flex items-start gap-3 p-3 hover:bg-slate-50 rounded-xl">
            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center shrink-0"><i class="bi bi-exclamation-triangle text-red-500 text-sm"></i></div>
            <div class="flex-1"><p class="text-sm font-semibold">Poin pelanggaran</p><p class="text-xs text-slate-500">Ahmad Fauzi &mdash; Bolos (-10 poin)</p></div>
            <span class="text-[10px] text-slate-400 shrink-0">5 Mei, 08:15</span>
        </div>
        <div class="flex items-start gap-3 p-3 hover:bg-slate-50 rounded-xl">
            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center shrink-0"><i class="bi bi-file-earmark-pdf text-purple-600 text-sm"></i></div>
            <div class="flex-1"><p class="text-sm font-semibold">Upload tata tertib</p><p class="text-xs text-slate-500">File: TATA_TERTIB_2025_2026.pdf</p></div>
            <span class="text-[10px] text-slate-400 shrink-0">1 Mei, 09:00</span>
        </div>
    </div>
</div>
@endsection