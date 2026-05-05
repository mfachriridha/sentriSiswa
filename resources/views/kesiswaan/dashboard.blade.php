@extends('layouts.app')

@section('title', 'Dashboard Kesiswaan')

@section('sidebar-menu')
<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-blue-600 text-white font-medium transition">
    <i class="bi bi-grid-1x2-fill"></i> Dashboard
</a>

<div class="pt-4 pb-2 text-xs font-semibold text-slate-500 uppercase px-4">Master Data</div>

<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
    <i class="bi bi-people-fill"></i> Data Guru
</a>

<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
    <i class="bi bi-mortarboard-fill"></i> Data Siswa
</a>

<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
    <i class="bi bi-diagram-3-fill"></i> Data Kelas
</a>

<div class="pt-4 pb-2 text-xs font-semibold text-slate-500 uppercase px-4">Manajemen</div>

<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
    <i class="bi bi-person-gear"></i> Manajemen Role
</a>

<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
    <i class="bi bi-file-earmark-pdf-fill"></i> Upload Tata Tertib
</a>

<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
    <i class="bi bi-exclamation-triangle-fill"></i> Poin Pelanggaran
</a>

<div class="pt-4 pb-2 text-xs font-semibold text-slate-500 uppercase px-4">Lainnya</div>

<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
    <i class="bi bi-person-circle"></i> Profil
</a>

<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
    <i class="bi bi-box-arrow-right"></i> Logout
</a>
@endsection

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Total Guru</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">42</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="bi bi-people-fill text-2xl text-blue-600"></i>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Total Siswa</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">360</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <i class="bi bi-mortarboard-fill text-2xl text-green-600"></i>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Total Kelas</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">12</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <i class="bi bi-diagram-3-fill text-2xl text-purple-600"></i>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Hadir Hari Ini</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">342</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                <i class="bi bi-check-circle-fill text-2xl text-yellow-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="card lg:col-span-2">
        <h3 class="text-lg font-bold mb-4">Aksi Cepat</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <button onclick="showModal('addGuruModal')" class="flex flex-col items-center gap-2 p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                <i class="bi bi-person-plus text-2xl text-blue-600"></i>
                <span class="text-sm font-medium">Tambah Guru</span>
            </button>
            <button onclick="showModal('addSiswaModal')" class="flex flex-col items-center gap-2 p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                <i class="bi bi-person-plus text-2xl text-green-600"></i>
                <span class="text-sm font-medium">Tambah Siswa</span>
            </button>
            <button onclick="showModal('addClassModal')" class="flex flex-col items-center gap-2 p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                <i class="bi bi-plus-circle text-2xl text-purple-600"></i>
                <span class="text-sm font-medium">Tambah Kelas</span>
            </button>
            <button onclick="showModal('uploadCSVModal')" class="flex flex-col items-center gap-2 p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                <i class="bi bi-upload text-2xl text-orange-600"></i>
                <span class="text-sm font-medium">Upload CSV</span>
            </button>
        </div>
    </div>

    <div class="card">
        <h3 class="text-lg font-bold mb-4">Upload Tata Tertib</h3>
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
            <i class="bi bi-file-earmark-pdf text-4xl text-red-400 mb-3"></i>
            <p class="text-sm text-gray-600 mb-3">Belum ada file PDF</p>
            <button class="btn-primary text-sm py-2">
                <i class="bi bi-upload"></i> Pilih PDF
            </button>
        </div>
    </div>
</div>

<!-- Recent Activities -->
<div class="card mb-8">
    <h3 class="text-lg font-bold mb-4">Aktivitas Terbaru</h3>
    <div class="space-y-4">
        <div class="flex items-center gap-4 p-3 hover:bg-gray-50 rounded-lg">
            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                <i class="bi bi-check-circle text-green-600"></i>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium">Absensi pagi selesai - Kelas XII IPA 1</p>
                <p class="text-xs text-gray-500">Wali Kelas: Budi Santoso, S.Pd • 07:30</p>
            </div>
            <span class="badge-success">Selesai</span>
        </div>
        <div class="flex items-center gap-4 p-3 hover:bg-gray-50 rounded-lg">
            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                <i class="bi bi-exclamation-triangle text-red-600"></i>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium">Poin pelanggaran: Ahmad Fauzi (-10 poin)</p>
                <p class="text-xs text-gray-500">Kesiswaan • 08:15</p>
            </div>
            <span class="badge-danger">Pelanggaran</span>
        </div>
        <div class="flex items-center gap-4 p-3 hover:bg-gray-50 rounded-lg">
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="bi bi-person-plus text-blue-600"></i>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium">Guru baru ditambahkan: Siti Nurhaliza, M.Pd</p>
                <p class="text-xs text-gray-500">Kesiswaan • 09:00</p>
            </div>
            <span class="badge-info">Data Baru</span>
        </div>
    </div>
</div>

<!-- Data Guru Table (Preview) -->
<div class="card">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h3 class="text-lg font-bold">Data Guru (Preview)</h3>
            <p class="text-sm text-gray-600">Kelola data master guru</p>
        </div>
        <button onclick="showModal('addGuruModal')" class="btn-primary inline-flex items-center gap-2">
            <i class="bi bi-plus-lg"></i> Tambah Guru
        </button>
    </div>

    <div class="table-container">
        <table class="w-full">
            <thead>
                <tr>
                    <th class="table-header">NIP</th>
                    <th class="table-header">Nama</th>
                    <th class="table-header">Role</th>
                    <th class="table-header">Kelas Diampu</th>
                    <th class="table-header">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <tr>
                    <td class="table-cell">1234567890</td>
                    <td class="table-cell font-medium">Budi Santoso, S.Pd</td>
                    <td class="table-cell">
                        <span class="badge-info">Wali Kelas</span>
                        <span class="badge-success">Guru Mapel</span>
                    </td>
                    <td class="table-cell">XII IPA 1</td>
                    <td class="table-cell">
                        <button class="text-blue-600 hover:text-blue-800 mr-3"><i class="bi bi-pencil"></i></button>
                        <button class="text-red-600 hover:text-red-800"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
                <tr>
                    <td class="table-cell">0987654321</td>
                    <td class="table-cell font-medium">Siti Nurhaliza, M.Pd</td>
                    <td class="table-cell">
                        <span class="badge-danger">Guru BK</span>
                        <span class="badge-success">Guru Mapel</span>
                    </td>
                    <td class="table-cell">-</td>
                    <td class="table-cell">
                        <button class="text-blue-600 hover:text-blue-800 mr-3"><i class="bi bi-pencil"></i></button>
                        <button class="text-red-600 hover:text-red-800"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
        document.getElementById('modal-overlay').classList.remove('hidden');
    }

    function hideModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
        document.getElementById('modal-overlay').classList.add('hidden');
    }
</script>
@endpush

<!-- Modal Overlay -->
<div id="modal-overlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50"></div>

<!-- Add Guru Modal -->
<div id="addGuruModal" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-2xl z-50 w-full max-w-md p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold">Tambah Guru</h3>
        <button onclick="hideModal('addGuruModal')" class="text-gray-500 hover:text-gray-700">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <form class="space-y-4">
        <div>
            <label class="block text-sm font-medium mb-1">NIP</label>
            <input type="text" class="input-field" placeholder="Masukkan NIP">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
            <input type="text" class="input-field" placeholder="Masukkan nama">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Role</label>
            <div class="space-y-2">
                <label class="flex items-center gap-2">
                    <input type="checkbox" class="rounded text-blue-600"> Wali Kelas
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" class="rounded text-blue-600"> Guru Mapel
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" class="rounded text-blue-600"> Guru BK
                </label>
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Kelas Diampu (jika Wali Kelas)</label>
            <select class="input-field">
                <option>Pilih Kelas</option>
                <option>X IPA 1</option>
                <option>X IPA 2</option>
                <option>XI IPA 1</option>
            </select>
        </div>
        <div class="flex gap-3 pt-4">
            <button type="button" onclick="hideModal('addGuruModal')" class="btn-secondary flex-1">Batal</button>
            <button type="button" onclick="alert('UI Only: Guru berhasil ditambahkan!'); hideModal('addGuruModal')" class="btn-primary flex-1">Simpan</button>
        </div>
    </form>
</div>

<!-- Add Siswa Modal -->
<div id="addSiswaModal" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-2xl z-50 w-full max-w-md p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold">Tambah Siswa</h3>
        <button onclick="hideModal('addSiswaModal')" class="text-gray-500 hover:text-gray-700">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <form class="space-y-4">
        <div>
            <label class="block text-sm font-medium mb-1">NIS/NISN</label>
            <input type="text" class="input-field" placeholder="Masukkan NIS/NISN">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Nama Lengkap</label>
            <input type="text" class="input-field" placeholder="Masukkan nama">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Kelas</label>
            <select class="input-field">
                <option>Pilih Kelas</option>
                <option>X IPA 1</option>
                <option>X IPA 2</option>
                <option>XI IPA 1</option>
            </select>
        </div>
        <div class="flex gap-3 pt-4">
            <button type="button" onclick="hideModal('addSiswaModal')" class="btn-secondary flex-1">Batal</button>
            <button type="button" onclick="alert('UI Only: Siswa berhasil ditambahkan!'); hideModal('addSiswaModal')" class="btn-primary flex-1">Simpan</button>
        </div>
    </form>
</div>

<!-- Add Class Modal -->
<div id="addClassModal" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-2xl z-50 w-full max-w-md p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold">Tambah Kelas</h3>
        <button onclick="hideModal('addClassModal')" class="text-gray-500 hover:text-gray-700">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <form class="space-y-4">
        <div>
            <label class="block text-sm font-medium mb-1">Nama Kelas</label>
            <input type="text" class="input-field" placeholder="Contoh: XII IPA 1">
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Wali Kelas</label>
            <select class="input-field">
                <option>Pilih Wali Kelas</option>
                <option>Budi Santoso, S.Pd</option>
                <option>Siti Nurhaliza, M.Pd</option>
            </select>
        </div>
        <div class="flex gap-3 pt-4">
            <button type="button" onclick="hideModal('addClassModal')" class="btn-secondary flex-1">Batal</button>
            <button type="button" onclick="alert('UI Only: Kelas berhasil ditambahkan!'); hideModal('addClassModal')" class="btn-primary flex-1">Simpan</button>
        </div>
    </form>
</div>

<!-- Upload CSV Modal -->
<div id="uploadCSVModal" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-2xl z-50 w-full max-w-lg p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold">Upload CSV/Excel</h3>
        <button onclick="hideModal('uploadCSVModal')" class="text-gray-500 hover:text-gray-700">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <div class="space-y-4">
        <div>
            <label class="block text-sm font-medium mb-2">Pilih Tipe Data</label>
            <div class="flex gap-4">
                <label class="flex-1">
                    <input type="radio" name="dataType" value="guru" class="peer hidden" checked>
                    <div class="border-2 border-gray-300 rounded-lg p-3 text-center cursor-pointer peer-checked:border-blue-600 peer-checked:bg-blue-50">
                        <i class="bi bi-person-badge text-xl"></i>
                        <p class="text-sm font-medium mt-1">Guru</p>
                    </div>
                </label>
                <label class="flex-1">
                    <input type="radio" name="dataType" value="siswa" class="peer hidden">
                    <div class="border-2 border-gray-300 rounded-lg p-3 text-center cursor-pointer peer-checked:border-blue-600 peer-checked:bg-blue-50">
                        <i class="bi bi-mortarboard text-xl"></i>
                        <p class="text-sm font-medium mt-1">Siswa</p>
                    </div>
                </label>
            </div>
        </div>
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
            <i class="bi bi-cloud-upload text-4xl text-gray-400 mb-3"></i>
            <p class="text-sm text-gray-600 mb-2">Drag & drop file atau klik untuk browse</p>
            <p class="text-xs text-gray-500">Format: .csv, .xlsx (Maks. 5MB)</p>
            <button type="button" class="btn-secondary mt-3 text-sm py-2">Pilih File</button>
        </div>
        <div class="bg-blue-50 p-4 rounded-lg">
            <p class="text-sm font-medium text-blue-800 mb-2">Format CSV Guru:</p>
            <p class="text-xs text-blue-700">Nama, NIP</p>
            <p class="text-sm font-medium text-blue-800 mt-3 mb-2">Format CSV Siswa:</p>
            <p class="text-xs text-blue-700">Nama, NIS/NISN, Kelas (opsional)</p>
        </div>
        <div class="flex gap-3 pt-4">
            <button type="button" onclick="hideModal('uploadCSVModal')" class="btn-secondary flex-1">Batal</button>
            <button type="button" onclick="alert('UI Only: File berhasil diupload!'); hideModal('uploadCSVModal')" class="btn-primary flex-1">Upload</button>
        </div>
    </div>
</div>
