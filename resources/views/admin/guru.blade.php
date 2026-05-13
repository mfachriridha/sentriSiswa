@extends('layouts.app')

@section('title', 'Manajemen Guru - Admin')

@section('page-title', 'Manajemen Guru')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div class="flex gap-2 flex-wrap">
        <button onclick="showModal()" class="btn-brand !text-sm"><i class="bi bi-plus-lg"></i> Tambah Guru</button>
        <button onclick="showCsvModal()" class="btn-outline !text-sm"><i class="bi bi-upload"></i> Upload CSV</button>
        <a href="{{ route('admin.guru.template') }}" class="btn-outline !text-sm"><i class="bi bi-download"></i> Template CSV</a>
        <button onclick="confirmAction('Hapus SEMUA data guru? Tindakan ini tidak bisa dibatalkan!', () => document.getElementById('destroyAllGuru').submit())" class="btn-danger !text-sm !ml-auto"><i class="bi bi-trash"></i> Hapus Semua</button>
        <form id="destroyAllGuru" action="{{ route('admin.guru.destroy-all') }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
    </div>
</div>

<div class="card anim-up">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <h3 class="font-bold text-slate-900">Data Guru <span class="text-sm font-medium text-muted">({{ $teachers->total() }})</span></h3>
    </div>

    <form id="filterForm" class="flex flex-wrap items-center gap-2 mb-4 pb-3 border-b border-[#c3c6d1]/20">
        <input type="text" name="search" class="input-field !w-40 !py-1 !px-2 !text-xs" placeholder="Cari nama / NIP / email..." value="{{ request('search') }}" onchange="this.form.submit()">

        <select name="sort" class="input-field !w-auto !py-1.5 !text-sm" onchange="this.form.submit()">
            <option value="id" {{ request('sort', 'id') == 'id' ? 'selected' : '' }}>Urut: No.</option>
            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Urut: Nama</option>
            <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Urut: Terbaru</option>
        </select>

        @php $cd = request('dir', 'asc'); @endphp
        <select name="dir" class="input-field !w-auto !py-1.5 !text-sm" onchange="this.form.submit()">
            <option value="asc" {{ $cd == 'asc' ? 'selected' : '' }}>A-Z / 1-9</option>
            <option value="desc" {{ $cd == 'desc' ? 'selected' : '' }}>Z-A / 9-1</option>
        </select>

        <input type="hidden" name="per_page" value="{{ request('per_page', 25) }}">

        @if(request('sort') != 'id' || request('dir') != 'asc' || request('search'))
        <a href="{{ route('admin.guru') }}" class="text-xs text-red-500 hover:text-red-700 font-semibold ml-1"><i class="bi bi-x-circle"></i> Reset</a>
        @endif
    </form>

    <div class="table-container">
        <table class="w-full">
            <thead>
                <tr>
                    <th class="table-header">No.</th>
                    <th class="table-header">NIP</th>
                    <th class="table-header">Nama</th>
                    <th class="table-header">Role</th>
                    <th class="table-header">Kelas</th>
                    <th class="table-header">Status</th>
                    <th class="table-header text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($teachers as $i => $teacher)
                <tr>
                    <td class="table-cell text-sm font-mono">{{ $teachers->firstItem() + $i }}</td>
                    <td class="table-cell text-sm">{{ $teacher->nip ?? '—' }}</td>
                    <td class="table-cell font-semibold">{{ $teacher->name }}</td>
                    <td class="table-cell">
                        @if($teacher->role === 'wali_kelas')
                            <span class="badge badge-blue">Wali Kelas</span>
                        @else
                            <span class="badge badge-amber">BK</span>
                        @endif
                    </td>
                    <td class="table-cell text-sm">
                        @foreach($teacher->teacherClasses as $tc)
                            {{ $tc->schoolClass->name }}@if(!$loop->last), @endif
                        @endforeach
                        @if($teacher->teacherClasses->isEmpty()) — @endif
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
                            <button onclick="editGuru({{ $teacher->id }})" class="inline-flex items-center gap-1 px-2 py-1.5 rounded text-sm font-semibold bg-blue-600 text-white hover:bg-blue-700 transition cursor-pointer">
                                <i class="bi bi-pencil"></i> Ubah
                            </button>
                            <button onclick="confirmAction('Hapus guru {{ $teacher->name }}?', () => this.closest('tr').querySelector('.delForm').submit())" class="inline-flex items-center gap-1 px-2 py-1.5 rounded text-sm font-semibold bg-red-600 text-white hover:bg-red-700 transition cursor-pointer">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                            <form class="delForm hidden" action="{{ route('admin.guru.destroy', $teacher) }}" method="POST">@csrf @method('DELETE')</form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="table-cell text-center text-muted py-8">Belum ada data guru.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @include('components.pagination', ['data' => $teachers])
</div>

<!-- Add/Edit Modal -->
<div id="modalOverlay" class="hidden fixed inset-0 z-50 bg-black/40" onclick="closeModal()"></div>
<div id="modalBox" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-50 bg-white rounded-2xl shadow-xl w-full max-w-md p-6 anim-up max-h-[90vh] overflow-y-auto">
    <div class="flex justify-between items-center mb-4">
        <h3 id="modalTitle" class="font-bold text-lg">Tambah Guru</h3>
        <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg text-xl"></i></button>
    </div>
    <form id="guruForm" method="POST" action="{{ route('admin.guru.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="_method" id="formMethod" value="POST">
        <div>
            <label class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-2 block">Nama Lengkap</label>
            <input type="text" name="name" id="guruName" required class="input-field" placeholder="Nama lengkap dengan gelar">
        </div>
        <div>
            <label class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-2 block">NIP</label>
            <input type="text" name="nip" id="guruNip" class="input-field" placeholder="199209122022212018" maxlength="18" inputmode="numeric" pattern="\d*">
            <p class="text-xs text-muted mt-1">Kosongkan jika tidak ada NIP</p>
        </div>
        <div>
            <label class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-2 block">Role</label>
            <select name="role" id="guruRole" required class="input-field" onchange="toggleClassPicker()">
                <option value="wali_kelas">Wali Kelas</option>
                <option value="bk">BK</option>
            </select>
        </div>
        <div id="waliKelasPicker">
            <label class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-2 block">Kelas yang Dipegang</label>
            <select class="input-field" id="wkelasSelect">
                <option value="">— Pilih Kelas —</option>
                @foreach($classes as $cls)
                <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                @endforeach
            </select>
            <input type="hidden" name="class_ids[]" id="wkelasHidden">
        </div>
        <div id="bkPicker" class="hidden">
            <label class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-2 block">Kelas yang Dimonitor</label>
            <div class="max-h-40 overflow-y-auto border border-[#c3c6d1] rounded-lg p-2 space-y-1">
                @foreach($classes as $cls)
                <label class="flex items-center gap-2 px-2 py-1 hover:bg-slate-50 rounded cursor-pointer">
                    <input type="checkbox" name="class_ids[]" value="{{ $cls->id }}" class="bkCheckbox rounded">
                    <span class="text-sm">{{ $cls->name }}</span>
                </label>
                @endforeach
            </div>
        </div>
        <div class="flex gap-3 pt-2">
            <button type="button" onclick="closeModal()" class="btn-outline flex-1 !py-2.5">Batal</button>
            <button type="submit" class="btn-brand flex-1 !py-2.5" id="modalSubmit">
                <span class="btn-text"><i class="bi bi-check-circle"></i> Simpan</span>
                <span class="btn-loading hidden"><i class="bi bi-hourglass-split animate-spin"></i> Menyimpan...</span>
            </button>
        </div>
    </form>
</div>

<!-- CSV Upload Modal -->
<div id="csvOverlay" class="hidden fixed inset-0 z-50 bg-black/40" onclick="closeCsvModal()"></div>
<div id="csvBox" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-50 bg-white rounded-2xl shadow-xl w-full max-w-md p-6 anim-up">
    <div class="flex justify-between items-center mb-4"><h3 class="font-bold text-lg">Upload CSV Guru</h3><button onclick="closeCsvModal()" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg text-xl"></i></button></div>
    <form action="{{ route('admin.guru.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <p class="text-sm text-slate-500 mb-4">Upload file CSV dengan kolom: <strong>Nama;NIP</strong></p>
        <div class="border-2 border-dashed border-slate-200 rounded-xl p-6 text-center mb-4">
            <i class="bi bi-cloud-upload text-3xl text-slate-300 mb-2 block"></i>
            <p class="text-sm text-slate-600">Klik untuk pilih file</p>
            <p class="text-xs text-slate-400">.csv (max 2MB)</p>
            <input type="file" name="csv_file" accept=".csv" required class="mt-3 text-sm">
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-sm text-blue-700 mb-4">
            <strong>Format:</strong> "Nama";"NIP"<br><strong>Contoh:</strong> "BUDI SANTOSO, S.Pd";"199209122022212018"
        </div>
        <div class="flex gap-3 pt-2"><button type="button" onclick="closeCsvModal()" class="btn-outline flex-1 !py-2.5">Batal</button><button type="submit" class="btn-brand flex-1 !py-2.5"><span class="btn-text"><i class="bi bi-search"></i> Preview</span></button></div>
    </form>
</div>

@include('components.confirm-modal')

@push('scripts')
<script>
function toggleClassPicker() {
    const role = document.getElementById('guruRole').value;
    document.getElementById('waliKelasPicker').classList.toggle('hidden', role !== 'wali_kelas');
    document.getElementById('bkPicker').classList.toggle('hidden', role !== 'bk');
}
function setClassIds(ids) {
    if (document.getElementById('guruRole').value === 'wali_kelas') {
        document.getElementById('wkelasSelect').value = ids[0] || '';
        document.getElementById('wkelasHidden').value = ids[0] || '';
    } else {
        document.querySelectorAll('.bkCheckbox').forEach(cb => { cb.checked = ids.includes(parseInt(cb.value)); });
    }
}
function getClassIds() {
    if (document.getElementById('guruRole').value === 'wali_kelas') {
        const v = document.getElementById('wkelasSelect').value;
        document.getElementById('wkelasHidden').value = v;
        return v ? [v] : [];
    }
    return Array.from(document.querySelectorAll('.bkCheckbox:checked')).map(cb => cb.value);
}
function showModal(user) {
    document.getElementById('modalOverlay').classList.remove('hidden');
    document.getElementById('modalBox').classList.remove('hidden');
    document.getElementById('guruName').value = user ? user.name : '';
    document.getElementById('guruNip').value = user ? (user.nip || '') : '';
    document.getElementById('guruRole').value = user ? user.role : 'wali_kelas';
    document.getElementById('modalTitle').textContent = user ? 'Edit Guru' : 'Tambah Guru';
    document.getElementById('formMethod').value = user ? 'PUT' : 'POST';
    const form = document.getElementById('guruForm');
    form.action = user ? '/admin/guru/' + user.id : '{{ route("admin.guru.store") }}';
    if (user && user.teacher_classes) { setClassIds(user.teacher_classes.map(tc => tc.school_class_id)); }
    else { setClassIds([]); }
    toggleClassPicker();
}
function closeModal() { document.getElementById('modalOverlay').classList.add('hidden'); document.getElementById('modalBox').classList.add('hidden'); }
function showCsvModal() { document.getElementById('csvOverlay').classList.remove('hidden'); document.getElementById('csvBox').classList.remove('hidden'); }
function closeCsvModal() { document.getElementById('csvOverlay').classList.add('hidden'); document.getElementById('csvBox').classList.add('hidden'); }
function setLoading(btn, loading) {
    const t = btn.querySelector('.btn-text'), l = btn.querySelector('.btn-loading');
    if (t) t.classList.toggle('hidden', loading);
    if (l) l.classList.toggle('hidden', !loading);
    btn.disabled = loading;
}
document.getElementById('guruForm').addEventListener('submit', function(e) {
    const classIds = getClassIds();
    if (classIds.length === 0) { e.preventDefault(); showToast('Pilih minimal satu kelas.', 'warning'); return; }
    setLoading(document.getElementById('modalSubmit'), true);
});
document.getElementById('wkelasSelect').addEventListener('change', function() { document.getElementById('wkelasHidden').value = this.value; });
function editGuru(id) {
    fetch('/admin/guru/' + id + '/edit?_ajax=1', { headers: {'Accept':'application/json'} })
        .then(r => r.json())
        .then(data => showModal(data.user))
        .catch(() => showToast('Gagal memuat data guru.', 'error'));
}
</script>
@endpush
@endsection
