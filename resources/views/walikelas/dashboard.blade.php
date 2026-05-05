@extends('layouts.app')

@section('title', 'Dashboard Wali Kelas')

@section('sidebar-menu')
<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-blue-600 text-white font-medium transition">
    <i class="bi bi-grid-1x2-fill"></i> Dashboard
</a>

<div class="pt-4 pb-2 text-xs font-semibold text-slate-500 uppercase px-4">Absensi</div>

<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
    <i class="bi bi-camera-fill"></i> Absensi Masuk
</a>

<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
    <i class="bi bi-box-arrow-left"></i> Absensi Pulang
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
    <h1 class="text-2xl font-bold text-gray-900">Absensi Masuk - Kelas XII IPA 1</h1>
    <p class="text-gray-600 mt-1">Rabu, 5 Mei 2026 | Wali Kelas: Budi Santoso, S.Pd</p>
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
        <p class="text-sm text-gray-600">Terlambat</p>
        <p class="text-2xl font-bold text-yellow-600">2</p>
    </div>
    <div class="card !p-4">
        <p class="text-sm text-gray-600">Belum Absen</p>
        <p class="text-2xl font-bold text-red-600">0</p>
    </div>
</div>

<!-- Filter & Actions -->
<div class="card mb-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h3 class="text-lg font-bold">Daftar Absensi Siswa</h3>
            <p class="text-sm text-gray-600">Verifikasi foto selfie dan koordinat GPS</p>
        </div>
        <div class="flex gap-2">
            <select class="input-field !w-auto">
                <option>Semua Status</option>
                <option>Hadir</option>
                <option>Terlambat</option>
                <option>Belum Absen</option>
            </select>
            <button onclick="showModal('saveAllModal')" class="btn-primary inline-flex items-center gap-2">
                <i class="bi bi-check-circle"></i> Simpan Semua
            </button>
        </div>
    </div>

    <!-- Student Attendance Cards -->
    <div class="space-y-4">
        <!-- Student 1 - Hadir dengan foto -->
        <div class="border border-gray-200 rounded-xl p-4 hover:bg-gray-50 transition">
            <div class="flex flex-col lg:flex-row gap-4">
                <div class="flex items-center gap-4 flex-1">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center shrink-0">
                        <i class="bi bi-person-fill text-blue-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900">Ahmad Fauzi</p>
                        <p class="text-sm text-gray-500">NIS: S001 | Jam: 07:15</p>
                    </div>
                    <span class="badge-success">Hadir</span>
                </div>

                <div class="flex items-center gap-4 lg:w-2/3">
                    <!-- Selfie Preview -->
                    <div class="shrink-0">
                        <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                            <i class="bi bi-camera-fill text-2xl text-gray-400"></i>
                        </div>
                    </div>

                    <!-- GPS Info -->
                    <div class="flex-1 text-sm">
                        <p class="text-gray-600"><i class="bi bi-geo-alt-fill text-green-600"></i> GPS: 106.588123, -6.119123</p>
                        <p class="text-xs text-green-600 mt-1"><i class="bi bi-check-circle-fill"></i> Dalam area sekolah</p>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <button onclick="showModal('viewPhotoModal')" class="btn-secondary !py-1.5 !px-3 text-sm">
                            <i class="bi bi-eye"></i> Lihat
                        </button>
                        <select class="input-field !py-1.5 !px-2 text-sm w-32">
                            <option>Hadir</option>
                            <option>Terlambat</option>
                            <option>Izin</option>
                            <option>Sakit</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student 2 - Terlambat -->
        <div class="border border-gray-200 rounded-xl p-4 hover:bg-gray-50 transition">
            <div class="flex flex-col lg:flex-row gap-4">
                <div class="flex items-center gap-4 flex-1">
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center shrink-0">
                        <i class="bi bi-person-fill text-yellow-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-semibold text-gray-900">Nurul Hidayah</p>
                        <p class="text-sm text-gray-500">NIS: S002 | Jam: 07:45</p>
                    </div>
                    <span class="badge-warning">Terlambat</span>
                </div>

                <div class="flex items-center gap-4 lg:w-2/3">
                    <div class="shrink-0">
                        <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                            <i class="bi bi-camera-fill text-2xl text-gray-400"></i>
                        </div>
                    </div>

                    <div class="flex-1 text-sm">
                        <p class="text-gray-600"><i class="bi bi-geo-alt-fill text-green-600"></i> GPS: 106.587957, -6.118907</p>
                        <p class="text-xs text-green-600 mt-1"><i class="bi bi-check-circle-fill"></i> Dalam area sekolah</p>
                    </div>

                    <div class="flex gap-2">
                        <button onclick="showModal('viewPhotoModal')" class="btn-secondary !py-1.5 !px-3 text-sm">
                            <i class="bi bi-eye"></i> Lihat
                        </button>
                        <select class="input-field !py-1.5 !px-2 text-sm w-32">
                            <option>Terlambat</option>
                            <option>Hadir</option>
                            <option>Izin</option>
                            <option>Sakit</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student 3 - Belum Absen -->
        <div class="border border-gray-200 rounded-xl p-4 bg-gray-50">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center shrink-0">
                    <i class="bi bi-person-fill text-gray-400"></i>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-900">Budi Darmawan</p>
                    <p class="text-sm text-gray-500">NIS: S003</p>
                </div>
                <span class="badge-danger">Belum Absen</span>
            </div>
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

<!-- View Photo Modal -->
<div id="viewPhotoModal" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-2xl z-50 w-full max-w-lg p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold">Foto Selfie</h3>
        <button onclick="hideModal('viewPhotoModal')" class="text-gray-500 hover:text-gray-700">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <div class="bg-gray-200 rounded-lg h-64 flex items-center justify-center mb-4">
        <div class="text-center">
            <i class="bi bi-camera-fill text-6xl text-gray-400"></i>
            <p class="text-sm text-gray-500 mt-2">Foto selfie siswa</p>
        </div>
    </div>
    <div class="bg-gray-50 p-3 rounded-lg">
        <p class="text-sm text-gray-600"><i class="bi bi-geo-alt-fill text-green-600"></i> Koordinat GPS:</p>
        <p class="text-sm font-mono mt-1">106.588123, -6.119123</p>
        <p class="text-xs text-green-600 mt-2"><i class="bi bi-check-circle-fill"></i> Valid: Dalam area geofencing</p>
    </div>
</div>

<!-- Save All Modal (Success) -->
<div id="saveAllModal" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-2xl z-50 w-full max-w-md p-6">
    <div class="text-center">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="bi bi-check-circle-fill text-5xl text-green-600"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Data Absensi Berhasil Disimpan</h3>
        <p class="text-gray-600 mb-2">Notifikasi telah dikirim ke orang tua siswa via WhatsApp</p>
        <div class="bg-green-50 p-3 rounded-lg mb-6 text-left">
            <p class="text-sm text-green-800">
                <i class="bi bi-whatsapp"></i> <strong>28 pesan</strong> terkirim ke orang tua/wali
            </p>
        </div>
        <button onclick="hideModal('saveAllModal')" class="btn-primary w-full py-3">
            <i class="bi bi-check"></i> Selesai
        </button>
    </div>
</div>
