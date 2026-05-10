@extends('layouts.app')

@section('title', 'Manajemen Guru - Admin')

@section('page-title', 'Manajemen Guru')

@section('content')
@if(session('success'))
<div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-xs font-semibold text-emerald-700 flex items-center gap-2 anim-up">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    <button onclick="this.parentElement.remove()" class="ml-auto text-emerald-400 hover:text-emerald-600"><i class="bi bi-x"></i></button>
</div>
@endif
@if(session('error'))
<div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-xs font-semibold text-red-700 flex items-center gap-2 anim-up">
    <i class="bi bi-x-circle-fill"></i> {{ session('error') }}
    <button onclick="this.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-600"><i class="bi bi-x"></i></button>
</div>
@endif
@if($errors->any())
<div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-xs font-semibold text-red-700 anim-up">
    @foreach($errors->all() as $error)
        <p><i class="bi bi-x-circle-fill mr-1"></i> {{ $error }}</p>
    @endforeach
    <button onclick="this.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-600"><i class="bi bi-x"></i></button>
</div>
@endif

<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div class="flex gap-2">
        <button onclick="showModal()" class="btn-brand !text-xs"><i class="bi bi-plus-lg"></i> Tambah Guru</button>
    </div>
</div>

<div class="card anim-up">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <h3 class="font-bold text-slate-900">Data Guru <span class="text-sm font-medium text-muted">({{ $teachers->count() }})</span></h3>
    </div>
    <div class="table-container">
        <table class="w-full">
            <thead>
                <tr>
                    <th class="table-header">NIP</th>
                    <th class="table-header">Nama</th>
                    <th class="table-header">Role</th>
                    <th class="table-header">Kelas</th>
                    <th class="table-header">Status</th>
                    <th class="table-header text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($teachers as $teacher)
                <tr>
                    <td class="table-cell text-xs">{{ $teacher->nip ?? '—' }}</td>
                    <td class="table-cell font-semibold">{{ $teacher->name }}</td>
                    <td class="table-cell">
                        @if($teacher->role === 'wali_kelas')
                            <span class="badge badge-blue">Wali Kelas</span>
                        @else
                            <span class="badge badge-amber">BK</span>
                        @endif
                    </td>
                    <td class="table-cell text-xs">
                        @foreach($teacher->teacherClasses as $tc)
                            {{ $tc->schoolClass->name }}@if(!$loop->last), @endif
                        @endforeach
                        @if($teacher->teacherClasses->isEmpty())
                            —
                        @endif
                    </td>
                    <td class="table-cell">
                        @if($teacher->is_active)
                            <span class="badge badge-green">Aktif</span>
                        @else
                            <span class="badge badge-red">Belum Registrasi</span>
                        @endif
                    </td>
                    <td class="table-cell-aksi">
                        <div class="flex items-center justify-center gap-1">
                            <button onclick="editGuru({{ $teacher->id }})" class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold bg-blue-600 text-white hover:bg-blue-700 transition cursor-pointer">
                                <i class="bi bi-pencil"></i> Ubah
                            </button>
                            <form action="{{ route('admin.guru.destroy', $teacher) }}" method="POST" class="inline" onsubmit="return confirm('Hapus guru {{ $teacher->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold bg-red-600 text-white hover:bg-red-700 transition cursor-pointer">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="table-cell text-center text-muted py-8">Belum ada data guru.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="modalOverlay" class="hidden fixed inset-0 z-50 bg-black/40" onclick="closeModal()"></div>
<div id="modalBox" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-50 bg-white rounded-2xl shadow-xl w-full max-w-md p-6 anim-up">
    <div class="flex justify-between items-center mb-4">
        <h3 id="modalTitle" class="font-bold text-lg">Tambah Guru</h3>
        <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg text-xl"></i></button>
    </div>

    <form id="guruForm" method="POST" action="{{ route('admin.guru.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="_method" id="formMethod" value="POST">

        <div>
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">Nama Lengkap</label>
            <input type="text" name="name" id="guruName" required class="input-field" placeholder="Nama lengkap dengan gelar">
        </div>
        <div>
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">NIP</label>
            <input type="text" name="nip" id="guruNip" class="input-field" placeholder="19920912 202221 2 018" maxlength="22" inputmode="numeric">
            <p class="text-[10px] text-muted mt-1">Kosongkan jika tidak ada NIP</p>
        </div>
        <div>
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">Role</label>
            <select name="role" id="guruRole" required class="input-field">
                <option value="wali_kelas">Wali Kelas</option>
                <option value="bk">BK</option>
            </select>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="button" onclick="closeModal()" class="btn-outline flex-1 !py-2.5">Batal</button>
            <button type="submit" class="btn-brand flex-1 !py-2.5">
                <span class="btn-text"><i class="bi bi-check-circle"></i> Simpan</span>
                <span class="btn-loading hidden"><i class="bi bi-hourglass-split animate-spin"></i> Menyimpan...</span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function showModal(user = null) {
    document.getElementById('modalOverlay').classList.remove('hidden');
    document.getElementById('modalBox').classList.remove('hidden');
    const form = document.getElementById('guruForm');
    document.getElementById('guruName').value = user ? user.name : '';
    document.getElementById('guruNip').value = user ? (user.nip || '') : '';
    document.getElementById('guruRole').value = user ? user.role : 'wali_kelas';
    document.getElementById('modalTitle').textContent = user ? 'Edit Guru' : 'Tambah Guru';
    document.getElementById('formMethod').value = user ? 'PUT' : 'POST';
    form.action = user ? '/admin/guru/' + user.id : '{{ route("admin.guru.store") }}';
}

function closeModal() {
    document.getElementById('modalOverlay').classList.add('hidden');
    document.getElementById('modalBox').classList.add('hidden');
}

function setLoading(btn, loading) {
    const t = btn.querySelector('.btn-text'), l = btn.querySelector('.btn-loading');
    if (t) t.classList.toggle('hidden', loading);
    if (l) l.classList.toggle('hidden', !loading);
    btn.disabled = loading;
}

document.getElementById('guruForm').addEventListener('submit', function() {
    setLoading(this.querySelector('[type="submit"]'), true);
});

function editGuru(id) {
    fetch('/admin/guru/' + id + '/edit?_ajax=1', { headers: {'Accept': 'application/json'} })
        .then(r => r.json())
        .then(user => showModal(user))
        .catch(() => alert('Gagal memuat data guru.'));
}
</script>
@endpush
@endsection
