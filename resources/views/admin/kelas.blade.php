@extends('layouts.app')

@section('title', 'Manajemen Kelas - Admin')

@section('page-title', 'Manajemen Kelas')

@section('content')
<div class="flex justify-end mb-6 gap-2">
    <button onclick="confirmAction('Hapus SEMUA kelas yang tidak memiliki siswa?', () => document.getElementById('destroyAllKelas').submit())" class="btn-danger !text-xs"><i class="bi bi-trash"></i> Hapus Kelas Kosong</button>
    <form id="destroyAllKelas" action="{{ route('admin.kelas.destroy-all') }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
    <button onclick="showModal('tambah')" class="btn-brand !text-xs"><i class="bi bi-plus-lg"></i> Tambah Kelas</button>
</div>

@if(session('success'))
<div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-xs font-semibold text-emerald-700 flex items-center gap-2">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    <button onclick="this.parentElement.remove()" class="ml-auto text-emerald-400 hover:text-emerald-600"><i class="bi bi-x"></i></button>
</div>
@endif
@if(session('error'))
<div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-xs font-semibold text-red-700 flex items-center gap-2">
    <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
    <button onclick="this.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-600"><i class="bi bi-x"></i></button>
</div>
@endif

<div class="card anim-up">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <h3 class="font-bold text-slate-900">Data Kelas <span class="text-sm font-medium text-muted">({{ $classes->total() }})</span></h3>
    </div>

    <!-- Filters -->
    <form id="filterForm" class="flex flex-wrap items-center gap-2 mb-4 pb-3 border-b border-[#c3c6d1]/20">
        <select name="prefix" class="input-field !w-auto !py-1 !px-2 !text-xs" onchange="this.form.submit()">
            <option value="">Semua Tingkat</option>
            @foreach($prefixes as $p)
            <option value="{{ $p }}" {{ request('prefix') == $p ? 'selected' : '' }}>{{ $p }}</option>
            @endforeach
        </select>

        <select name="sort" class="input-field !w-auto !py-1 !px-2 !text-xs" onchange="this.form.submit()">
            <option value="id" {{ request('sort', 'id') == 'id' ? 'selected' : '' }}>Urut: No.</option>
            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Urut: Nama</option>
            <option value="students_count" {{ request('sort') == 'students_count' ? 'selected' : '' }}>Urut: Jumlah Siswa</option>
            <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Urut: Terbaru</option>
        </select>

        @php $currentDir = request('dir', 'asc'); @endphp
        <select name="dir" class="input-field !w-auto !py-1 !px-2 !text-xs" onchange="this.form.submit()">
            <option value="asc" {{ $currentDir == 'asc' ? 'selected' : '' }}>A-Z / 1-9</option>
            <option value="desc" {{ $currentDir == 'desc' ? 'selected' : '' }}>Z-A / 9-1</option>
        </select>

        <input type="hidden" name="per_page" value="{{ request('per_page', 25) }}">

        @if(request()->anyFilled(['prefix', 'sort', 'dir']) && (request('prefix') || request('sort') != 'id' || request('dir') != 'asc'))
        <a href="{{ route('admin.kelas') }}" class="text-[10px] text-red-500 hover:text-red-700 font-semibold ml-1"><i class="bi bi-x-circle"></i> Reset</a>
        @endif
    </form>

    <div class="table-container">
        <table class="w-full">
            <thead><tr><th class="table-header">No.</th><th class="table-header">Nama Kelas</th><th class="table-header">Jumlah Siswa</th><th class="table-header">Aksi</th></tr></thead>
            <tbody>
                @forelse($classes as $i => $k)
                <tr>
                    <td class="table-cell font-mono text-xs">{{ $k->id }}</td>
                    <td class="table-cell font-semibold">{{ $k->name }}</td>
                    <td class="table-cell">{{ $k->students_count }}</td>
                    <td class="table-cell-aksi">
                        <div class="flex items-center justify-center gap-1">
                            <button onclick="lihatKelas({{ $k->id }}, '{{ $k->name }}')" class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold border border-[#c3c6d1] text-[#43474f] hover:bg-[#edeeef] transition cursor-pointer"><i class="bi bi-eye"></i> Lihat</button>
                            <button onclick='editKelas({{ $k->id }}, "{{ $k->name }}")' class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold bg-blue-600 text-white hover:bg-blue-700 transition cursor-pointer"><i class="bi bi-pencil"></i> Ubah</button>
                            <button onclick="confirmAction('Hapus kelas {{ $k->name }}? Semua siswa akan kehilangan kelas.', () => deleteKelas({{ $k->id }}))" class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold bg-red-600 text-white hover:bg-red-700 transition cursor-pointer"><i class="bi bi-trash"></i> Hapus</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td class="table-cell text-center text-muted" colspan="4">Belum ada data kelas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @include('components.pagination', ['data' => $classes])
</div>

<!-- Edit Kelas Modal -->
<div id="modalOverlay" class="hidden fixed inset-0 z-50 bg-black/40" onclick="closeModal()"></div>
<div id="modalBox" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-50 bg-white rounded-2xl shadow-xl w-full max-w-sm p-6"></div>

<!-- Kelas Detail Modal -->
<div id="kelasOverlay" class="hidden fixed inset-0 z-50 bg-black/40" onclick="closeKelasModal()"></div>
<div id="kelasBox" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-50 bg-white rounded-2xl shadow-xl w-full max-w-3xl p-6 max-h-[90vh] overflow-y-auto anim-up"></div>

<!-- Student Detail Overlay -->
<div id="detailOverlay" class="hidden fixed inset-0 z-[60] bg-black/40" onclick="closeDetail()"></div>
<div id="detailBox" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-[60] bg-white rounded-2xl shadow-xl w-full max-w-3xl p-6 max-h-[90vh] overflow-y-auto anim-up"></div>

@include('components.confirm-modal')

<!-- Toast -->
<div id="toast" class="hidden fixed top-4 right-4 z-[200] px-4 py-3 rounded-lg shadow-lg text-sm font-semibold text-white anim-up"></div>

@push('scripts')
<script>
// ── Toast ──
function showToast(msg, type) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'fixed top-4 right-4 z-[200] px-4 py-3 rounded-lg shadow-lg text-sm font-semibold text-white anim-up ' + (type === 'success' ? 'bg-emerald-600' : 'bg-red-600');
    t.classList.remove('hidden');
    setTimeout(() => t.classList.add('hidden'), 3000);
}

function setLoading(btn, loading) {
    const t = btn.querySelector('.btn-text'), l = btn.querySelector('.btn-loading');
    if (t) t.classList.toggle('hidden', loading);
    if (l) l.classList.toggle('hidden', !loading);
    btn.disabled = loading;
}

// ── Delete Kelas ──
function deleteKelas(id) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/kelas/' + id;
    form.innerHTML = '@csrf @method("DELETE")';
    document.body.appendChild(form);
    form.submit();
}

// ── CRUD Modal ──
function showModal(type, id, name) {
    const box = document.getElementById('modalBox');
    const title = type === 'tambah' ? 'Tambah Kelas' : 'Edit Kelas';
    const action = type === 'tambah'
        ? '{{ route("admin.kelas.store") }}'
        : '{{ route("admin.kelas.update", "__ID__") }}'.replace('__ID__', id);
    const method = type === 'tambah' ? '' : '@method("PUT")';
    box.innerHTML = `<div class="flex justify-between items-center mb-4"><h3 class="font-bold text-lg">${title}</h3><button onclick="closeModal()" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg text-xl"></i></button></div>
        <form action="${action}" method="POST" class="space-y-3" onsubmit="setLoading(this.querySelector('[type=submit]'), true)">
            @csrf ${method}
            <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Nama Kelas <span class="text-red-500">*</span></label><input type="text" name="name" class="input-field" value="${name||''}" placeholder="Contoh: XII IPA 1" required></div>
            <div class="flex gap-3 pt-2"><button type="button" onclick="closeModal()" class="btn-outline flex-1 !py-2.5">Batal</button><button type="submit" class="btn-brand flex-1 !py-2.5"><span class="btn-text"><i class="bi bi-check-circle"></i> Simpan</span><span class="btn-loading hidden"><i class="bi bi-hourglass-split animate-spin"></i> Menyimpan...</span></button></div>
        </form>`;
    document.getElementById('modalOverlay').classList.remove('hidden');
    box.classList.remove('hidden');
}
function closeModal() { document.getElementById('modalOverlay').classList.add('hidden'); document.getElementById('modalBox').classList.add('hidden'); }
function editKelas(id, name) { showModal('edit', id, name); }

// ── Kelas Detail ──
function lihatKelas(id, name) {
    const box = document.getElementById('kelasBox');
    box.innerHTML = '<div class="text-center py-8"><div class="w-8 h-8 border-4 border-[#001e40] border-t-transparent rounded-full animate-spin mx-auto mb-3"></div><p class="text-sm text-muted">Memuat data...</p></div>';
    document.getElementById('kelasOverlay').classList.remove('hidden');
    box.classList.remove('hidden');
    fetch('/admin/kelas/' + id + '/students', { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(students => renderKelasBox(id, name, students));
}

function renderKelasBox(id, name, students) {
    const rows = students.length === 0
        ? '<tr><td class="table-cell text-center text-muted py-4" colspan="5">Tidak ada siswa di kelas ini.</td></tr>'
        : students.map((s, i) => `<tr>
            <td class="table-cell">${i + 1}</td>
            <td class="table-cell font-mono text-xs">${s.nis}</td>
            <td class="table-cell font-semibold">${s.name}</td>
            <td class="table-cell text-xs">${s.gender || '—'}</td>
            <td class="table-cell-aksi">
                <button onclick="lihatSiswa(${s.id})" class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold border border-[#c3c6d1] text-[#43474f] hover:bg-[#edeeef] transition cursor-pointer"><i class="bi bi-eye"></i> Lihat</button>
                <button onclick="confirmAction('Keluarkan ${s.name.replace(/'/g, "\\'")} dari kelas ini?', () => removeStudent(${id}, ${s.id}))" class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold bg-red-600 text-white hover:bg-red-700 transition cursor-pointer"><i class="bi bi-x-circle"></i> Keluarkan</button>
            </td>
        </tr>`).join('');

    document.getElementById('kelasBox').innerHTML = `
        <div class="flex justify-between items-start mb-4">
            <div><h3 class="font-bold text-lg">Kelas ${name}</h3><p class="text-xs text-muted">${students.length} siswa</p></div>
            <div class="flex gap-2">
                <button onclick="showAddStudent(${id})" class="btn-outline !text-xs"><i class="bi bi-plus-lg"></i> Tambah Siswa</button>
                <button onclick="closeKelasModal()" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg text-xl"></i></button>
            </div>
        </div>
        <div id="searchAddBox_${id}" class="hidden mb-3 p-3 bg-slate-50 rounded-lg border border-[#c3c6d1]/30">
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 block">Cari Siswa (Nama / NIS)</label>
            <input type="text" id="searchInput_${id}" class="input-field !py-1.5" placeholder="Ketik minimal 2 karakter..." oninput="searchSiswa(${id}, this.value)">
            <div id="searchResults_${id}" class="mt-2 space-y-1 max-h-48 overflow-y-auto"></div>
        </div>
        <div class="table-container">
            <table class="w-full">
                <thead><tr><th class="table-header">No.</th><th class="table-header">NIS</th><th class="table-header">Nama</th><th class="table-header">JK</th><th class="table-header text-center">Aksi</th></tr></thead>
                <tbody>${rows}</tbody>
            </table>
        </div>`;
}

function closeKelasModal() { document.getElementById('kelasOverlay').classList.add('hidden'); document.getElementById('kelasBox').classList.add('hidden'); }

// ── Add Student Search ──
function showAddStudent(classId) {
    const box = document.getElementById('searchAddBox_' + classId);
    if (!box) return;
    box.classList.toggle('hidden');
    if (!box.classList.contains('hidden')) {
        const inp = document.getElementById('searchInput_' + classId);
        if (inp) inp.focus();
    }
}

let searchTimer;
function searchSiswa(classId, q) {
    clearTimeout(searchTimer);
    const results = document.getElementById('searchResults_' + classId);
    if (!results) return;
    if (q.length < 2) { results.innerHTML = ''; return; }
    results.innerHTML = '<p class="text-xs text-muted p-2">Mencari...</p>';
    searchTimer = setTimeout(() => {
        fetch('/admin/siswa/search?q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(students => {
                if (students.length === 0) { results.innerHTML = '<p class="text-xs text-muted p-2">Tidak ditemukan.</p>'; return; }
                results.innerHTML = students.map(s => `<div class="flex items-center justify-between px-2 py-1.5 hover:bg-white rounded cursor-pointer border-b border-slate-100 last:border-0" onclick="assignStudent(${classId}, ${s.id}, '${s.name.replace(/'/g, "\\'")}')">
                    <div><span class="text-xs font-semibold">${s.name}</span><span class="text-[10px] text-muted ml-2">NIS: ${s.nis}</span></div>
                    <span class="text-[10px] text-blue-600 font-semibold"><i class="bi bi-plus-circle"></i> Tambah</span>
                </div>`).join('');
            });
    }, 300);
}

// ── Actions ──
function assignStudent(classId, studentId, name) {
    confirmAction('Tambahkan ' + name + ' ke kelas ini? Siswa hanya bisa berada di satu kelas.', () => {
        fetch('/admin/kelas/' + classId + '/assign', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ student_id: studentId })
        })
        .then(r => r.json())
        .then(d => { showToast(d.message, d.success ? 'success' : 'error'); if (d.success) location.reload(); });
    });
}

function removeStudent(classId, studentId) {
    fetch('/admin/kelas/' + classId + '/remove', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ student_id: studentId })
    })
    .then(r => r.json())
    .then(d => { showToast(d.message, d.success ? 'success' : 'error'); if (d.success) location.reload(); });
}

// ── Student Detail ──
function lihatSiswa(id) {
    const box = document.getElementById('detailBox');
    box.innerHTML = '<div class="text-center py-8"><div class="w-8 h-8 border-4 border-[#001e40] border-t-transparent rounded-full animate-spin mx-auto mb-3"></div><p class="text-sm text-muted">Memuat...</p></div>';
    document.getElementById('detailOverlay').classList.remove('hidden');
    box.classList.remove('hidden');
    fetch('/admin/siswa/' + id + '/detail', { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(s => {
            const father = s.father || {}, mother = s.mother || {};
            let html = `<div class="flex justify-between items-start mb-5"><div class="flex items-start gap-4"><div class="w-20 h-24 bg-slate-100 border-2 border-dashed border-slate-300 rounded flex items-center justify-center shrink-0"><div class="text-center"><i class="bi bi-person-fill text-3xl text-slate-300"></i><p class="text-[8px] text-slate-400 mt-0.5">3x4</p></div></div><div><h3 class="font-bold text-xl">${s.name}</h3><p class="text-sm font-mono text-muted mt-1"><strong>NIS:</strong> <strong class="text-[#001e40]">${s.nis}</strong> &middot; <strong>NISN:</strong> <strong class="text-[#001e40]">${s.nisn || '—'}</strong></p><p class="text-xs text-muted">${s.school_class?.name || '—'}</p></div></div><button onclick="closeDetail()" class="text-slate-400 hover:text-slate-600 shrink-0"><i class="bi bi-x-lg text-2xl"></i></button></div>`;
            html += `<div class="grid grid-cols-3 gap-4 text-sm mb-4">
                <div><span class="text-xs text-muted">Jenis Kelamin</span><p class="font-semibold">${s.gender || '—'}</p></div>
                <div><span class="text-xs text-muted">Tempat Lahir</span><p class="font-semibold">${s.birth_place || '—'}</p></div>
                <div><span class="text-xs text-muted">Tanggal Lahir</span><p class="font-semibold">${s.birth_date || '—'}</p></div>
                <div><span class="text-xs text-muted">Agama</span><p class="font-semibold">${s.religion || '—'}</p></div>
                <div><span class="text-xs text-muted">Anak ke</span><p class="font-semibold">${s.birth_order || '—'}</p></div>
                <div><span class="text-xs text-muted">Status Keluarga</span><p class="font-semibold">${s.family_status || '—'}</p></div>
                <div class="col-span-3"><span class="text-xs text-muted">Alamat</span><p class="font-semibold">${s.address || '—'}</p></div></div>`;
            html += '<hr class="mb-4"><h4 class="text-sm font-bold text-purple-600 uppercase mb-3">Orang Tua</h4><div class="grid grid-cols-2 gap-4 text-sm mb-4">';
            if (father.name) html += `<div class="p-3 bg-slate-50 rounded-xl"><span class="text-xs text-muted">Ayah</span><p class="font-semibold">${father.name || '—'}</p><p class="text-xs text-muted">${father.job || ''} &middot; ${father.phone || ''}</p></div>`;
            if (mother.name) html += `<div class="p-3 bg-slate-50 rounded-xl"><span class="text-xs text-muted">Ibu</span><p class="font-semibold">${mother.name || '—'}</p><p class="text-xs text-muted">${mother.job || ''} &middot; ${mother.phone || ''}</p></div>`;
            html += '</div><button onclick="closeDetail()" class="btn-outline w-full !py-2.5">Tutup</button>';
            box.innerHTML = html;
        });
}
function closeDetail() { document.getElementById('detailOverlay').classList.add('hidden'); document.getElementById('detailBox').classList.add('hidden'); }
</script>
@endpush
@endsection
