@extends('layouts.app')

@section('title', 'Manajemen Siswa - Admin')

@section('page-title', 'Manajemen Siswa')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div class="flex gap-2">
        <button onclick="showModal('tambah')" class="btn-brand !text-xs"><i class="bi bi-plus-lg"></i> Tambah Siswa</button>
        <button onclick="showModal('csv')" class="btn-outline !text-xs"><i class="bi bi-upload"></i> Upload CSV</button>
        <a href="#" onclick="alert('UI Only: Download template CSV')" class="btn-outline !text-xs"><i class="bi bi-download"></i> Template CSV</a>
    </div>
</div>

<div class="card anim-up">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
        <h3 class="font-bold text-slate-900">Data Siswa</h3>
        <input type="text" class="input-field !w-48 !py-1.5 !text-xs" placeholder="Cari nama / NIS...">
    </div>
    <div class="table-container">
        <table class="w-full">
            <thead><tr><th class="table-header">NIS</th><th class="table-header">NISN</th><th class="table-header">Nama</th><th class="table-header">Kelas</th><th class="table-header">JK</th><th class="table-header">WA</th><th class="table-header">Aksi</th></tr></thead>
            <tbody>
                <tr><td class="table-cell">S001</td><td class="table-cell">0087654321</td><td class="table-cell font-semibold"><button onclick="showModal('detail',0)" class="text-blue-600 hover:underline">Ahmad Fauzi</button></td><td class="table-cell">XII IPA 1</td><td class="table-cell">L</td><td class="table-cell">081312345678</td><td class="table-cell"><button onclick="showModal('edit',0)" class="text-blue-600 hover:text-blue-800 mr-2"><i class="bi bi-pencil"></i></button><button onclick="alert('Hapus?')" class="text-red-500 hover:text-red-700"><i class="bi bi-trash"></i></button></td></tr>
                <tr><td class="table-cell">S002</td><td class="table-cell">0087654322</td><td class="table-cell font-semibold"><button onclick="showModal('detail',1)" class="text-blue-600 hover:underline">Nurul Hidayah</button></td><td class="table-cell">XII IPA 1</td><td class="table-cell">P</td><td class="table-cell">081323456789</td><td class="table-cell"><button onclick="showModal('edit',1)" class="text-blue-600 hover:text-blue-800 mr-2"><i class="bi bi-pencil"></i></button><button onclick="alert('Hapus?')" class="text-red-500 hover:text-red-700"><i class="bi bi-trash"></i></button></td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Overlay -->
<div id="modalOverlay" class="hidden fixed inset-0 z-50 bg-black/40" onclick="closeModal()"></div>
<div id="modalBox" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-50 bg-white rounded-2xl shadow-xl w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto"></div>

@push('scripts')
<script>
const siswaData = [
    { nama:'Ahmad Fauzi', nis:'S001', nisn:'0087654321', kelas:'XII IPA 1', jk:'Laki-laki', wa:'081312345678', waortu:'081398765432',
      tmpt:'Tangerang', tgl:'17-08-2008', agama:'Islam', status:'Anak Kandung', anakke:'2', alamat:'Jl. Merdeka No. 42, Kab. Tangerang',
      telp:'021-5551234', asal:'SMP Negeri 3', diterima:'15 Juli 2023',
      ayah:'H. Supriyanto, S.Pd.', pekAyah:'Guru PNS', waAyah:'081234567891',
      ibu:'Hj. Siti Rahmawati', pekIbu:'IRT', waIbu:'081398765432', alamatOrtu:'Jl. Merdeka No. 42',
      wali:'-', alamatWali:'-', telpWali:'-', pekWali:'-' },
    { nama:'Nurul Hidayah', nis:'S002', nisn:'0087654322', kelas:'XII IPA 1', jk:'Perempuan', wa:'081323456789', waortu:'081387654321',
      tmpt:'Jakarta', tgl:'22-04-2008', agama:'Islam', status:'Anak Kandung', anakke:'1', alamat:'Jl. Melati No. 15, Jakarta',
      telp:'021-5555678', asal:'SMP Negeri 5', diterima:'15 Juli 2023',
      ayah:'Drs. H. Ahmad', pekAyah:'Wiraswasta', waAyah:'081234567892',
      ibu:'Dra. Hj. Fatimah', pekIbu:'Guru', waIbu:'081387654321', alamatOrtu:'Jl. Melati No. 15',
      wali:'-', alamatWali:'-', telpWali:'-', pekWali:'-' }
];

function showModal(type, idx) {
    const box = document.getElementById('modalBox');
    const d = siswaData[idx] || siswaData[0];
    let html = '';
    if (type === 'tambah' || type === 'edit') {
        html = `<div class="flex justify-between items-center mb-4"><h3 class="font-bold text-lg">${type==='tambah'?'Tambah':'Edit'} Siswa</h3><button onclick="closeModal()" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg text-xl"></i></button></div>
        <form class="space-y-3">
            <div class="grid grid-cols-2 gap-3">
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">NIS <span class="text-red-500">*</span></label><input type="text" class="input-field" value="${d.nis}"></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">NISN</label><input type="text" class="input-field" value="${d.nisn}"></div>
            </div>
            <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Nama Lengkap <span class="text-red-500">*</span></label><input type="text" class="input-field" value="${d.nama}"></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Jenis Kelamin</label><select class="input-field"><option ${d.jk==='Laki-laki'?'selected':''}>Laki-laki</option><option ${d.jk==='Perempuan'?'selected':''}>Perempuan</option></select></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Kelas</label><input type="text" class="input-field" value="${d.kelas}"></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Tempat Lahir</label><input type="text" class="input-field" value="${d.tmpt}"></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Tanggal Lahir</label><input type="text" class="input-field" value="${d.tgl}"></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Agama</label><input type="text" class="input-field" value="${d.agama}"></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Anak ke</label><input type="text" class="input-field" value="${d.anakke}"></div>
            </div>
            <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Status Keluarga</label><input type="text" class="input-field" value="${d.status}"></div>
            <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Alamat</label><textarea class="input-field" rows="2">${d.alamat}</textarea></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Telepon</label><input type="text" class="input-field" value="${d.telp}"></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Sekolah Asal</label><input type="text" class="input-field" value="${d.asal}"></div>
            </div>
            <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block">Diterima</label><input type="text" class="input-field" value="${d.diterima}"></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block"><i class="bi bi-whatsapp text-emerald-600"></i> WA Siswa</label><input type="tel" class="input-field" value="${d.wa}"></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1 block"><i class="bi bi-whatsapp text-emerald-600"></i> WA Orang Tua</label><input type="tel" class="input-field" value="${d.waortu}"></div>
            </div>
            <hr class="border-slate-200 my-2">
            <p class="text-xs font-bold text-purple-600 uppercase tracking-wider mb-2">Orang Tua</p>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="text-xs font-bold text-slate-500 uppercase mb-1 block">Ayah</label><input type="text" class="input-field" value="${d.ayah}"></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase mb-1 block">Pekerjaan Ayah</label><input type="text" class="input-field" value="${d.pekAyah}"></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="text-xs font-bold text-slate-500 uppercase mb-1 block">WA Ayah</label><input type="tel" class="input-field" value="${d.waAyah}"></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase mb-1 block">Ibu</label><input type="text" class="input-field" value="${d.ibu}"></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="text-xs font-bold text-slate-500 uppercase mb-1 block">Pekerjaan Ibu</label><input type="text" class="input-field" value="${d.pekIbu}"></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase mb-1 block">WA Ibu</label><input type="tel" class="input-field" value="${d.waIbu}"></div>
            </div>
            <div><label class="text-xs font-bold text-slate-500 uppercase mb-1 block">Alamat Orang Tua</label><input type="text" class="input-field" value="${d.alamatOrtu}"></div>
            <hr class="border-slate-200 my-2">
            <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-2">Wali</p>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="text-xs font-bold text-slate-500 uppercase mb-1 block">Nama Wali</label><input type="text" class="input-field" value="${d.wali}"></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase mb-1 block">Pekerjaan Wali</label><input type="text" class="input-field" value="${d.pekWali}"></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="text-xs font-bold text-slate-500 uppercase mb-1 block">Alamat Wali</label><input type="text" class="input-field" value="${d.alamatWali}"></div>
                <div><label class="text-xs font-bold text-slate-500 uppercase mb-1 block">Telp Wali</label><input type="text" class="input-field" value="${d.telpWali}"></div>
            </div>
            <div class="flex gap-3 pt-2"><button type="button" onclick="closeModal()" class="btn-outline flex-1 !py-2.5">Batal</button><button type="button" onclick="alert('UI Only: Data tersimpan!');closeModal()" class="btn-brand flex-1 !py-2.5">Simpan</button></div>
        </form>`;
    } else if (type === 'csv') {
        html = `<div class="flex justify-between items-center mb-4"><h3 class="font-bold text-lg">Upload CSV Siswa</h3><button onclick="closeModal()" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg text-xl"></i></button></div>
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
        <div class="flex gap-3 pt-2"><button type="button" onclick="closeModal()" class="btn-outline flex-1 !py-2.5">Batal</button><button type="button" onclick="alert('UI Only: CSV diupload!');closeModal()" class="btn-brand flex-1 !py-2.5">Upload</button></div>`;
    } else if (type === 'detail') {
        html = `<div class="flex justify-between items-center mb-4"><h3 class="font-bold text-lg">Detail Siswa</h3><button onclick="closeModal()" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg text-xl"></i></button></div>
        <div class="space-y-4">
            <div class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center"><i class="bi bi-person-fill text-3xl text-blue-500"></i></div>
                <div><p class="font-bold text-lg">${d.nama}</p><p class="text-sm text-slate-500">${d.kelas} &middot; NIS ${d.nis}</p></div>
            </div>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div><span class="text-slate-500">NISN</span><p class="font-semibold">${d.nisn}</p></div>
                <div><span class="text-slate-500">JK</span><p class="font-semibold">${d.jk}</p></div>
                <div><span class="text-slate-500">Tempat Lahir</span><p class="font-semibold">${d.tmpt}</p></div>
                <div><span class="text-slate-500">Tgl Lahir</span><p class="font-semibold">${d.tgl}</p></div>
                <div><span class="text-slate-500">Agama</span><p class="font-semibold">${d.agama}</p></div>
                <div><span class="text-slate-500">Anak ke</span><p class="font-semibold">${d.anakke}</p></div>
                <div class="col-span-2"><span class="text-slate-500">Alamat</span><p class="font-semibold">${d.alamat}</p></div>
                <div><span class="text-slate-500">WA Siswa</span><p class="font-semibold text-emerald-600"><i class="bi bi-whatsapp"></i> ${d.wa}</p></div>
                <div><span class="text-slate-500">WA Ortu</span><p class="font-semibold text-emerald-600"><i class="bi bi-whatsapp"></i> ${d.waortu}</p></div>
            </div>
            <hr>
            <p class="font-bold text-sm text-purple-600">Orang Tua</p>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div><span class="text-slate-500">Ayah</span><p class="font-semibold">${d.ayah}</p><p class="text-xs text-slate-400">${d.pekAyah}</p></div>
                <div><span class="text-slate-500">Ibu</span><p class="font-semibold">${d.ibu}</p><p class="text-xs text-slate-400">${d.pekIbu}</p></div>
            </div>
            <hr>
            <p class="font-bold text-sm text-amber-600">Wali</p>
            <p class="text-sm text-slate-400 italic">${d.wali === '-' ? 'Tidak ada wali' : d.wali}</p>
            <button onclick="closeModal()" class="btn-outline w-full !py-2.5">Tutup</button>
        </div>`;
    }
    box.innerHTML = html;
    document.getElementById('modalOverlay').classList.remove('hidden');
    box.classList.remove('hidden');
}
function closeModal() {
    document.getElementById('modalOverlay').classList.add('hidden');
    document.getElementById('modalBox').classList.add('hidden');
}
</script>
@endpush
@endsection