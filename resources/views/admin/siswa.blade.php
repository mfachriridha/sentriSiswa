@extends('layouts.app')

@section('title', 'Manajemen Siswa - Admin')

@section('page-title', 'Manajemen Siswa')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div class="flex gap-2 flex-wrap">
        <a href="{{ route('admin.siswa.tambah') }}" class="btn-brand !text-sm"><i class="bi bi-plus-lg"></i> Tambah Siswa</a>
        <button onclick="showModal()" class="btn-outline !text-sm"><i class="bi bi-upload"></i> Upload CSV</button>
        <a href="{{ route('admin.siswa.template') }}" class="btn-outline !text-sm"><i class="bi bi-download"></i> Template CSV</a>
        <button onclick="confirmAction('Hapus SEMUA data siswa? Tindakan ini tidak bisa dibatalkan!', () => document.getElementById('destroyAllSiswa').submit())" class="btn-danger !text-sm !ml-auto"><i class="bi bi-trash"></i> Hapus Semua</button>
        <form id="destroyAllSiswa" action="{{ route('admin.siswa.destroy-all') }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
    </div>
</div>

<div class="card anim-up">
    <form id="filterForm" class="flex flex-wrap items-center gap-2 mb-4 pb-3 border-b border-[#c3c6d1]/20">
        <input type="text" name="search" class="input-field !w-40 !py-1 !px-2 !text-xs" placeholder="Cari nama / NIS / NISN..." value="{{ request('search') }}" onchange="this.form.submit()">

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
        <a href="{{ route('admin.siswa') }}" class="text-xs text-red-500 hover:text-red-700 font-semibold ml-1"><i class="bi bi-x-circle"></i> Reset</a>
        @endif
    </form>
    <div class="table-container">
        <table class="w-full">
            <thead><tr><th class="table-header">No.</th><th class="table-header">NIS</th><th class="table-header">Nama</th><th class="table-header">Kelas</th><th class="table-header">JK</th><th class="table-header">Status</th><th class="table-header">Aksi</th></tr></thead>
            <tbody>
                @forelse($students as $i => $s)
                <?php
                $mother = $s->parents->firstWhere('type', 'mother');
                $father = $s->parents->firstWhere('type', 'father');
                ?>
                <tr data-id="{{ $s->id }}">
                    <td class="table-cell text-sm font-mono">{{ $students->firstItem() + $i }}</td>
                    <td class="table-cell font-mono"><strong>{{ $s->nis }}</strong></td>
                    <td class="table-cell font-semibold">{{ $s->name }}</td>
                    <td class="table-cell">{{ $s->schoolClass?->name ?? '—' }}</td>
                    <td class="table-cell">{{ $s->gender }}</td>
                    <td class="table-cell">
                        @if($s->user_id)
                            <span class="badge badge-green">Aktif</span>
                        @else
                            <span class="badge badge-red">Belum Registrasi</span>
                        @endif
                    </td>
                    <td class="table-cell-aksi">
                        <div class="flex items-center justify-center gap-1">
                            <button onclick='lihatDetail({!! json_encode(array_merge($s->toArray(), ["schoolClass_name" => $s->schoolClass?->name, "father" => $father?->toArray(), "mother" => $mother?->toArray()]), JSON_HEX_APOS | JSON_HEX_QUOT) !!})' class="inline-flex items-center gap-1 px-2 py-1.5 rounded text-sm font-semibold border border-[#c3c6d1] text-[#43474f] hover:bg-[#edeeef] transition cursor-pointer"><i class="bi bi-eye"></i> Lihat</button>
                            <a href="{{ route('admin.siswa.edit', $s->id) }}" class="inline-flex items-center gap-1 px-2 py-1.5 rounded text-sm font-semibold bg-blue-600 text-white hover:bg-blue-700 transition"><i class="bi bi-pencil"></i> Ubah</a>
                            <button onclick="confirmAction('Hapus {{ $s->name }}?', () => this.closest('tr').querySelector('form').submit())" class="inline-flex items-center gap-1 px-2 py-1.5 rounded text-sm font-semibold bg-red-600 text-white hover:bg-red-700 transition cursor-pointer"><i class="bi bi-trash"></i> Hapus</button>
                            <form action="{{ route('admin.siswa.destroy', $s) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td class="table-cell text-center text-muted" colspan="7">Belum ada data siswa. Upload CSV atau tambah manual.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @include('components.pagination', ['data' => $students])
</div>

<!-- Detail Modal (JIRA-style wide) -->
<div id="detailOverlay" class="hidden fixed inset-0 z-50 bg-black/40" onclick="closeDetail()"></div>
<div id="detailBox" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-50 bg-white rounded-2xl shadow-xl w-full max-w-3xl p-6 max-h-[90vh] overflow-y-auto"></div>

<!-- CSV Upload Modal -->
<div id="modalOverlay" class="hidden fixed inset-0 z-50 bg-black/40" onclick="closeModal()"></div>
<div id="modalBox" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-50 bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
    <div class="flex justify-between items-center mb-4"><h3 class="font-bold text-lg">Upload CSV Siswa</h3><button onclick="closeModal()" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg text-xl"></i></button></div>
    <form action="{{ route('admin.siswa.import') }}" method="POST" enctype="multipart/form-data" onsubmit="showLoading('Memproses CSV...')">
        @csrf
        <p class="text-sm text-slate-500 mb-4">Upload file CSV dengan kolom: <strong>Nama, Jenis Kelamin, NIS, Kelas, NISN</strong></p>
        <div class="border-2 border-dashed border-slate-200 rounded-xl p-6 text-center mb-4">
            <i class="bi bi-cloud-upload text-3xl text-slate-300 mb-2 block"></i>
            <p id="fileName" class="text-sm text-slate-600 mb-3">Pilih file CSV</p>
            <p class="text-xs text-slate-400 mb-3">.csv (max 2MB)</p>
            <input type="file" name="csv_file" id="csvFile" accept=".csv" class="hidden" onchange="updateFileName()">
            <label for="csvFile" class="inline-flex items-center gap-1 px-4 py-2 rounded text-sm font-semibold bg-[#1c6880] text-white cursor-pointer hover:bg-[#155066] transition">
                <i class="bi bi-folder2-open"></i> Pilih File CSV
            </label>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-sm text-blue-700">
            <strong>Template:</strong> Nama;Jenis Kelamin;NIS;Kelas;NISN<br>
            <strong>Contoh:</strong> "ABYAN MUNADHIL";"Laki-laki";"252610001";"10. 1";"0104227920"
        </div>
        <div class="flex gap-3 pt-2"><button type="button" onclick="closeModal()" class="btn-outline flex-1 !py-2.5">Batal</button><button type="submit" class="btn-brand flex-1 !py-2.5">Pratinjau</button></div>
    </form>
</div>

@include('components.confirm-modal')

@push('scripts')
<script>
function showModal(){document.getElementById('modalOverlay').classList.remove('hidden');document.getElementById('modalBox').classList.remove('hidden')}
function closeModal(){document.getElementById('modalOverlay').classList.add('hidden');document.getElementById('modalBox').classList.add('hidden')}
function updateFileName(){const f=document.getElementById('csvFile').files[0];document.getElementById('fileName').textContent=f?'✔ '+f.name:'Pilih file CSV'}

function lihatDetail(s) {
    let html=`<div class="flex justify-between items-start mb-5"><div class="flex items-start gap-4"><div class="w-20 h-24 bg-slate-100 border-2 border-dashed border-slate-300 rounded flex items-center justify-center shrink-0"><div class="text-center"><i class="bi bi-person-fill text-3xl text-slate-300"></i><p class="text-[10px] text-slate-400 mt-0.5">3x4</p></div></div><div><h3 class="font-bold text-xl">${s.name}</h3><p class="text-sm font-mono text-muted mt-1"><strong>NIS:</strong> <strong class="text-[#1c6880]">${s.nis}</strong> &middot; <strong>NISN:</strong> <strong class="text-[#1c6880]">${s.nisn||'—'}</strong></p><p class="text-sm text-muted">${s.schoolClass_name||'—'}</p></div></div><button onclick="closeDetail()" class="text-slate-400 hover:text-slate-600 shrink-0"><i class="bi bi-x-lg text-2xl"></i></button></div>`;

    html+=`<div class="grid grid-cols-3 gap-4 text-sm mb-4">
        <div><span class="text-sm text-muted">Jenis Kelamin</span><p class="font-semibold">${s.gender||'—'}</p></div>
        <div><span class="text-sm text-muted">Tempat Lahir</span><p class="font-semibold">${s.birth_place||'—'}</p></div>
        <div><span class="text-sm text-muted">Tanggal Lahir</span><p class="font-semibold">${s.birth_date||'—'}</p></div>
        <div><span class="text-sm text-muted">Agama</span><p class="font-semibold">${s.religion||'—'}</p></div>
        <div><span class="text-sm text-muted">Anak ke</span><p class="font-semibold">${s.birth_order||'—'}</p></div>
        <div><span class="text-sm text-muted">Status Keluarga</span><p class="font-semibold">${s.family_status||'—'}</p></div>
        <div class="col-span-3"><span class="text-sm text-muted">Alamat</span><p class="font-semibold">${s.address||'—'}</p></div>
    </div>`;

    html+=`<hr class="mb-4"><h4 class="text-sm font-bold text-purple-600 uppercase mb-3">Orang Tua</h4><div class="grid grid-cols-2 gap-4 text-sm mb-4">`;
    if(s.father)html+=`<div class="p-3 bg-slate-50 rounded-xl"><span class="text-sm text-muted">Ayah</span><p class="font-semibold">${s.father.name||'—'}</p><p class="text-sm text-muted">${s.father.job||''} &middot; ${s.father.phone||''}</p></div>`;
    if(s.mother)html+=`<div class="p-3 bg-slate-50 rounded-xl"><span class="text-sm text-muted">Ibu</span><p class="font-semibold">${s.mother.name||'—'}</p><p class="text-sm text-muted">${s.mother.job||''} &middot; ${s.mother.phone||''}</p></div>`;
    html+=`</div>`;

    html+=`<button onclick="closeDetail()" class="btn-outline w-full !py-2.5">Tutup</button>`;
    document.getElementById('detailBox').innerHTML=html;
    document.getElementById('detailOverlay').classList.remove('hidden');
    document.getElementById('detailBox').classList.remove('hidden');
}
function closeDetail(){document.getElementById('detailOverlay').classList.add('hidden');document.getElementById('detailBox').classList.add('hidden')}
</script>
@endpush
@endsection
