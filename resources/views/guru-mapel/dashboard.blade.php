@extends('layouts.app')

@section('title', 'Dashboard Guru Mapel')

@section('sidebar-menu')
<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-blue-600 text-white font-medium transition">
    <i class="bi bi-grid-1x2-fill"></i> Dashboard
</a>

<div class="pt-4 pb-2 text-xs font-semibold text-slate-500 uppercase px-4">Absensi</div>

<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
    <i class="bi bi-calendar-check-fill"></i> Absensi Kelas
</a>

<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
    <i class="bi bi-clock-history"></i> Riwayat Absensi
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
    <h1 class="text-2xl font-bold text-gray-900">Absensi Kelas - XII IPA 1</h1>
    <p class="text-gray-600 mt-1">Rabu, 5 Mei 2026 | Guru Mapel: Siti Nurhaliza, M.Pd</p>
</div>

<!-- Stats -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="card !p-4">
        <p class="text-sm text-gray-600">Total Siswa</p>
        <p class="text-2xl font-bold text-gray-900">30</p>
    </div>
    <div class="card !p-4">
        <p class="text-sm text-gray-600">Hadir</p>
        <p class="text-2xl font-bold text-green-600">28</p>
    </div>
    <div class="card !p-4">
        <p class="text-sm text-gray-600">Izin/Sakit</p>
        <p class="text-2xl font-bold text-yellow-600">2</p>
    </div>
    <div class="card !p-4">
        <p class="text-sm text-gray-600">Belum Absen</p>
        <p class="text-2xl font-bold text-red-600">0</p>
    </div>
</div>

<!-- Attendance Table -->
<div class="card">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h3 class="text-lg font-bold">Daftar Kehadiran Siswa</h3>
            <p class="text-sm text-gray-600">Data absensi pagi (tanpa selfie)</p>
        </div>
        <div class="flex gap-2">
            <select class="input-field !w-auto">
                <option>Semua Status</option>
                <option>Hadir</option>
                <option>Izin</option>
                <option>Sakit</option>
            </select>
            <button onclick="showModal('saveAttendanceModal')" class="btn-primary inline-flex items-center gap-2">
                <i class="bi bi-check-circle"></i> Simpan
            </button>
        </div>
    </div>

    <div class="table-container">
        <table class="w-full">
            <thead>
                <tr>
                    <th class="table-header">No</th>
                    <th class="table-header">Nama Siswa</th>
                    <th class="table-header">NIS</th>
                    <th class="table-header">Status Kehadiran</th>
                    <th class="table-header">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <tr class="hover:bg-gray-50">
                    <td class="table-cell">1</td>
                    <td class="table-cell font-medium">Ahmad Fauzi</td>
                    <td class="table-cell">S001</td>
                    <td class="table-cell">
                        <select class="input-field !py-1.5 !px-2 text-sm">
                            <option>Hadir</option>
                            <option>Izin</option>
                            <option>Sakit</option>
                            <option>Alpha</option>
                        </select>
                    </td>
                    <td class="table-cell">
                        <input type="text" class="input-field !py-1.5 text-sm" placeholder="Opsional">
                    </td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="table-cell">2</td>
                    <td class="table-cell font-medium">Nurul Hidayah</td>
                    <td class="table-cell">S002</td>
                    <td class="table-cell">
                        <select class="input-field !py-1.5 !px-2 text-sm">
                            <option>Izin</option>
                            <option>Hadir</option>
                            <option>Sakit</option>
                            <option>Alpha</option>
                        </select>
                    </td>
                    <td class="table-cell">
                        <input type="text" value="Sakit flu" class="input-field !py-1.5 text-sm">
                    </td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="table-cell">3</td>
                    <td class="table-cell font-medium">Budi Darmawan</td>
                    <td class="table-cell">S003</td>
                    <td class="table-cell">
                        <select class="input-field !py-1.5 !px-2 text-sm">
                            <option>Hadir</option>
                            <option>Izin</option>
                            <option>Sakit</option>
                            <option>Alpha</option>
                        </select>
                    </td>
                    <td class="table-cell">
                        <input type="text" class="input-field !py-1.5 text-sm" placeholder="Opsional">
                    </td>
                </tr>
                <!-- More rows... -->
            </tbody>
        </table>
    </div>

    <div class="mt-4 text-sm text-gray-500">
        <i class="bi bi-info-circle"></i> Data absensi diambil dari absensi pagi (wali kelas). Guru mapel hanya perlu menandai kehadiran.
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

<!-- Save Attendance Modal -->
<div id="saveAttendanceModal" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-2xl z-50 w-full max-w-md p-6">
    <div class="text-center">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="bi bi-check-circle-fill text-5xl text-green-600"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Absensi Berhasil Disimpan</h3>
        <p class="text-gray-600 mb-6">Data kehadiran kelas XII IPA 1 telah disimpan.</p>
        <div class="bg-blue-50 p-3 rounded-lg mb-6 text-left">
            <p class="text-sm text-blue-800">
                <i class="bi bi-info-circle"></i> Tidak ada notifikasi WhatsApp yang dikirim (sesuai aturan guru mapel).
            </p>
        </div>
        <button onclick="hideModal('saveAttendanceModal')" class="btn-primary w-full py-3">
            <i class="bi bi-check"></i> Selesai
        </button>
    </div>
</div>
