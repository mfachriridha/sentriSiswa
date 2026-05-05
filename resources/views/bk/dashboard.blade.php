@extends('layouts.app')

@section('title', 'Dashboard Guru BK')

@section('sidebar-menu')
<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-blue-600 text-white font-medium transition">
    <i class="bi bi-grid-1x2-fill"></i> Dashboard
</a>

<div class="pt-4 pb-2 text-xs font-semibold text-slate-500 uppercase px-4">Monitoring</div>

<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
    <i class="bi bi-eye-fill"></i> Monitoring Semua Kelas
</a>

<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
    <i class="bi bi-bar-chart-fill"></i> Rekap Absensi
</a>

<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
    <i class="bi bi-exclamation-triangle-fill"></i> Poin Pelanggaran
</a>

<div class="pt-4 pb-2 text-xs font-semibold text-slate-500 uppercase px-4">Wali Kelas (Jika Di-assign)</div>

<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
    <i class="bi bi-camera-fill"></i> Absensi Kelas
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
<!-- Header Info -->
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">Monitoring Absensi - Guru BK</h1>
    <p class="text-gray-600 mt-1">Rabu, 5 Mei 2026 | Pantau absensi semua kelas</p>
</div>

<!-- Stats -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="card !p-4">
        <p class="text-sm text-gray-600">Total Siswa</p>
        <p class="text-2xl font-bold text-gray-900">360</p>
    </div>
    <div class="card !p-4">
        <p class="text-sm text-gray-600">Hadir Hari Ini</p>
        <p class="text-2xl font-bold text-green-600">342</p>
    </div>
    <div class="card !p-4">
        <p class="text-sm text-gray-600">Terlambat</p>
        <p class="text-2xl font-bold text-yellow-600">12</p>
    </div>
    <div class="card !p-4">
        <p class="text-sm text-gray-600">Belum Absen</p>
        <p class="text-2xl font-bold text-red-600">6</p>
    </div>
</div>

<!-- Filter & Rekap -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="card lg:col-span-2">
        <h3 class="text-lg font-bold mb-4">Rekap Absensi per Kelas</h3>

        <div class="mb-4 flex gap-2">
            <select class="input-field !w-auto">
                <option>Semua Tingkat</option>
                <option>Kelas X</option>
                <option>Kelas XI</option>
                <option>Kelas XII</option>
            </select>
            <select class="input-field !w-auto">
                <option>Semua Status</option>
                <option>Hadir 100%</option>
                <option>Ada yang Terlambat</option>
                <option>Belum Lengkap</option>
            </select>
        </div>

        <div class="table-container">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="table-header">Kelas</th>
                        <th class="table-header">Wali Kelas</th>
                        <th class="table-header">Hadir</th>
                        <th class="table-header">Terlambat</th>
                        <th class="table-header">Belum</th>
                        <th class="table-header">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr class="hover:bg-gray-50">
                        <td class="table-cell font-medium">XII IPA 1</td>
                        <td class="table-cell">Budi Santoso, S.Pd</td>
                        <td class="table-cell"><span class="badge-success">28</span></td>
                        <td class="table-cell"><span class="badge-warning">2</span></td>
                        <td class="table-cell"><span class="badge-danger">0</span></td>
                        <td class="table-cell">
                            <button class="text-blue-600 hover:text-blue-800 text-sm">
                                <i class="bi bi-eye"></i> Detail
                            </button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="table-cell font-medium">XII IPA 2</td>
                        <td class="table-cell">Siti Nurhaliza, M.Pd</td>
                        <td class="table-cell"><span class="badge-success">27</span></td>
                        <td class="table-cell"><span class="badge-warning">3</span></td>
                        <td class="table-cell"><span class="badge-danger">2</span></td>
                        <td class="table-cell">
                            <button class="text-blue-600 hover:text-blue-800 text-sm">
                                <i class="bi bi-eye"></i> Detail
                            </button>
                        </td>
                    </tr>
                    <tr class="hover:bg-gray-50">
                        <td class="table-cell font-medium">XI IPA 1</td>
                        <td class="table-cell">Ahmad Fauzi, S.Pd</td>
                        <td class="table-cell"><span class="badge-success">30</span></td>
                        <td class="table-cell"><span class="badge-warning">0</span></td>
                        <td class="table-cell"><span class="badge-danger">0</span></td>
                        <td class="table-cell">
                            <button class="text-blue-600 hover:text-blue-800 text-sm">
                                <i class="bi bi-eye"></i> Detail
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Poin Pelanggaran Summary -->
    <div class="card">
        <h3 class="text-lg font-bold mb-4">Poin Pelanggaran</h3>
        <div class="space-y-4">
            <div class="p-3 bg-red-50 rounded-lg">
                <p class="text-sm font-medium text-red-800">Poin Rendah (&lt;50)</p>
                <p class="text-2xl font-bold text-red-600 mt-1">5 Siswa</p>
            </div>
            <div class="p-3 bg-yellow-50 rounded-lg">
                <p class="text-sm font-medium text-yellow-800">Poin Sedang (50-80)</p>
                <p class="text-2xl font-bold text-yellow-600 mt-1">12 Siswa</p>
            </div>
            <div class="p-3 bg-green-50 rounded-lg">
                <p class="text-sm font-medium text-green-800">Poin Baik (&gt;80)</p>
                <p class="text-2xl font-bold text-green-600 mt-1">343 Siswa</p>
            </div>
            <button onclick="showModal('addViolationModal')" class="btn-danger w-full py-2 text-sm">
                <i class="bi bi-exclamation-triangle"></i> Tambah Pelanggaran
            </button>
        </div>
    </div>
</div>

<!-- Recent Violations -->
<div class="card">
    <h3 class="text-lg font-bold mb-4">Pelanggaran Terbaru</h3>
    <div class="space-y-3">
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="bi bi-exclamation-triangle text-red-600"></i>
                </div>
                <div>
                    <p class="text-sm font-medium">Ahmad Fauzi - Bolos</p>
                    <p class="text-xs text-gray-500">-10 poin | BK: Siti Nurhaliza</p>
                </div>
            </div>
            <span class="badge-danger">-10</span>
        </div>
        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="bi bi-exclamation-circle text-yellow-600"></i>
                </div>
                <div>
                    <p class="text-sm font-medium">Nurul Hidayah - Terlambat</p>
                    <p class="text-xs text-gray-500">-5 poin | BK: Siti Nurhaliza</p>
                </div>
            </div>
            <span class="badge-warning">-5</span>
        </div>
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

<!-- Add Violation Modal -->
<div id="addViolationModal" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-2xl z-50 w-full max-w-md p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold">Tambah Pelanggaran</h3>
        <button onclick="hideModal('addViolationModal')" class="text-gray-500 hover:text-gray-700">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <form class="space-y-4">
        <div>
            <label class="block text-sm font-medium mb-1">Siswa</label>
            <select class="input-field">
                <option>Pilih Siswa</option>
                <option>Ahmad Fauzi (S001)</option>
                <option>Nurul Hidayah (S002)</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Jenis Pelanggaran</label>
            <select class="input-field">
                <option>Pilih Pelanggaran</option>
                <option>Bolos (-10 poin)</option>
                <option>Terlambat (-5 poin)</option>
                <option>Tidak Memakai Seragam (-5 poin)</option>
                <option>Merokok (-20 poin)</option>
                <option>Lainnya</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1">Keterangan</label>
            <textarea class="input-field" rows="3" placeholder="Detail pelanggaran"></textarea>
        </div>
        <div class="bg-yellow-50 p-3 rounded-lg">
            <p class="text-sm text-yellow-800">
                <i class="bi bi-info-circle"></i> Poin siswa akan otomatis berkurang. Notifikasi akan dikirim ke orang tua via WhatsApp setiap minggu.
            </p>
        </div>
        <div class="flex gap-3 pt-4">
            <button type="button" onclick="hideModal('addViolationModal')" class="btn-secondary flex-1">Batal</button>
            <button type="button" onclick="alert('UI Only: Pelanggaran berhasil ditambahkan!'); hideModal('addViolationModal')" class="btn-primary flex-1">Simpan</button>
        </div>
    </form>
</div>
