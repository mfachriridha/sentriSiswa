@extends('layouts.app')

@section('title', 'Presensi - SentriSiswa')

@section('content')
<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Presensi Siswa</h1>
        <p class="text-sm text-slate-500 mt-0.5">{{ now()->translatedFormat('l, d F Y') }} &middot; Kelas XII IPA 1</p>
    </div>
    <span class="badge badge-blue self-start">Aktif</span>
</div>

<!-- Poin -->
<div class="card !bg-gradient-to-r !from-blue-50 !to-indigo-50 !border-blue-100 mb-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-xs font-bold text-blue-500 uppercase tracking-wide">Poin Pelanggaran</p>
            <p class="stat-value text-blue-900 mt-1">95<span class="text-sm font-medium text-blue-400">/100</span></p>
            <p class="text-xs text-blue-600 mt-0.5">Saldo awal 100 poin</p>
        </div>
        <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center">
            <i class="bi bi-star-fill text-2xl text-blue-500"></i>
        </div>
    </div>
</div>

<!-- Presensi Cards -->
<div class="grid lg:grid-cols-2 gap-5 mb-6">
    <!-- Presensi Masuk -->
    <div class="card anim-up">
        <div class="flex items-start justify-between mb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i class="bi bi-sunrise-fill text-emerald-600 text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900">Presensi Masuk</h3>
                    <p class="text-xs text-slate-500">Batas 07:30 WIB</p>
                </div>
            </div>
            <span class="badge badge-green">Bisa Presensi</span>
        </div>

        <div class="bg-slate-900 rounded-xl h-44 flex items-center justify-center mb-4">
            <div class="text-center text-white/60">
                <i class="bi bi-camera-fill text-4xl mb-2 block"></i>
                <p class="text-sm font-medium">Kamera Selfie</p>
                <p class="text-xs text-white/40 mt-1">Pastikan wajah terlihat jelas</p>
            </div>
        </div>

        <div class="flex items-center gap-3 p-3 bg-emerald-50 border border-emerald-200 rounded-xl mb-4">
            <i class="bi bi-geo-alt-fill text-emerald-600"></i>
            <div>
                <p class="text-xs font-bold text-emerald-800">Dalam Area Sekolah</p>
                <p class="text-[10px] text-emerald-600">106.588123, -6.119123</p>
            </div>
        </div>

        <button onclick="submitPresensi('masuk')" class="btn-brand w-full !py-3">
            <i class="bi bi-check-circle"></i> Submit Presensi Masuk
        </button>
    </div>

    <!-- Presensi Pulang -->
    <div class="card anim-up" style="animation-delay:.1s">
        <div class="flex items-start justify-between mb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center">
                    <i class="bi bi-sunset-fill text-orange-600 text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900">Presensi Pulang</h3>
                    <p class="text-xs text-slate-500">Mulai 15:30 WIB</p>
                </div>
            </div>
            <span class="badge badge-amber">Belum Waktu</span>
        </div>

        <div class="bg-slate-900 rounded-xl h-44 flex items-center justify-center mb-4 opacity-60">
            <div class="text-center text-white/40">
                <i class="bi bi-camera-fill text-4xl mb-2 block"></i>
                <p class="text-sm font-medium">Kamera Selfie</p>
                <p class="text-xs mt-1">Akan aktif pukul 15:30</p>
            </div>
        </div>

        <div class="flex items-center gap-3 p-3 bg-slate-100 border border-slate-200 rounded-xl mb-4">
            <i class="bi bi-clock text-slate-400"></i>
            <p class="text-xs font-medium text-slate-500">Menunggu waktu presensi pulang</p>
        </div>

        <button disabled class="btn-outline w-full !py-3 opacity-50 cursor-not-allowed">
            <i class="bi bi-check-circle"></i> Submit Presensi Pulang
        </button>
    </div>
</div>

<!-- Status Hari Ini -->
<div class="card mb-6 anim-up" style="animation-delay:.15s">
    <h3 class="font-bold text-slate-900 mb-4">Status Hari Ini</h3>
    <div class="flex items-center gap-4 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl">
        <div class="w-11 h-11 bg-emerald-100 rounded-full flex items-center justify-center shrink-0">
            <i class="bi bi-check-circle-fill text-emerald-600 text-xl"></i>
        </div>
        <div class="flex-1">
            <p class="font-bold text-emerald-800">Sudah Presensi Masuk</p>
            <p class="text-sm text-emerald-700">07:15 WIB &middot; Lokasi valid</p>
        </div>
        <button onclick="showPhoto()" class="btn-outline !py-2 !px-3 text-xs">
            <i class="bi bi-image"></i> Foto
        </button>
    </div>
</div>

<!-- Riwayat -->
<div class="card anim-up" style="animation-delay:.2s">
    <div class="flex justify-between items-center mb-4">
        <h3 class="font-bold text-slate-900">Riwayat Presensi</h3>
        <span class="text-xs font-semibold text-blue-600 cursor-pointer hover:underline">Lihat Semua</span>
    </div>
    <div class="space-y-2">
        <div class="flex items-center justify-between p-3 hover:bg-slate-50 rounded-xl transition">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-emerald-100 rounded-full flex items-center justify-center"><i class="bi bi-check-lg text-emerald-600"></i></div>
                <div><p class="text-sm font-semibold">Selasa, 4 Mei 2026</p><p class="text-xs text-slate-500">Masuk 07:10 &middot; Pulang 15:45</p></div>
            </div>
            <span class="badge badge-green">Hadir</span>
        </div>
        <div class="flex items-center justify-between p-3 hover:bg-slate-50 rounded-xl transition">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-amber-100 rounded-full flex items-center justify-center"><i class="bi bi-exclamation-lg text-amber-600"></i></div>
                <div><p class="text-sm font-semibold">Senin, 3 Mei 2026</p><p class="text-xs text-slate-500">Masuk 07:40 &middot; Pulang 15:50</p></div>
            </div>
            <span class="badge badge-amber">Terlambat</span>
        </div>
        <div class="flex items-center justify-between p-3 hover:bg-slate-50 rounded-xl transition">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-red-100 rounded-full flex items-center justify-center"><i class="bi bi-x-lg text-red-600"></i></div>
                <div><p class="text-sm font-semibold">Minggu, 2 Mei 2026</p><p class="text-xs text-slate-500">Tidak presensi</p></div>
            </div>
            <span class="badge badge-red">Alpha</span>
        </div>
    </div>
</div>

<!-- Foto Modal -->
<div id="photoModal" class="hidden fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4" onclick="if(event.target===this)closePhoto()">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-5 anim-fade" onclick="event.stopPropagation()">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold">Foto Presensi Masuk</h3>
            <button onclick="closePhoto()" class="text-slate-400 hover:text-slate-600"><i class="bi bi-x-lg text-xl"></i></button>
        </div>
        <div class="bg-slate-100 rounded-xl h-56 flex items-center justify-center mb-4">
            <i class="bi bi-person-fill text-6xl text-slate-300"></i>
        </div>
        <div class="text-xs text-slate-500 space-y-1">
            <p><i class="bi bi-clock mr-1"></i> 07:15 WIB</p>
            <p><i class="bi bi-geo-alt mr-1"></i> 106.588123, -6.119123</p>
            <p class="text-emerald-600 font-medium"><i class="bi bi-check-circle mr-1"></i> Hadir</p>
        </div>
    </div>
</div>

<script>
function submitPresensi(j) { alert('UI Only: Presensi '+j+' berhasil!\nNotifikasi akan dikirim ke orang tua via WhatsApp.'); }
function showPhoto() { document.getElementById('photoModal').classList.remove('hidden'); }
function closePhoto() { document.getElementById('photoModal').classList.add('hidden'); }
</script>
@endsection