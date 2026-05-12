@extends('layouts.guest')

@section('title', 'Portal Presensi Digital SMA Negeri 11 Kab. Tangerang')

@section('content')
<!-- Hero Section -->
<section class="relative min-h-[90vh] flex items-center justify-center overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0">
        <img src="{{ asset('images/hero-bg.jpg') }}" alt="Lapangan SMA Negeri 11" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-b from-[#1c6880]/80 via-[#1c6880]/70 to-black/60"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 text-center text-white anim-up">
        <div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-sm px-4 py-2 rounded-full mb-6 text-sm font-medium">
            <i class="bi bi-geo-alt-fill"></i>
            <span>SMA Negeri 11 Kab. Tangerang</span>
        </div>

        <h1 class="text-4xl sm:text-5xl md:text-6xl font-black tracking-tight leading-tight mb-4">
            Sistem Presensi<br class="hidden sm:block"> Digital
        </h1>

        <p class="text-base sm:text-lg md:text-xl text-white/90 max-w-2xl mx-auto mb-8 leading-relaxed">
            Presensi berbasis geofencing dan selfie terverifikasi. Pantau kehadiran siswa secara real-time, otomatis, dan transparan.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4">
            <a href="{{ route('auth.login') }}" class="btn-primary !py-3.5 !px-8 !text-base min-w-[160px]">
                <i class="bi bi-box-arrow-in-right"></i> Masuk
            </a>
            <a href="{{ route('auth.register') }}" class="btn-outline !py-3.5 !px-8 !text-base !border-white !text-white hover:!bg-white/20 hover:!border-white min-w-[160px]">
                <i class="bi bi-person-plus"></i> Daftar
            </a>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 animate-bounce">
        <i class="bi bi-chevron-double-down text-white/70 text-2xl"></i>
    </div>
</section>

<!-- Stats Bar -->
<section class="bg-[#1c6880] py-10 sm:py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
            <div class="text-center text-white anim-up">
                <div class="w-12 h-12 bg-white/15 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <i class="bi bi-people-fill text-2xl"></i>
                </div>
                <p class="text-3xl sm:text-4xl font-black">360+</p>
                <p class="text-sm sm:text-base text-white/80 mt-1">Siswa Aktif</p>
            </div>
            <div class="text-center text-white anim-up">
                <div class="w-12 h-12 bg-white/15 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <i class="bi bi-check-circle-fill text-2xl"></i>
                </div>
                <p class="text-3xl sm:text-4xl font-black">92%</p>
                <p class="text-sm sm:text-base text-white/80 mt-1">Tingkat Kehadiran</p>
            </div>
            <div class="text-center text-white anim-up">
                <div class="w-12 h-12 bg-white/15 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <i class="bi bi-person-badge-fill text-2xl"></i>
                </div>
                <p class="text-3xl sm:text-4xl font-black">42</p>
                <p class="text-sm sm:text-base text-white/80 mt-1">Guru & Staff</p>
            </div>
            <div class="text-center text-white anim-up">
                <div class="w-12 h-12 bg-white/15 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <i class="bi bi-diagram-3-fill text-2xl"></i>
                </div>
                <p class="text-3xl sm:text-4xl font-black">12</p>
                <p class="text-sm sm:text-base text-white/80 mt-1">Kelas</p>
            </div>
        </div>
    </div>
</section>

<!-- Features Grid -->
<section class="py-16 sm:py-20 bg-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-12 anim-up">
            <p class="text-sm font-bold text-[#1c6880] uppercase tracking-widest mb-2">Fitur Utama</p>
            <h2 class="text-3xl sm:text-4xl font-black text-[#191c1d] tracking-tight">Rangkaian Fitur Lengkap</h2>
            <p class="text-base text-[#43474f] max-w-2xl mx-auto mt-3">Semua yang Anda butuhkan untuk sistem presensi modern, akurat, dan transparan.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-[#f8f9fa] rounded-xl p-6 border border-[#c3c6d1]/30 hover:shadow-lg hover:-translate-y-1 transition-all group anim-up">
                <div class="w-12 h-12 bg-[#d4e8ee] rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <i class="bi bi-geo-alt-fill text-[#1c6880] text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-[#191c1d] mb-2">Geofencing Presisi</h3>
                <p class="text-sm text-[#43474f] leading-relaxed">Validasi lokasi siswa hanya di dalam area sekolah menggunakan GPS dan poligon geografis.</p>
            </div>

            <div class="bg-[#f8f9fa] rounded-xl p-6 border border-[#c3c6d1]/30 hover:shadow-lg hover:-translate-y-1 transition-all group anim-up">
                <div class="w-12 h-12 bg-[#dcfce7] rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <i class="bi bi-camera-fill text-[#15803d] text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-[#191c1d] mb-2">Selfie Terverifikasi</h3>
                <p class="text-sm text-[#43474f] leading-relaxed">Foto selfie sebagai bukti kehadiran yang diverifikasi oleh wali kelas secara otomatis.</p>
            </div>

            <div class="bg-[#f8f9fa] rounded-xl p-6 border border-[#c3c6d1]/30 hover:shadow-lg hover:-translate-y-1 transition-all group anim-up">
                <div class="w-12 h-12 bg-[#fef3c7] rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <i class="bi bi-graph-up-arrow text-[#a16207] text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-[#191c1d] mb-2">Laporan Real-time</h3>
                <p class="text-sm text-[#43474f] leading-relaxed">Rekap kehadiran otomatis yang bisa diakses kapan saja oleh guru, BK, dan orang tua.</p>
            </div>

            <div class="bg-[#f8f9fa] rounded-xl p-6 border border-[#c3c6d1]/30 hover:shadow-lg hover:-translate-y-1 transition-all group anim-up">
                <div class="w-12 h-12 bg-[#e6f7e6] rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <i class="bi bi-whatsapp text-[#15803d] text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-[#191c1d] mb-2">Notifikasi WhatsApp</h3>
                <p class="text-sm text-[#43474f] leading-relaxed">Orang tua menerima laporan kehadiran dan pelanggaran siswa secara otomatis via WhatsApp.</p>
            </div>

            <div class="bg-[#f8f9fa] rounded-xl p-6 border border-[#c3c6d1]/30 hover:shadow-lg hover:-translate-y-1 transition-all group anim-up">
                <div class="w-12 h-12 bg-[#fee2e2] rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <i class="bi bi-shield-exclamation text-[#ba1a1a] text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-[#191c1d] mb-2">Poin Pelanggaran</h3>
                <p class="text-sm text-[#43474f] leading-relaxed">Sistem poin pelanggaran dengan notifikasi mingguan ke orang tua dan wali kelas.</p>
            </div>

            <div class="bg-[#f8f9fa] rounded-xl p-6 border border-[#c3c6d1]/30 hover:shadow-lg hover:-translate-y-1 transition-all group anim-up">
                <div class="w-12 h-12 bg-[#e0f2f1] rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <i class="bi bi-database-fill text-[#00796b] text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-[#191c1d] mb-2">Master Data Terpusat</h3>
                <p class="text-sm text-[#43474f] leading-relaxed">Kelola data guru, siswa, dan kelas dengan mudah melalui import CSV atau input manual.</p>
            </div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="py-16 sm:py-20 bg-[#f8f9fa]">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-12 anim-up">
            <p class="text-sm font-bold text-[#1c6880] uppercase tracking-widest mb-2">Cara Kerja</p>
            <h2 class="text-3xl sm:text-4xl font-black text-[#191c1d] tracking-tight">3 Langkah Mudah</h2>
            <p class="text-base text-[#43474f] max-w-2xl mx-auto mt-3">Proses presensi yang simpel, cepat, dan terverifikasi otomatis.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 relative">
            <!-- Connector Line (desktop only) -->
            <div class="hidden md:block absolute top-16 left-[20%] right-[20%] h-0.5 bg-[#c3c6d1]"></div>

            <div class="relative text-center anim-up">
                <div class="w-14 h-14 bg-[#1c6880] text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-black relative z-10">1</div>
                <h3 class="text-lg font-bold text-[#191c1d] mb-2">Daftar Akun</h3>
                <p class="text-sm text-[#43474f] leading-relaxed max-w-xs mx-auto">Validasi NIP untuk guru atau NIS/NISN untuk siswa. Buat akun dengan email dan password.</p>
            </div>

            <div class="relative text-center anim-up">
                <div class="w-14 h-14 bg-[#1c6880] text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-black relative z-10">2</div>
                <h3 class="text-lg font-bold text-[#191c1d] mb-2">Presensi Selfie</h3>
                <p class="text-sm text-[#43474f] leading-relaxed max-w-xs mx-auto">Ambil foto selfie di area sekolah. Sistem memvalidasi lokasi dan waktu secara otomatis.</p>
            </div>

            <div class="relative text-center anim-up">
                <div class="w-14 h-14 bg-[#1c6880] text-white rounded-full flex items-center justify-center mx-auto mb-4 text-xl font-black relative z-10">3</div>
                <h3 class="text-lg font-bold text-[#191c1d] mb-2">Pantau Laporan</h3>
                <p class="text-sm text-[#43474f] leading-relaxed max-w-xs mx-auto">Guru, BK, dan orang tua bisa memantau kehadiran dan pelanggaran secara real-time.</p>
            </div>
        </div>
    </div>
</section>

<!-- Final CTA -->
<section class="py-16 sm:py-20 bg-[#1c6880]">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center text-white anim-up">
        <h2 class="text-3xl sm:text-4xl font-black tracking-tight mb-3">Siap Memulai?</h2>
        <p class="text-base sm:text-lg text-white/85 max-w-xl mx-auto mb-8">Masuk ke dashboard Anda atau daftar akun baru untuk mulai menggunakan SentriSiswa.</p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 sm:gap-4">
            <a href="{{ route('auth.login') }}" class="bg-white text-[#1c6880] hover:bg-white/90 font-bold text-base py-3.5 px-8 rounded-lg transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5 min-w-[160px] inline-flex items-center justify-center gap-2">
                <i class="bi bi-box-arrow-in-right"></i> Masuk
            </a>
            <a href="{{ route('auth.register') }}" class="border-2 border-white text-white hover:bg-white/15 font-bold text-base py-3.5 px-8 rounded-lg transition-all min-w-[160px] inline-flex items-center justify-center gap-2">
                <i class="bi bi-person-plus"></i> Daftar
            </a>
        </div>
    </div>
</section>
@endsection
