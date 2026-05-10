@extends('layouts.app')

@section('title', 'Manajemen Siswa - Admin')

@section('page-title', 'Manajemen Siswa')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div class="flex gap-2">
        <a href="{{ route('admin.siswa.tambah') }}" class="btn-brand !text-xs"><i class="bi bi-plus-lg"></i> Tambah Siswa</a>
        <button onclick="showModal()" class="btn-outline !text-xs"><i class="bi bi-upload"></i> Upload CSV</button>
        <a href="#" onclick="alert('UI Only: Download template CSV')" class="btn-outline !text-xs"><i class="bi bi-download"></i> Template CSV</a>
    </div>
</div>

<div class="card anim-up">
    @if(session('success'))
    <div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-xs font-semibold text-emerald-700 flex items-center gap-2" id="flashMsg">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        <button onclick="this.parentElement.remove()" class="ml-auto text-emerald-400 hover:text-emerald-600"><i class="bi bi-x"></i></button>
    </div>
    @endif
    @if(session('error'))
    <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-xs font-semibold text-red-700 flex items-center gap-2" id="flashMsg">
        <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
        <button onclick="this.parentElement.remove()" class="ml-auto text-red-400 hover:text-red-600"><i class="bi bi-x"></i></button>
    </div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <h3 class="font-bold text-slate-900">Data Siswa <span class="text-sm font-medium text-muted">({{ $totalSiswa }})</span></h3>
        <input type="text" class="input-field !w-48 !py-1.5 !text-xs" placeholder="Cari nama / NIS...">
    </div>
    <div class="table-container">
        <table class="w-full">
            <thead><tr><th class="table-header">No.</th><th class="table-header">NIS</th><th class="table-header">NISN</th><th class="table-header">Nama</th><th class="table-header">Kelas</th><th class="table-header">JK</th><th class="table-header">Nomor HP Siswa</th><th class="table-header">Nomor HP Orang Tua</th><th class="table-header">Aksi</th></tr></thead>
            <tbody>
                @forelse($students as $i => $s)
                <?php
                $mother = $s->parents->firstWhere('type', 'mother');
                $father = $s->parents->firstWhere('type', 'father');
                $parentPhone = $mother?->phone ?? $father?->phone ?? '—';
                ?>
                <tr data-id="{{ $s->id }}">
                    <td class="table-cell">{{ $i + 1 }}</td>
                    <td class="table-cell">{{ $s->nis }}</td>
                    <td class="table-cell">{{ $s->nisn ?? '—' }}</td>
                    <td class="table-cell font-semibold">{{ $s->name }}</td>
                    <td class="table-cell">{{ $s->kelas?->name ?? '—' }}</td>
                    <td class="table-cell">{{ $s->gender }}</td>
                    <td class="table-cell">—</td>
                    <td class="table-cell">{{ $parentPhone }}</td>
                    <td class="table-cell-aksi">
                        <div class="flex items-center justify-center gap-1">
                            <button onclick='lihatDetail({!! json_encode(array_merge($s->toArray(), ["kelas_name" => $s->kelas?->name, "father" => $father?->toArray(), "mother" => $mother?->toArray()]), JSON_HEX_APOS | JSON_HEX_QUOT) !!})' class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold border border-[#c3c6d1] text-[#43474f] hover:bg-[#edeeef] transition cursor-pointer"><i class="bi bi-eye"></i> Lihat</button>
                            <a href="{{ route('admin.siswa.edit') }}" class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold bg-blue-600 text-white hover:bg-blue-700 transition"><i class="bi bi-pencil"></i> Ubah</a>
                            <button onclick="if(confirm('Hapus {{ $s->name }}?')) this.closest('tr').remove()" class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold bg-red-600 text-white hover:bg-red-700 transition cursor-pointer"><i class="bi bi-trash"></i> Hapus</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td class="table-cell text-center text-muted" colspan="9">Belum ada data siswa. Upload CSV atau tambah manual.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Detail Modal -->
<div id="detailOverlay" class="hidden fixed inset-0 z-50 bg-black/40" onclick="closeDetail()"></div>
<div id="detailBox" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-50 bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 max-h-[85vh] overflow-y-auto"></div>

<!-- CSV Upload Modal -->
<div id="modalOverlay" class="hidden fixed inset-0 z-50 bg-black/40" onclick="closeModal()"></div>
<div id="modalBox" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-50 bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
    <div class="flex justify-between items-center mb-4"><h3 class="font-bold text-lg">Upload CSV Siswa</h3><button onclick="closeModal()" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg text-xl"></i></button></div>
    <form action="{{ route('admin.siswa.import') }}" method="POST" enctype="multipart/form-data" onsubmit="showLoad()">
        @csrf
        <p class="text-xs text-slate-500 mb-4">Upload file CSV dengan kolom: <strong>Nama, Jenis Kelamin, NIS, Kelas, NISN</strong></p>
        <div class="border-2 border-dashed border-slate-200 rounded-xl p-6 text-center mb-4">
            <i class="bi bi-cloud-upload text-3xl text-slate-300 mb-2 block"></i>
            <p id="fileName" class="text-sm text-slate-600 mb-3">Pilih file CSV</p>
            <p class="text-xs text-slate-400 mb-3">.csv (max 2MB)</p>
            <input type="file" name="csv_file" id="csvFile" accept=".csv" class="hidden" onchange="updateFileName()">
            <label for="csvFile" class="inline-flex items-center gap-1 px-4 py-2 rounded text-xs font-semibold bg-[#001e40] text-white cursor-pointer hover:bg-[#003366] transition">
                <i class="bi bi-folder2-open"></i> Pilih File CSV
            </label>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-xs text-blue-700">
            <strong>Template:</strong> Nama;Jenis Kelamin;NIS;Kelas;NISN<br>
            <strong>Contoh:</strong> "ABYAN MUNADHIL";"Laki-laki";"252610001";"10. 1";"0104227920"
        </div>
        <div class="flex gap-3 pt-2"><button type="button" onclick="closeModal()" class="btn-outline flex-1 !py-2.5">Batal</button><button type="submit" class="btn-brand flex-1 !py-2.5">Pratinjau</button></div>
    </form>
</div>

<!-- Loading Overlay -->
<div id="loadOverlay" class="hidden fixed inset-0 z-[200] bg-black/30 flex items-center justify-center">
    <div class="bg-white rounded-2xl shadow-xl px-8 py-6 flex flex-col items-center gap-3">
        <div class="w-10 h-10 border-4 border-[#001e40] border-t-transparent rounded-full animate-spin"></div>
        <p class="text-sm font-bold text-[#001e40]">Memproses CSV...</p>
    </div>
</div>

<style>@keyframes spin{to{transform:rotate(360deg)}}.animate-spin{animation:spin .8s linear infinite}</style>

@push('scripts')
<script>
function showModal(){document.getElementById('modalOverlay').classList.remove('hidden');document.getElementById('modalBox').classList.remove('hidden')}
function closeModal(){document.getElementById('modalOverlay').classList.add('hidden');document.getElementById('modalBox').classList.add('hidden')}
function updateFileName(){const f=document.getElementById('csvFile').files[0];document.getElementById('fileName').textContent=f?'✔ '+f.name:'Pilih file CSV'}
function showLoad(){document.getElementById('loadOverlay').classList.remove('hidden')}

function lihatDetail(s) {
    let html = `<div class="flex justify-between items-start mb-4"><div><h3 class="font-bold text-lg">${s.name}</h3><p class="text-sm text-muted">${s.nis} &middot; ${s.nisn || '—'}</p></div><button onclick="closeDetail()" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg text-xl"></i></button></div>`;
    html += `<div class="grid grid-cols-2 gap-3 text-sm">
        <div><span class="text-xs text-muted">NISN</span><p class="font-semibold">${s.nisn || '—'}</p></div>
        <div><span class="text-xs text-muted">JK</span><p class="font-semibold">${s.gender}</p></div>
        <div><span class="text-xs text-muted">Tempat Lahir</span><p class="font-semibold">${s.birth_place || '—'}</p></div>
        <div><span class="text-xs text-muted">Tgl Lahir</span><p class="font-semibold">${s.birth_date || '—'}</p></div>
        <div><span class="text-xs text-muted">Agama</span><p class="font-semibold">${s.religion || '—'}</p></div>
        <div><span class="text-xs text-muted">Status</span><p class="font-semibold">${s.family_status || '—'}</p></div>
        <div class="col-span-2"><span class="text-xs text-muted">Alamat</span><p class="font-semibold">${s.address || '—'}</p></div>
        <div><span class="text-xs text-muted">Kelas</span><p class="font-semibold">${s.kelas_name || '—'}</p></div>
        <div><span class="text-xs text-muted">Anak ke</span><p class="font-semibold">${s.birth_order || '—'}</p></div>
    </div>`;
    html += `<hr class="my-3"><p class="text-xs font-bold text-purple-600 uppercase mb-2">Orang Tua</p>`;
    html += `<div class="grid grid-cols-2 gap-2 text-sm">`;
    if (s.father) html += `<div><span class="text-xs text-muted">Ayah</span><p class="font-semibold">${s.father.name || '—'}</p><p class="text-xs text-muted">${s.father.job || ''} &middot; ${s.father.phone || ''}</p></div>`;
    if (s.mother) html += `<div><span class="text-xs text-muted">Ibu</span><p class="font-semibold">${s.mother.name || '—'}</p><p class="text-xs text-muted">${s.mother.job || ''} &middot; ${s.mother.phone || ''}</p></div>`;
    html += `</div><button onclick="closeDetail()" class="btn-outline w-full !py-2 mt-4">Tutup</button>`;
    document.getElementById('detailBox').innerHTML = html;
    document.getElementById('detailOverlay').classList.remove('hidden');
    document.getElementById('detailBox').classList.remove('hidden');
}
function closeDetail() {
    document.getElementById('detailOverlay').classList.add('hidden');
    document.getElementById('detailBox').classList.add('hidden');
}
</script>
@endpush
@endsection