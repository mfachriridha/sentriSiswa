@extends('layouts.app')

@section('title', 'Poin Pelanggaran - Admin')

@section('page-title', 'Poin Pelanggaran')

@section('content')
@if(session('success'))
<div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-xs font-semibold text-emerald-700 flex items-center gap-2 anim-up">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    <button onclick="this.parentElement.remove()" class="ml-auto text-emerald-400 hover:text-emerald-600"><i class="bi bi-x"></i></button>
</div>
@endif
@if(session('error'))
<div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-xs font-semibold text-red-700 flex items-center gap-2 anim-up">
    <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
    <button onclick="this.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-600"><i class="bi bi-x"></i></button>
</div>
@endif

<!-- Tambah Pelanggaran ke Siswa -->
<div class="card mb-6 anim-up">
    <h3 class="font-bold text-slate-900 mb-4">Catat Pelanggaran Siswa</h3>
    <form action="{{ route('admin.poin.store') }}" method="POST" class="space-y-3">
        @csrf
        <div class="grid md:grid-cols-3 gap-4">
            <div><label class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Siswa</label><select name="student_id" class="input-field" required><option value="">Pilih Siswa</option>@foreach($students as $s)<option value="{{ $s->id }}">{{ $s->name }} ({{ $s->nis }}) — {{ $s->poin }} poin</option>@endforeach</select></div>
            <div><label class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Jenis Pelanggaran</label><select name="violation_id" class="input-field" required><option value="">Pilih</option>@foreach($violations as $v)<option value="{{ $v->id }}">{{ $v->name }} ({{ $v->poin }} poin)</option>@endforeach</select></div>
            <div><label class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Keterangan</label><input type="text" name="note" class="input-field"></div>
        </div>
        <button type="submit" class="btn-danger !text-sm"><i class="bi bi-plus-circle"></i> Simpan Pelanggaran</button>
    </form>
</div>

<!-- Jenis Pelanggaran -->
<div class="grid lg:grid-cols-2 gap-6">
    <div class="card anim-up">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-slate-900">Jenis Pelanggaran</h3>
            <button onclick="showViolationModal()" class="btn-brand !text-xs"><i class="bi bi-plus-lg"></i> Tambah</button>
        </div>
        <div class="table-container">
            <table class="w-full">
                <thead><tr><th class="table-header">Nama</th><th class="table-header">Poin</th><th class="table-header">Kategori</th><th class="table-header text-center">Aksi</th></tr></thead>
                <tbody>
                    @forelse($violations as $v)
                    <tr>
                        <td class="table-cell text-xs font-semibold">{{ $v->name }}</td>
                        <td class="table-cell"><span class="badge badge-red">{{ $v->poin }}</span></td>
                        <td class="table-cell text-xs uppercase">
                            @if($v->category === 'ringan')<span class="badge badge-green">Ringan</span>
                            @elseif($v->category === 'sedang')<span class="badge badge-amber">Sedang</span>
                            @elseif($v->category === 'berat')<span class="badge badge-red">Berat</span>
                            @else<span class="badge" style="background:#1a1a1a;color:#fff">Amat Berat</span>@endif
                        </td>
                        <td class="table-cell-aksi">
                            <button onclick='editViolation({{ $v->id }}, "{{ $v->name }}", {{ $v->poin }}, "{{ $v->category }}")' class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold bg-blue-600 text-white hover:bg-blue-700 transition cursor-pointer"><i class="bi bi-pencil"></i> Ubah</button>
                            <button onclick="confirmAction('Hapus {{ $v->name }}?', () => { const f=document.createElement('form'); f.method='POST'; f.action='{{ route('admin.poin.violation.destroy', $v) }}'; f.innerHTML='@csrf @method('DELETE')'; document.body.appendChild(f); f.submit(); })" class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold bg-red-600 text-white hover:bg-red-700 transition cursor-pointer"><i class="bi bi-trash"></i> Hapus</button>
                        </td>
                    </tr>
                    @empty
                    <tr><td class="table-cell text-center text-muted" colspan="4">Belum ada jenis pelanggaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Riwayat -->
    <div class="card anim-up">
        <h3 class="font-bold text-slate-900 mb-4">Riwayat Pelanggaran</h3>
        <div class="table-container">
            <table class="w-full">
                <thead><tr><th class="table-header">Tanggal</th><th class="table-header">Siswa</th><th class="table-header">Pelanggaran</th><th class="table-header">Poin</th></tr></thead>
                <tbody>
                    @forelse($riwayat as $r)
                    <tr><td class="table-cell text-xs">{{ $r->created_at->format('d/m H:i') }}</td><td class="table-cell text-xs font-semibold">{{ $r->student->name }}</td><td class="table-cell text-xs">{{ $r->violation->name }}</td><td class="table-cell"><span class="badge badge-red">{{ $r->violation->poin }}</span></td></tr>
                    @empty
                    <tr><td class="table-cell text-center text-muted" colspan="4">Belum ada riwayat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Violation Type Modal -->
<div id="vModalOverlay" class="hidden fixed inset-0 z-50 bg-black/40" onclick="closeViolationModal()"></div>
<div id="vModalBox" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-50 bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 anim-up">
    <div class="flex justify-between items-center mb-4">
        <h3 id="vModalTitle" class="font-bold text-lg">Tambah Jenis Pelanggaran</h3>
        <button onclick="closeViolationModal()" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg text-xl"></i></button>
    </div>
    <form id="vForm" method="POST" action="{{ route('admin.poin.violation.store') }}" class="space-y-3">
        @csrf
        <input type="hidden" name="_method" id="vMethod" value="POST">
        <div>
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Nama Pelanggaran</label>
            <input type="text" name="name" id="vName" required class="input-field" placeholder="Contoh: Terlambat 1-10 menit">
        </div>
        <div>
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Poin</label>
            <input type="number" name="poin" id="vPoin" required class="input-field" min="1" placeholder="5">
        </div>
        <div>
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Kategori</label>
            <select name="category" id="vCategory" required class="input-field">
                <option value="ringan">Ringan</option>
                <option value="sedang">Sedang</option>
                <option value="berat">Berat</option>
                <option value="amat_berat">Amat Berat</option>
            </select>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="button" onclick="closeViolationModal()" class="btn-outline flex-1 !py-2.5">Batal</button>
            <button type="submit" class="btn-brand flex-1 !py-2.5" id="vSubmit">
                <span class="btn-text"><i class="bi bi-check-circle"></i> Simpan</span>
                <span class="btn-loading hidden"><i class="bi bi-hourglass-split animate-spin"></i> Menyimpan...</span>
            </button>
        </div>
    </form>
</div>

@include('components.confirm-modal')

@push('scripts')
<script>
function showViolationModal(edit = null) {
    document.getElementById('vModalOverlay').classList.remove('hidden');
    document.getElementById('vModalBox').classList.remove('hidden');
    document.getElementById('vName').value = edit ? edit.name : '';
    document.getElementById('vPoin').value = edit ? edit.poin : '';
    document.getElementById('vCategory').value = edit ? edit.category : 'ringan';
    document.getElementById('vModalTitle').textContent = edit ? 'Edit Jenis Pelanggaran' : 'Tambah Jenis Pelanggaran';
    document.getElementById('vMethod').value = edit ? 'PUT' : 'POST';
    document.getElementById('vForm').action = edit
        ? '/admin/poin/violation/' + edit.id
        : '{{ route("admin.poin.violation.store") }}';
}

function closeViolationModal() {
    document.getElementById('vModalOverlay').classList.add('hidden');
    document.getElementById('vModalBox').classList.add('hidden');
}

function editViolation(id, name, poin, category) {
    showViolationModal({ id, name, poin, category });
}

function setLoading(btn, loading) {
    const t = btn.querySelector('.btn-text'), l = btn.querySelector('.btn-loading');
    if (t) t.classList.toggle('hidden', loading);
    if (l) l.classList.toggle('hidden', !loading);
    btn.disabled = loading;
}

document.getElementById('vForm').addEventListener('submit', function() {
    setLoading(document.getElementById('vSubmit'), true);
});
</script>
@endpush
@endsection
