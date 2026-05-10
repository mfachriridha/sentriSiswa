@extends('layouts.app')

@section('title', 'Poin Pelanggaran - Admin')

@section('page-title', 'Poin Pelanggaran')

@section('content')
@if(session('success'))
<div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-xs font-semibold text-emerald-700 flex items-center gap-2">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    <button onclick="this.parentElement.remove()" class="ml-auto text-emerald-400 hover:text-emerald-600"><i class="bi bi-x"></i></button>
</div>
@endif

<div class="card mb-6 anim-up">
    <h3 class="font-bold text-slate-900 mb-4">Tambah Pelanggaran</h3>
    <form action="{{ route('admin.poin.store') }}" method="POST" class="space-y-3">
        @csrf
        <div class="grid md:grid-cols-3 gap-4">
            <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Siswa</label><select name="student_id" class="input-field" required><option value="">Pilih Siswa</option>@foreach($students as $s)<option value="{{ $s->id }}">{{ $s->name }} ({{ $s->nis }}) — {{ $s->poin }} poin</option>@endforeach</select></div>
            <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Jenis Pelanggaran</label><select name="violation_id" class="input-field" required><option value="">Pilih</option>@foreach($violations as $v)<option value="{{ $v->id }}">{{ $v->name }} ({{ $v->poin }} poin)</option>@endforeach</select></div>
            <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Keterangan</label><input type="text" name="note" class="input-field"></div>
        </div>
        <button type="submit" class="btn-danger !text-xs"><i class="bi bi-plus-circle"></i> Simpan Pelanggaran</button>
    </form>
</div>

<div class="card anim-up" style="animation-delay:.1s">
    <h3 class="font-bold text-slate-900 mb-4">Riwayat Pelanggaran</h3>
    <div class="table-container">
        <table class="w-full">
            <thead><tr><th class="table-header">Tanggal</th><th class="table-header">Siswa</th><th class="table-header">Pelanggaran</th><th class="table-header">Poin</th><th class="table-header">Kategori</th><th class="table-header">Ket</th></tr></thead>
            <tbody>
                @forelse($riwayat as $r)
                <tr><td class="table-cell">{{ $r->created_at->format('d/m/Y H:i') }}</td><td class="table-cell font-semibold">{{ $r->student->name }}</td><td class="table-cell">{{ $r->violation->name }}</td><td class="table-cell"><span class="badge badge-red">{{ $r->violation->poin }}</span></td><td class="table-cell text-xs uppercase">{{ str_replace('_',' ',$r->violation->category) }}</td><td class="table-cell text-xs text-muted">{{ $r->note ?: '—' }}</td></tr>
                @empty
                <tr><td class="table-cell text-center text-muted" colspan="6">Belum ada riwayat pelanggaran.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection