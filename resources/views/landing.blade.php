@extends('layouts.guest')

@section('title', 'Portal Presensi SMA')

@section('content')
<div class="p-4 md:p-8">
    <div class="max-w-6xl mx-auto w-full">

        <!-- Hero -->
        <div class="mb-10 text-center anim-up">
            <h2 class="text-3xl md:text-4xl font-black text-[#001e40] tracking-tight mb-3">Selamat Datang di Portal Presensi</h2>
            <h3 class="text-xl font-bold text-[#0062a0] mb-3">SMA Negeri 11 Kab. Tangerang</h3>
            <p class="text-[#43474f] max-w-2xl mx-auto">
                Silakan lihat dokumen tata tertib untuk memahami hak dan kewajiban Anda sebelum melanjutkan ke dashboard.
            </p>
        </div>

        <!-- Bento Grid: PDF Viewer + Sidebar -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-5 mb-10 anim-up" style="animation-delay:.08s">

            <!-- Main PDF Area -->
            <div class="lg:col-span-3 bg-white rounded-lg shadow-sm overflow-hidden flex flex-col h-[500px] md:h-[600px]">
                <?php
                $pdfPath = 'storage/tata-tertib/tata_tertib.pdf';
                $hasPdf = file_exists(public_path($pdfPath));
                ?>
                @if($hasPdf)
                    <iframe src="{{ asset($pdfPath) }}" class="w-full h-full border-0"></iframe>
                @else
                    <div class="flex-1 flex items-center justify-center p-8">
                        <div class="text-center">
                            <i class="bi bi-file-earmark-pdf text-6xl text-[#c3c6d1] mb-4"></i>
                            <p class="text-[#43474f] font-semibold">Dokumen tata tertib belum diunggah.</p>
                            <p class="text-sm text-[#717680] mt-1">Hubungi admin untuk mengunggah PDF.</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar Cards -->
            <div class="space-y-4">
                <div class="bg-[#001e40] text-white p-5 rounded-lg shadow-md relative overflow-hidden">
                    <div class="relative z-10">
                        <i class="bi bi-info-circle text-2xl mb-3 block"></i>
                        <h4 class="font-bold text-base mb-2">Ringkasan Utama</h4>
                        <ul class="text-xs space-y-2 opacity-90">
                            <li class="flex gap-2"><i class="bi bi-check-circle text-[#32a3fd] shrink-0 mt-0.5"></i> Toleransi keterlambatan 15 menit.</li>
                            <li class="flex gap-2"><i class="bi bi-check-circle text-[#32a3fd] shrink-0 mt-0.5"></i> Wajib melakukan presensi selfie.</li>
                            <li class="flex gap-2"><i class="bi bi-check-circle text-[#32a3fd] shrink-0 mt-0.5"></i> Minimal kehadiran 85%.</li>
                        </ul>
                    </div>
                    <div class="absolute -right-4 -bottom-4 w-20 h-20 bg-white/10 rounded-full blur-2xl"></div>
                </div>

                <div class="bg-white p-5 rounded-lg border border-[#c3c6d1]/30 space-y-3 shadow-sm">
                    <h4 class="font-bold text-sm text-[#001e40]">Kontak Bantuan</h4>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-[#d0e4ff] flex items-center justify-center">
                            <i class="bi bi-headset text-[#00497a] text-sm"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-[#43474f]">Admin Kesiswaan</p>
                            <p class="text-xs font-bold text-[#001e40]">admin@sentrisiswa.sch.id</p>
                        </div>
                    </div>
                </div>

                <div class="bg-[#e6f7e6] text-[#002204] p-5 rounded-lg flex items-start gap-3 shadow-sm">
                    <i class="bi bi-shield-check text-[#005312] text-lg"></i>
                    <div>
                        <p class="text-[10px] uppercase font-bold tracking-wider text-[#005312]">Status Dokumen</p>
                        <p class="text-sm font-bold">Tervalidasi &amp; Berlaku</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10 anim-up" style="animation-delay:.16s">
            <div class="bg-white rounded-lg p-5 border border-[#c3c6d1]/30 shadow-sm text-center">
                <i class="bi bi-people-fill text-[#0062a0] text-xl"></i>
                <p class="stat-value text-[#001e40] mt-2">360</p>
                <p class="text-xs font-medium text-[#43474f]">Siswa Aktif</p>
            </div>
            <div class="bg-white rounded-lg p-5 border border-[#c3c6d1]/30 shadow-sm text-center">
                <i class="bi bi-check-circle-fill text-[#15803d] text-xl"></i>
                <p class="stat-value text-[#15803d] mt-2">92%</p>
                <p class="text-xs font-medium text-[#43474f]">Kehadiran</p>
            </div>
            <div class="bg-white rounded-lg p-5 border border-[#c3c6d1]/30 shadow-sm text-center">
                <i class="bi bi-person-badge-fill text-[#0062a0] text-xl"></i>
                <p class="stat-value text-[#001e40] mt-2">42</p>
                <p class="text-xs font-medium text-[#43474f]">Guru</p>
            </div>
            <div class="bg-white rounded-lg p-5 border border-[#c3c6d1]/30 shadow-sm text-center">
                <i class="bi bi-diagram-3-fill text-[#0062a0] text-xl"></i>
                <p class="stat-value text-[#001e40] mt-2">12</p>
                <p class="text-xs font-medium text-[#43474f]">Kelas</p>
            </div>
        </div>

        <!-- Fitur -->
        <div class="mb-10 anim-up" style="animation-delay:.24s">
            <p class="text-xs font-bold text-[#0062a0] uppercase tracking-widest mb-1">Fitur</p>
            <h2 class="section-title mb-6">Rangkaian Fitur</h2>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg p-5 border border-[#c3c6d1]/30 shadow-sm hover:shadow-md transition group">
                    <div class="w-10 h-10 bg-[#e6f0ff] rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <i class="bi bi-geo-alt-fill text-[#0062a0]"></i>
                    </div>
                    <h3 class="font-bold text-sm text-[#001e40] mb-1">Geofencing Presisi</h3>
                    <p class="text-xs text-[#43474f]">Validasi lokasi siswa hanya di dalam area sekolah.</p>
                </div>
                <div class="bg-white rounded-lg p-5 border border-[#c3c6d1]/30 shadow-sm hover:shadow-md transition group">
                    <div class="w-10 h-10 bg-[#e6f7e6] rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <i class="bi bi-camera-fill text-[#15803d]"></i>
                    </div>
                    <h3 class="font-bold text-sm text-[#001e40] mb-1">Selfie Terverifikasi</h3>
                    <p class="text-xs text-[#43474f]">Foto selfie sebagai bukti kehadiran siswa.</p>
                </div>
                <div class="bg-white rounded-lg p-5 border border-[#c3c6d1]/30 shadow-sm hover:shadow-md transition group">
                    <div class="w-10 h-10 bg-[#f3e6ff] rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <i class="bi bi-graph-up text-[#7c3aed]"></i>
                    </div>
                    <h3 class="font-bold text-sm text-[#001e40] mb-1">Laporan Real-time</h3>
                    <p class="text-xs text-[#43474f]">Rekap kehadiran otomatis dan real-time.</p>
                </div>
                <div class="bg-white rounded-lg p-5 border border-[#c3c6d1]/30 shadow-sm hover:shadow-md transition group">
                    <div class="w-10 h-10 bg-[#fff3cd] rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <i class="bi bi-whatsapp text-[#b8860b]"></i>
                    </div>
                    <h3 class="font-bold text-sm text-[#001e40] mb-1">Notifikasi WhatsApp</h3>
                    <p class="text-xs text-[#43474f]">Orang tua terima laporan kehadiran otomatis.</p>
                </div>
                <div class="bg-white rounded-lg p-5 border border-[#c3c6d1]/30 shadow-sm hover:shadow-md transition group">
                    <div class="w-10 h-10 bg-[#fce4ec] rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <i class="bi bi-shield-check text-[#d32f2f]"></i>
                    </div>
                    <h3 class="font-bold text-sm text-[#001e40] mb-1">Poin Pelanggaran</h3>
                    <p class="text-xs text-[#43474f]">Sistem poin dengan notifikasi mingguan.</p>
                </div>
                <div class="bg-white rounded-lg p-5 border border-[#c3c6d1]/30 shadow-sm hover:shadow-md transition group">
                    <div class="w-10 h-10 bg-[#e0f2f1] rounded-lg flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <i class="bi bi-file-earmark-spreadsheet text-[#00796b]"></i>
                    </div>
                    <h3 class="font-bold text-sm text-[#001e40] mb-1">Master Data Terpusat</h3>
                    <p class="text-xs text-[#43474f]">Kelola data guru &amp; siswa dengan mudah.</p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection