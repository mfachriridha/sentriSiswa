@extends('layouts.app')

@section('title', 'Dashboard Siswa')

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

<div class="pt-4 pb-2 text-xs font-semibold text-slate-500 uppercase px-4">Akun</div>

<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
    <i class="bi bi-person-circle"></i> Profil Saya
</a>

<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
    <i class="bi bi-bar-chart-fill"></i> Poin Saya
</a>

<a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
    <i class="bi bi-box-arrow-right"></i> Logout
</a>
@endsection

@section('content')
<!-- Header Info -->
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard Siswa</h1>
    <p class="text-gray-600 mt-1">Rabu, 5 Mei 2026 | Kelas: XII IPA 1</p>
</div>

<!-- Status Poin -->
<div class="card mb-8 bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-200">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm text-blue-600 font-medium">Poin Pelanggaran</p>
            <p class="text-3xl font-bold text-blue-900 mt-1">95</p>
            <p class="text-xs text-blue-700 mt-1">Dari saldo awal 100</p>
        </div>
        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
            <i class="bi bi-star-fill text-3xl text-blue-600"></i>
        </div>
    </div>
</div>

<!-- Absensi Buttons -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <!-- Absensi Masuk -->
    <div class="card hover:shadow-md transition cursor-pointer" onclick="showModal('absenMasukModal')">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center">
                <i class="bi bi-camera-fill text-3xl text-green-600"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-gray-900">Absensi Masuk</h3>
                <p class="text-sm text-gray-600">Ambil selfie & validasi GPS</p>
                <p class="text-xs text-gray-500 mt-1">Batas: 07:30 WIB</p>
            </div>
            <span class="badge-success">Bisa Absen</span>
        </div>
    </div>

    <!-- Absensi Pulang -->
    <div class="card hover:shadow-md transition cursor-pointer" onclick="showModal('absenPulangModal')">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center">
                <i class="bi bi-box-arrow-left text-3xl text-orange-600"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-gray-900">Absensi Pulang</h3>
                <p class="text-sm text-gray-600">Ambil selfie & validasi GPS</p>
                <p class="text-xs text-gray-500 mt-1">Mulai: 15:30 WIB</p>
            </div>
            <span class="badge-warning">Belum Waktu</span>
        </div>
    </div>
</div>

<!-- Today's Status -->
<div class="card mb-8">
    <h3 class="text-lg font-bold mb-4">Status Absensi Hari Ini</h3>
    <div class="flex items-center gap-4 p-4 bg-green-50 rounded-lg border border-green-200">
        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
            <i class="bi bi-check-circle-fill text-2xl text-green-600"></i>
        </div>
        <div class="flex-1">
            <p class="font-semibold text-green-800">Anda Sudah Absen Masuk</p>
            <p class="text-sm text-green-700">Pukul 07:15 WIB | Lokasi: Valid</p>
        </div>
        <button onclick="showModal('viewTodayPhoto')" class="btn-secondary !py-1.5 !px-3 text-sm">
            <i class="bi bi-eye"></i> Lihat Foto
        </button>
    </div>
</div>

<!-- Riwayat Singkat -->
<div class="card">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-bold">Riwayat Absensi</h3>
        <a href="#" class="text-sm text-blue-600 hover:text-blue-800">Lihat Semua</a>
    </div>
    <div class="space-y-3">
        <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="bi bi-check-lg text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm font-medium">Selasa, 4 Mei 2026</p>
                    <p class="text-xs text-gray-500">Masuk: 07:10 | Pulang: 15:45</p>
                </div>
            </div>
            <span class="badge-success">Hadir</span>
        </div>
        <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="bi bi-exclamation-lg text-yellow-600"></i>
                </div>
                <div>
                    <p class="text-sm font-medium">Senin, 3 Mei 2026</p>
                    <p class="text-xs text-gray-500">Masuk: 07:40 | Pulang: 15:50</p>
                </div>
            </div>
            <span class="badge-warning">Terlambat</span>
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

<!-- Absensi Masuk Modal -->
<div id="absenMasukModal" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-2xl z-50 w-full max-w-lg p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold">Absensi Masuk</h3>
        <button onclick="hideModal('absenMasukModal')" class="text-gray-500 hover:text-gray-700">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <div class="space-y-4">
        <!-- Camera Preview -->
        <div class="bg-gray-900 rounded-lg h-64 flex items-center justify-center">
            <div class="text-center text-white">
                <i class="bi bi-camera-fill text-5xl mb-3"></i>
                <p class="text-sm">Kamera Selfie</p>
                <p class="text-xs text-gray-400 mt-1">Pastikan wajah terlihat jelas</p>
            </div>
        </div>

        <!-- GPS Status -->
        <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center gap-2">
                <i class="bi bi-geo-alt-fill text-green-600"></i>
                <p class="text-sm font-medium text-green-800">GPS: Dalam Area Sekolah</p>
            </div>
            <p class="text-xs text-green-700 mt-1">106.588123, -6.119123</p>
        </div>

        <!-- Preview Photo -->
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
            <i class="bi bi-image text-4xl text-gray-400 mb-2"></i>
            <p class="text-sm text-gray-600">Preview Selfie</p>
            <p class="text-xs text-gray-500">Ukuran maks. 500KB</p>
        </div>

        <!-- Submit Button -->
        <button onclick="alert('UI Only: Absensi masuk berhasil!'); hideModal('absenMasukModal')" class="btn-primary w-full py-3">
            <i class="bi bi-check-circle"></i> Submit Absensi Masuk
        </button>
    </div>
</div>

<!-- Absensi Pulang Modal -->
<div id="absenPulangModal" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-2xl z-50 w-full max-w-lg p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold">Absensi Pulang</h3>
        <button onclick="hideModal('absenPulangModal')" class="text-gray-500 hover:text-gray-700">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <div class="space-y-4">
        <div class="bg-gray-900 rounded-lg h-64 flex items-center justify-center">
            <div class="text-center text-white">
                <i class="bi bi-camera-fill text-5xl mb-3"></i>
                <p class="text-sm">Kamera Selfie Pulang</p>
            </div>
        </div>

        <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-center gap-2">
                <i class="bi bi-geo-alt-fill text-green-600"></i>
                <p class="text-sm font-medium text-green-800">GPS: Dalam Area Sekolah</p>
            </div>
        </div>

        <button onclick="alert('UI Only: Absensi pulang berhasil!'); hideModal('absenPulangModal')" class="btn-primary w-full py-3">
            <i class="bi bi-check-circle"></i> Submit Absensi Pulang
        </button>
    </div>
</div>

<!-- View Today Photo Modal -->
<div id="viewTodayPhoto" class="hidden fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-2xl z-50 w-full max-w-md p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold">Foto Absensi Hari Ini</h3>
        <button onclick="hideModal('viewTodayPhoto')" class="text-gray-500 hover:text-gray-700">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <div class="bg-gray-200 rounded-lg h-64 flex items-center justify-center mb-4">
        <i class="bi bi-image text-6xl text-gray-400"></i>
    </div>
    <div class="text-sm text-gray-600">
        <p><i class="bi bi-clock"></i> Pukul: 07:15 WIB</p>
        <p class="mt-1"><i class="bi bi-geo-alt"></i> GPS: 106.588123, -6.119123</p>
        <p class="mt-1"><i class="bi bi-check-circle text-green-600"></i> Status: Hadir</p>
    </div>
</div>
