@extends('layouts.app')

@section('title', 'Kehadiran - SentriSiswa')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Kehadiran</h1>
        <p class="text-sm text-slate-500 mt-0.5">{{ now()->translatedFormat('l, d F Y') }} &middot; Kelas XII IPA 1</p>
    </div>
</div>

<div class="grid lg:grid-cols-5 gap-5">
    <!-- Kiri: Selfie + Lokasi -->
    <div class="lg:col-span-3 space-y-5">

        <!-- Status Presensi Hari Ini -->
        <div class="flex items-center gap-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl anim-up">
            <div class="w-11 h-11 bg-emerald-100 rounded-full flex items-center justify-center shrink-0">
                <i class="bi bi-check-circle-fill text-emerald-600 text-xl"></i>
            </div>
            <div class="flex-1">
                <p class="font-bold text-emerald-800">Sudah Presensi Hari Ini</p>
                <p class="text-sm text-emerald-700">07:15 WIB &middot; Lokasi: valid</p>
            </div>
            <button onclick="alert('Lihat foto presensi')" class="btn-outline !py-2 !px-3 text-xs">
                <i class="bi bi-image"></i> Lihat Foto
            </button>
        </div>

        <!-- Kamera Selfie -->
        <div class="card anim-up" style="animation-delay:.08s">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-slate-900 rounded-xl flex items-center justify-center">
                        <i class="bi bi-camera-fill text-white text-lg"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900">Selfie Presensi</h3>
                        <p class="text-xs text-slate-500">Ambil foto selfie untuk bukti kehadiran</p>
                    </div>
                </div>
                <span class="badge badge-green">Aktif</span>
            </div>

            <!-- Camera Preview -->
            <div class="bg-slate-900 rounded-xl h-56 flex items-center justify-center mb-4">
                <div class="text-center text-white/60">
                    <i class="bi bi-camera-fill text-5xl mb-3 block"></i>
                    <p class="text-sm font-medium">Kamera Siap</p>
                    <p class="text-xs text-white/40 mt-1">Pastikan wajah terlihat jelas & pencahayaan cukup</p>
                </div>
            </div>

            <button onclick="alert('UI Only: Foto selfie berhasil diambil!')" class="btn-brand w-full !py-3">
                <i class="bi bi-camera"></i> Ambil Foto Selfie
            </button>
        </div>

        <!-- Deteksi Lokasi -->
        <div class="card anim-up" style="animation-delay:.16s">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="bi bi-geo-alt-fill text-blue-600 text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900">Deteksi Lokasi</h3>
                    <p class="text-xs text-slate-500">Geofencing &mdash; validasi area sekolah</p>
                </div>
            </div>

            <!-- Status Lokasi -->
            <div class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-xl mb-4">
                <div class="w-9 h-9 bg-emerald-100 rounded-full flex items-center justify-center shrink-0">
                    <i class="bi bi-check-circle-fill text-emerald-600"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold text-emerald-800">Dalam Area Absen</p>
                    <p class="text-xs text-emerald-700 mt-0.5">Lokasi Anda berada di dalam polygon area sekolah</p>
                </div>
            </div>

            <div class="space-y-2 text-sm">
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                    <span class="text-slate-500">Latitude</span>
                    <span class="font-mono font-semibold text-slate-800">-6.117940</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                    <span class="text-slate-500">Longitude</span>
                    <span class="font-mono font-semibold text-slate-800">106.577800</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                    <span class="text-slate-500">Akurasi</span>
                    <span class="font-semibold text-emerald-600">~12 meter</span>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <button onclick="submitPresensi()" class="btn-primary w-full !py-3.5 text-base anim-up" style="animation-delay:.24s">
            <i class="bi bi-check-circle"></i> Kirim Presensi
        </button>
    </div>

    <!-- Kanan: Info Geofencing -->
    <div class="lg:col-span-2 space-y-4">
        <!-- Peta Area -->
        <div class="card anim-up" style="animation-delay:.12s">
            <h3 class="font-bold text-slate-900 mb-3">Area Sekolah</h3>
            <div class="bg-slate-100 rounded-xl h-48 flex items-center justify-center relative overflow-hidden">
                <!-- Simulasi peta dengan polygon -->
                <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(0,98,160,0.08),transparent_70%)]"></div>
                <div class="relative">
                    <!-- Polygon Area -->
                    <svg width="200" height="160" viewBox="0 0 200 160" class="opacity-80">
                        <polygon points="30,20 70,140 170,120 150,30 80,15 30,20"
                            fill="rgba(0,98,160,0.15)" stroke="#0062a0" stroke-width="2" stroke-dasharray="4,3"/>
                        <text x="100" y="85" text-anchor="middle" fill="#0062a0" font-size="10" font-weight="600">Area Absen</text>
                        <!-- Titik user -->
                        <circle cx="100" cy="70" r="6" fill="#059669" stroke="white" stroke-width="2"/>
                        <text x="100" y="58" text-anchor="middle" fill="#059669" font-size="8" font-weight="700">ANDA</text>
                    </svg>
                </div>
            </div>
            <div class="mt-3 text-xs text-slate-500 text-center">
                <i class="bi bi-geo-alt text-emerald-600"></i> Titik lokasi Anda saat ini
            </div>
        </div>

        <!-- Info Polygon -->
        <div class="card anim-up" style="animation-delay:.2s">
            <h3 class="font-bold text-slate-900 mb-3">Batas Area Absen</h3>
            <div class="space-y-2 text-sm">
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                    <span class="text-slate-500">Status</span>
                    <span class="badge badge-green">Dalam Jangkauan</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                    <span class="text-slate-500">Titik Polygon</span>
                    <span class="font-semibold">6 titik</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                    <span class="text-slate-500">Luas Area</span>
                    <span class="font-semibold">~1.200 m²</span>
                </div>
            </div>
            <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-700">
                <i class="bi bi-info-circle-fill mr-1"></i> Presensi hanya dapat dilakukan di dalam area yang telah ditentukan.
            </div>
        </div>
    </div>
</div>

<script>
function submitPresensi() {
    alert('UI Only: Presensi berhasil!\n\nSelfie & lokasi telah tervalidasi.\nNotifikasi akan dikirim ke orang tua via WhatsApp.');
}
</script>
@endsection