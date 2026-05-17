@extends('layouts.app')

@section('title', 'Pengaturan - Admin')

@section('page-title', 'Pengaturan Sistem')

@section('content')
@if(session('success'))
<div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-xs font-semibold text-emerald-700 flex items-center gap-2 anim-up">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    <button onclick="this.parentElement.remove()" class="ml-auto text-emerald-400 hover:text-emerald-600"><i class="bi bi-x"></i></button>
</div>
@endif

<div class="card anim-up">
    <h3 class="font-bold text-slate-900 mb-4">Jam Operasional</h3>
    <p class="text-xs text-slate-500 mb-5">Atur jam masuk, jam pulang, dan batas toleransi keterlambatan. Digunakan untuk validasi presensi dan auto-alfa.</p>
    <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-4 max-w-lg">
        @csrf @method('PUT')
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Jam Masuk</label>
                <input type="time" name="jam_masuk" value="{{ $jamMasuk }}" class="input-field" required>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Jam Pulang</label>
                <input type="time" name="jam_pulang" value="{{ $jamPulang }}" class="input-field" required>
            </div>
        </div>
        <div>
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Batas Toleransi Keterlambatan (menit)</label>
            <input type="number" name="batas_telat" value="{{ $batasTelat }}" class="input-field !w-32" min="1" max="120" required>
            <p class="text-[10px] text-slate-400 mt-1">Siswa yang presensi lebih dari batas ini akan ditandai terlambat.</p>
        </div>
        <button type="submit" class="btn-brand"><i class="bi bi-check-circle"></i> Simpan</button>
    </form>
</div>
@endsection
