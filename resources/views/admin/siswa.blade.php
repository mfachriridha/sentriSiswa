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
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <h3 class="font-bold text-slate-900">Data Siswa <span class="text-sm font-medium text-muted">(360)</span></h3>
        <input type="text" class="input-field !w-48 !py-1.5 !text-xs" placeholder="Cari nama / NIS...">
    </div>
    <div class="table-container">
        <table class="w-full">
            <thead><tr><th class="table-header">NIS</th><th class="table-header">NISN</th><th class="table-header">Nama</th><th class="table-header">Kelas</th><th class="table-header">JK</th><th class="table-header">WA</th><th class="table-header">WA Ortu</th><th class="table-header">Aksi</th></tr></thead>
            <tbody>
                <tr><td class="table-cell">S001</td><td class="table-cell">0087654321</td><td class="table-cell font-semibold">Ahmad Fauzi</td><td class="table-cell">XII IPA 1</td><td class="table-cell">L</td><td class="table-cell">081312345678</td><td class="table-cell">081398765432</td><td class="table-cell-aksi"><div class="flex items-center justify-center gap-1"><button onclick="showDetail(0)" class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold border border-[#c3c6d1] text-[#43474f] hover:bg-[#edeeef] transition cursor-pointer"><i class="bi bi-eye"></i> Lihat</button><a href="{{ route('admin.siswa.edit') }}" class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold bg-blue-600 text-white hover:bg-blue-700 transition"><i class="bi bi-pencil"></i> Ubah</a><button onclick="alert('Hapus Ahmad Fauzi?')" class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold bg-red-600 text-white hover:bg-red-700 transition cursor-pointer"><i class="bi bi-trash"></i> Hapus</button></div></td></tr>
                <tr><td class="table-cell">S002</td><td class="table-cell">0087654322</td><td class="table-cell font-semibold">Nurul Hidayah</td><td class="table-cell">XII IPA 1</td><td class="table-cell">P</td><td class="table-cell">081323456789</td><td class="table-cell">081387654321</td><td class="table-cell-aksi"><div class="flex items-center justify-center gap-1"><button onclick="showDetail(1)" class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold border border-[#c3c6d1] text-[#43474f] hover:bg-[#edeeef] transition cursor-pointer"><i class="bi bi-eye"></i> Lihat</button><a href="{{ route('admin.siswa.edit') }}" class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold bg-blue-600 text-white hover:bg-blue-700 transition"><i class="bi bi-pencil"></i> Ubah</a><button onclick="alert('Hapus Nurul Hidayah?')" class="inline-flex items-center gap-1 px-1.5 py-1 rounded text-xs font-semibold bg-red-600 text-white hover:bg-red-700 transition cursor-pointer"><i class="bi bi-trash"></i> Hapus</button></div></td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- CSV Upload Modal -->
<div id="modalOverlay" class="hidden fixed inset-0 z-50 bg-black/40" onclick="closeModal()"></div>
<div id="modalBox" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-50 bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
    <div class="flex justify-between items-center mb-4"><h3 class="font-bold text-lg">Upload CSV Siswa</h3><button onclick="closeModal()" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg text-xl"></i></button></div>
    <p class="text-xs text-slate-500 mb-4">Upload file CSV dengan kolom: <strong>Nama, Jenis Kelamin, NIS, Kelas, NISN</strong></p>
    <div class="border-2 border-dashed border-slate-200 rounded-xl p-6 text-center mb-4">
        <i class="bi bi-cloud-upload text-3xl text-slate-300 mb-2 block"></i>
        <p class="text-sm text-slate-600">Klik atau drag file</p>
        <p class="text-xs text-slate-400">.csv (max 2MB)</p>
        <button class="btn-outline !py-2 !text-xs mt-3">Pilih File</button>
    </div>
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 text-xs text-blue-700">
        <strong>Template:</strong> "Nama","Jenis Kelamin","NIS","Kelas","NISN"<br>
        <strong>Contoh:</strong> "Ahmad Fauzi","Laki-laki","S001","XII IPA 1","0087654321"
    </div>
    <div class="flex gap-3 pt-2"><button type="button" onclick="closeModal()" class="btn-outline flex-1 !py-2.5">Batal</button><button type="button" onclick="alert('UI Only: CSV diupload!');closeModal()" class="btn-brand flex-1 !py-2.5">Upload</button></div>
</div>

<!-- Detail Modal -->
<div id="detailOverlay" class="hidden fixed inset-0 z-50 bg-black/40" onclick="closeDetail()"></div>
<div id="detailBox" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-50 bg-white rounded-2xl shadow-xl w-full max-w-lg p-6 max-h-[85vh] overflow-y-auto"></div>

@push('scripts')
<script>
const siswaD = [
    {n:'Ahmad Fauzi',nis:'S001',nisn:'0087654321',kelas:'XII IPA 1',jk:'Laki-laki',wa:'081312345678',wao:'081398765432',tmpt:'Tangerang',tgl:'17-08-2008',agama:'Islam',anak:'2',status:'Anak Kandung',alamat:'Jl. Merdeka No. 42, Kab. Tangerang',telp:'021-5551234',sekolah:'SMP Negeri 3',diterima:'15 Juli 2023',ayah:'H. Supriyanto, S.Pd.',pekAyah:'Guru PNS',waAyah:'081234567891',ibu:'Hj. Siti Rahmawati',pekIbu:'IRT',waIbu:'081398765432',ortu:'Jl. Merdeka No. 42',wali:'-',pekWali:'-',alamatWali:'-',telpWali:'-'},
    {n:'Nurul Hidayah',nis:'S002',nisn:'0087654322',kelas:'XII IPA 1',jk:'Perempuan',wa:'081323456789',wao:'081387654321',tmpt:'Jakarta',tgl:'22-04-2008',agama:'Islam',anak:'1',status:'Anak Kandung',alamat:'Jl. Melati No. 15, Jakarta',telp:'021-5555678',sekolah:'SMP Negeri 5',diterima:'15 Juli 2023',ayah:'Drs. H. Ahmad',pekAyah:'Wiraswasta',waAyah:'081234567892',ibu:'Dra. Hj. Fatimah',pekIbu:'Guru',waIbu:'081387654321',ortu:'Jl. Melati No. 15',wali:'-',pekWali:'-',alamatWali:'-',telpWali:'-'}
];

function showModal() {
    document.getElementById('modalOverlay').classList.remove('hidden');
    document.getElementById('modalBox').classList.remove('hidden');
}
function closeModal() {
    document.getElementById('modalOverlay').classList.add('hidden');
    document.getElementById('modalBox').classList.add('hidden');
}

function showDetail(i) {
    const d = siswaD[i];
    const box = document.getElementById('detailBox');
    box.innerHTML = `<div class="flex justify-between items-start mb-4"><div><h3 class="font-bold text-lg">${d.n}</h3><p class="text-sm text-muted">${d.kelas} &middot; NIS ${d.nis}</p></div><button onclick="closeDetail()" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg text-xl"></i></button></div>
    <div class="grid grid-cols-2 gap-3 text-sm">
        <div><span class="text-xs text-muted">NISN</span><p class="font-semibold">${d.nisn}</p></div>
        <div><span class="text-xs text-muted">JK</span><p class="font-semibold">${d.jk}</p></div>
        <div><span class="text-xs text-muted">Tempat Lahir</span><p class="font-semibold">${d.tmpt}</p></div>
        <div><span class="text-xs text-muted">Tgl Lahir</span><p class="font-semibold">${d.tgl}</p></div>
        <div><span class="text-xs text-muted">Agama</span><p class="font-semibold">${d.agama}</p></div>
        <div><span class="text-xs text-muted">Anak ke</span><p class="font-semibold">${d.anak}</p></div>
        <div class="col-span-2"><span class="text-xs text-muted">Alamat</span><p class="font-semibold">${d.alamat}</p></div>
        <div><span class="text-xs text-muted">Telp Siswa</span><p class="font-semibold">${d.wa}</p></div>
        <div><span class="text-xs text-muted">Telp Ortu</span><p class="font-semibold">${d.wao}</p></div>
    </div>
    <hr class="my-3">
    <p class="text-xs font-bold text-purple-600 uppercase mb-2">Orang Tua</p>
    <div class="grid grid-cols-2 gap-2 text-sm">
        <div><span class="text-xs text-muted">Ayah</span><p class="font-semibold">${d.ayah}</p><p class="text-xs text-muted">${d.pekAyah}</p></div>
        <div><span class="text-xs text-muted">Ibu</span><p class="font-semibold">${d.ibu}</p><p class="text-xs text-muted">${d.pekIbu}</p></div>
    </div>
    <hr class="my-3">
    <p class="text-xs font-bold text-amber-600 uppercase mb-2">Wali</p>
    <p class="text-sm italic text-muted">${d.wali === '-' ? 'Tidak ada wali' : d.wali}</p>
    <button onclick="closeDetail()" class="btn-outline w-full !py-2 mt-4">Tutup</button>`;
    document.getElementById('detailOverlay').classList.remove('hidden');
    box.classList.remove('hidden');
}
function closeDetail() {
    document.getElementById('detailOverlay').classList.add('hidden');
    document.getElementById('detailBox').classList.add('hidden');
}
</script>
@endpush
@endsection