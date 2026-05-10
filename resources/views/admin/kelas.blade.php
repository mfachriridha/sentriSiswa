@extends('layouts.app')

@section('title', 'Manajemen Kelas - Admin')

@section('page-title', 'Manajemen Kelas')

@section('content')
<div class="flex justify-end mb-6">
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
    <h3 class="font-bold text-slate-900 mb-4">Data Kelas <span class="text-sm font-medium text-muted">({{ $kelases->count() }})</span></h3>
    <div class="table-container">
        <table class="w-full">
            <thead><tr><th class="table-header">No.</th><th class="table-header">Nama Kelas</th><th class="table-header">Jumlah Siswa</th><th class="table-header">Aksi</th></tr></thead>
            <tbody>
                @forelse($kelases as $i => $k)
                <tr>
                    <td class="table-cell">{{ $i + 1 }}</td>
                    <td class="table-cell font-semibold">{{ $k->name }}</td>
                    <td class="table-cell">{{ $k->students_count }}</td>
                    <td class="table-cell-aksi">
                        <div class="flex items-center justify-center gap-1">
                            <button onclick='editKelas({{ $k->id }}, "{{ $k->name }}")' class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold bg-blue-600 text-white hover:bg-blue-700 transition cursor-pointer"><i class="bi bi-pencil"></i> Ubah</button>
                            <form action="{{ route('admin.kelas.destroy', $k) }}" method="POST" onsubmit="return confirm('Hapus kelas {{ $k->name }}?')" class="inline">
                                @csrf @method('DELETE')
                                <button class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold bg-red-600 text-white hover:bg-red-700 transition cursor-pointer"><i class="bi bi-trash"></i> Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td class="table-cell text-center text-muted" colspan="4">Belum ada data kelas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="modalOverlay" class="hidden fixed inset-0 z-50 bg-black/40" onclick="closeModal()"></div>
<div id="modalBox" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-50 bg-white rounded-2xl shadow-xl w-full max-w-sm p-6"></div>

@push('scripts')
<script>
function showModal(type, id, name) {
    const box = document.getElementById('modalBox');
    let title = type === 'tambah' ? 'Tambah Kelas' : 'Edit Kelas';
    let action = type === 'tambah'
        ? '{{ route("admin.kelas.store") }}'
        : '{{ route("admin.kelas.update", "__ID__") }}'.replace('__ID__', id);
    let method = type === 'tambah' ? '' : '@method("PUT")';
    box.innerHTML = `<div class="flex justify-between items-center mb-4"><h3 class="font-bold text-lg">${title}</h3><button onclick="closeModal()" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg text-xl"></i></button></div>
        <form action="${action}" method="POST" class="space-y-3">
            @csrf ${method}
            <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Nama Kelas <span class="text-red-500">*</span></label><input type="text" name="name" class="input-field" value="${name||''}" placeholder="Contoh: XII IPA 1" required></div>
            <div class="flex gap-3 pt-2"><button type="button" onclick="closeModal()" class="btn-outline flex-1 !py-2.5">Batal</button><button type="submit" class="btn-brand flex-1 !py-2.5">Simpan</button></div>
        </form>`;
    document.getElementById('modalOverlay').classList.remove('hidden');
    box.classList.remove('hidden');
}
function closeModal(){document.getElementById('modalOverlay').classList.add('hidden');document.getElementById('modalBox').classList.add('hidden')}
function editKelas(id, name){showModal('edit', id, name)}
</script>
@endpush
@endsection