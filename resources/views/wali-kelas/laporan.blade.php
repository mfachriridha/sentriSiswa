@extends('layouts.app')

@section('title', 'Laporan - Wali Kelas')

@section('page-title', 'Laporan Kehadiran & Poin')

@section('content')
@if(session('error'))
<div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs font-semibold text-amber-700 flex items-center gap-2 anim-up">
    <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
    <button onclick="this.parentElement.remove()" class="ml-auto text-amber-400 hover:text-amber-600"><i class="bi bi-x"></i></button>
</div>
@endif

<div class="flex items-center gap-3 mb-6 anim-up">
    <div class="w-10 h-10 bg-[#001e40] rounded-xl flex items-center justify-center"><i class="bi bi-diagram-3 text-white"></i></div>
    <div>
        <h2 class="font-bold text-slate-900">Kelas {{ $teacherClass->schoolClass->name }}</h2>
        <p class="text-xs text-muted">{{ $rows->count() }} siswa</p>
    </div>
</div>

<!-- Filter -->
<form class="flex flex-wrap items-end gap-3 mb-6 card !p-4 anim-up">
    <div>
        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1 block">Periode Cepat</label>
        <div class="flex gap-1">
            @foreach($periods as $label => $range)
            <button type="button" onclick="setPeriod('{{ $range[0]->toDateString() }}', '{{ $range[1]->toDateString() }}')" class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-[#c3c6d1] hover:bg-[#edeeef] transition">{{ $label }}</button>
            @endforeach
        </div>
    </div>
    <div>
        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1 block">Dari</label>
        <input type="date" name="start" value="{{ $start->toDateString() }}" class="input-field !py-1.5 !text-xs" form="laporanForm">
    </div>
    <div>
        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1 block">Sampai</label>
        <input type="date" name="end" value="{{ $end->toDateString() }}" class="input-field !py-1.5 !text-xs" form="laporanForm">
    </div>
    <button type="submit" form="laporanForm" class="btn-brand !py-1.5 !text-xs"><i class="bi bi-search"></i> Tampilkan</button>
    @if(request('start') || request('end'))
    <a href="{{ route('wali-kelas.laporan') }}" class="btn-outline !py-1.5 !text-xs"><i class="bi bi-x-circle"></i> Reset</a>
    @endif
</form>

<form id="laporanForm" method="GET" action="{{ route('wali-kelas.laporan') }}" class="hidden"></form>

<!-- Summary Table -->
<div class="card anim-up">
    <h3 class="font-bold text-slate-900 mb-4">{{ $start->translatedFormat('d M Y') }} – {{ $end->translatedFormat('d M Y') }}</h3>
    <div class="table-container">
        <table class="w-full">
            <thead>
                <tr>
                    <th class="table-header">NIS</th>
                    <th class="table-header">Nama</th>
                    <th class="table-header">Hadir</th>
                    <th class="table-header">Izin</th>
                    <th class="table-header">Sakit</th>
                    <th class="table-header">Alfa</th>
                    <th class="table-header">Total</th>
                    <th class="table-header">Poin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $r)
                <tr>
                    <td class="table-cell text-xs font-mono">{{ $r['nis'] }}</td>
                    <td class="table-cell font-semibold text-xs">{{ $r['name'] }}</td>
                    <td class="table-cell"><span class="badge badge-green">{{ $r['hadir'] }}</span></td>
                    <td class="table-cell"><span class="badge badge-amber">{{ $r['izin'] }}</span></td>
                    <td class="table-cell"><span class="badge badge-blue">{{ $r['sakit'] }}</span></td>
                    <td class="table-cell"><span class="badge badge-red">{{ $r['alfa'] }}</span></td>
                    <td class="table-cell font-semibold text-xs">{{ $r['total'] }}</td>
                    <td class="table-cell text-xs">
                        <span class="{{ $r['poin'] >= 80 ? 'text-emerald-600' : ($r['poin'] >= 50 ? 'text-amber-600' : 'text-red-600') }} font-bold">{{ $r['poin'] }}</span>
                        @if($r['poin_dipotong'] > 0)
                        <span class="text-red-500 text-[10px]">-{{ $r['poin_dipotong'] }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td class="table-cell text-center text-muted py-8" colspan="8">Tidak ada data untuk periode ini.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
function setPeriod(start, end) {
    const f = document.getElementById('laporanForm');
    f.querySelector('[name=start]').value = start;
    f.querySelector('[name=end]').value = end;
    f.submit();
}
</script>
@endsection
