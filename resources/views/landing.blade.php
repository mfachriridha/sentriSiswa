@extends('layouts.guest')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <!-- Hero Section -->
    <div class="container mx-auto px-4 py-12 lg:py-20">
        <div class="text-center mb-12">
            <div class="w-24 h-24 bg-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                <i class="bi bi-shield-check text-white text-5xl"></i>
            </div>
            <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-4">Sentri Siswa</h1>
            <p class="text-xl text-gray-600 mb-2">Sistem Absensi SMA Terintegrasi</p>
            <p class="text-sm text-gray-500 uppercase tracking-widest">SMAN 11 Kab. Tangerang</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center mb-16">
            <a href="#" class="btn-primary text-center text-lg px-8 py-3 inline-flex items-center justify-center gap-2">
                <i class="bi bi-box-arrow-in-right"></i> Login
            </a>
            <a href="#" class="btn-secondary text-center text-lg px-8 py-3 inline-flex items-center justify-center gap-2">
                <i class="bi bi-person-plus"></i> Register
            </a>
        </div>

        <!-- PDF Viewer Section -->
        <div class="max-w-5xl mx-auto">
            <div class="card">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Tata Tertib Sekolah</h2>
                        <p class="text-gray-600 mt-1">Baca dan pahami tata tertib sebelum menggunakan sistem</p>
                    </div>
                    <button class="btn-primary inline-flex items-center gap-2">
                        <i class="bi bi-download"></i> Download PDF
                    </button>
                </div>

                <!-- PDF Viewer Placeholder -->
                <div class="bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 h-96 lg:h-[600px] flex flex-col items-center justify-center">
                    <div class="text-center">
                        <i class="bi bi-file-earmark-pdf text-6xl text-red-400 mb-4"></i>
                        <p class="text-gray-600 font-medium">PDF Tata Tertib Belum Diupload</p>
                        <p class="text-sm text-gray-500 mt-2">Admin akan mengupload dokumen tata tertib sekolah</p>
                        <button class="mt-4 btn-secondary inline-flex items-center gap-2">
                            <i class="bi bi-upload"></i> Upload PDF (Admin)
                        </button>
                    </div>
                </div>

                <!-- PDF Info -->
                <div class="mt-4 flex items-center gap-4 text-sm text-gray-500">
                    <span><i class="bi bi-info-circle"></i> Pastikan Anda memahami tata tertib</span>
                </div>
            </div>
        </div>

        <!-- Features Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-16 max-w-5xl mx-auto">
            <div class="card text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-geo-alt-fill text-3xl text-blue-600"></i>
                </div>
                <h3 class="font-bold text-lg mb-2">Geofencing</h3>
                <p class="text-gray-600 text-sm">Validasi lokasi dengan GPS dan polygon area sekolah</p>
            </div>
            <div class="card text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-camera-fill text-3xl text-green-600"></i>
                </div>
                <h3 class="font-bold text-lg mb-2">Selfie Absensi</h3>
                <p class="text-gray-600 text-sm">Absensi dengan foto selfie yang terverifikasi</p>
            </div>
            <div class="card text-center">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-whatsapp text-3xl text-purple-600"></i>
                </div>
                <h3 class="font-bold text-lg mb-2">WhatsApp Notif</h3>
                <p class="text-gray-600 text-sm">Notifikasi otomatis ke orang tua via WhatsApp</p>
            </div>
        </div>
    </div>
</div>
@endsection
