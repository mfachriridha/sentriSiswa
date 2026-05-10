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
                <div class="bg-[#e7e8e9] px-5 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <i class="bi bi-file-earmark-pdf text-[#ba1a1a] text-lg"></i>
                        <span class="text-sm font-bold text-[#001e40]">TATA_TERTIB_2025_2026.pdf</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="alert('UI Only: Zoom out')" class="p-1.5 hover:bg-[#d9dadb] rounded transition-colors">
                            <i class="bi bi-dash text-[#43474f] text-sm"></i>
                        </button>
                        <span class="text-xs font-semibold text-[#43474f]">100%</span>
                        <button onclick="alert('UI Only: Zoom in')" class="p-1.5 hover:bg-[#d9dadb] rounded transition-colors">
                            <i class="bi bi-plus text-[#43474f] text-sm"></i>
                        </button>
                        <span class="w-px h-5 bg-[#c3c6d1] mx-1"></span>
                        <button onclick="alert('UI Only: Download')" class="p-1.5 hover:bg-[#d9dadb] rounded transition-colors">
                            <i class="bi bi-download text-[#43474f] text-sm"></i>
                        </button>
                    </div>
                    @endif
                </div>
            </div>
                    @else
                    <div id="pdfContent" class="w-full max-w-2xl bg-white shadow-2xl p-6 md:p-12 min-h-[800px]">
                        <div class="text-center border-b-2 border-[#001e40] pb-6 mb-6">
                            <h4 class="text-lg font-black text-[#001e40] uppercase tracking-widest">SMA Negeri 11 Kab. Tangerang</h4>
                            <p class="text-xs font-bold text-[#0062a0] mt-1">KEPUTUSAN KEPALA SEKOLAH NOMOR: 421.3/SK/2025</p>
                        </div>
                        <div class="space-y-5">
                            <div class="space-y-1.5">
                                <h5 class="text-sm font-bold text-[#001e40]">BAB I: KETENTUAN UMUM</h5>
                                <div class="h-2 w-3/4 bg-[#e1e3e4] rounded-full"></div>
                                <div class="h-2 w-full bg-[#e7e8e9] rounded-full"></div>
                                <div class="h-2 w-5/6 bg-[#e7e8e9] rounded-full"></div>
                            </div>
                            <div class="space-y-1.5">
                                <h5 class="text-sm font-bold text-[#001e40]">BAB II: KEHADIRAN DAN KETERLAMBATAN</h5>
                                <div class="h-2 w-1/2 bg-[#e1e3e4] rounded-full"></div>
                                <div class="h-2 w-full bg-[#e7e8e9] rounded-full"></div>
                                <div class="h-2 w-full bg-[#e7e8e9] rounded-full"></div>
                                <div class="h-2 w-4/5 bg-[#e7e8e9] rounded-full"></div>
                            </div>
                            <div class="space-y-1.5">
                                <h5 class="text-sm font-bold text-[#001e40]">BAB III: PENGGUNAAN SERAGAM</h5>
                                <div class="h-2 w-2/3 bg-[#e1e3e4] rounded-full"></div>
                                <div class="h-2 w-full bg-[#e7e8e9] rounded-full"></div>
                                <div class="h-2 w-11/12 bg-[#e7e8e9] rounded-full"></div>
                            </div>
                            <div class="space-y-1.5">
                                <h5 class="text-sm font-bold text-[#001e40]">BAB IV: SANKSI DAN PENGHARGAAN</h5>
                                <div class="h-2 w-3/4 bg-[#e1e3e4] rounded-full"></div>
                                <div class="h-2 w-full bg-[#e7e8e9] rounded-full"></div>
                                <div class="h-2 w-5/6 bg-[#e7e8e9] rounded-full"></div>
                            </div>
                        </div>
                        <div class="mt-8 p-4 bg-[#f8f9fa] rounded-lg text-center text-sm text-[#43474f]">
                            <i class="bi bi-info-circle mr-1"></i> Dokumen tata tertib resmi — hubungi admin untuk mengunggah PDF asli.
                        </div>
                    </div>
                </div>
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