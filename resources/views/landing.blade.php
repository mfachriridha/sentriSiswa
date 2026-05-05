@extends('layouts.guest')

@section('title', 'SentriSiswa - Presensi Sekolah')

@section('content')
<div class="py-10 px-5 sm:py-16">
    <div class="mx-auto max-w-5xl">
        
        <!-- Hero -->
        <div class="text-center mb-14 anim-up">
            <div class="inline-flex items-center gap-2 bg-blue-50 text-blue-700 px-4 py-1.5 rounded-full text-xs font-semibold tracking-wide mb-6 border border-blue-100">
                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></span>
                Sistem Presensi Siswa SMA
            </div>
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 mb-5 leading-tight">
                Presensi Modern<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">untuk Sekolah</span>
            </h1>
            <p class="text-lg text-slate-500 max-w-2xl mx-auto mb-8 leading-relaxed">
                Verifikasi lokasi, foto selfie, dan laporan otomatis — semua dalam satu sistem 
                yang sederhana dan transparan.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('auth.login') }}" class="btn-brand px-8 py-3.5 text-base">
                    <i class="bi bi-box-arrow-in-right"></i> Masuk
                </a>
                <a href="{{ route('auth.register') }}" class="btn-outline px-8 py-3.5 text-base">
                    <i class="bi bi-person-plus"></i> Daftar Akun
                </a>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-14 anim-up" style="animation-delay:.1s">
            <div class="card-sm text-center">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <i class="bi bi-people-fill text-blue-600 text-lg"></i>
                </div>
                <p class="stat-value text-slate-900">360</p>
                <p class="text-xs text-slate-500 font-medium">Siswa Aktif</p>
            </div>
            <div class="card-sm text-center">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <i class="bi bi-check-circle-fill text-emerald-600 text-lg"></i>
                </div>
                <p class="stat-value text-emerald-600">92%</p>
                <p class="text-xs text-slate-500 font-medium">Kehadiran</p>
            </div>
            <div class="card-sm text-center">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <i class="bi bi-person-badge-fill text-purple-600 text-lg"></i>
                </div>
                <p class="stat-value text-slate-900">42</p>
                <p class="text-xs text-slate-500 font-medium">Guru</p>
            </div>
            <div class="card-sm text-center">
                <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <i class="bi bi-diagram-3-fill text-orange-600 text-lg"></i>
                </div>
                <p class="stat-value text-slate-900">12</p>
                <p class="text-xs text-slate-500 font-medium">Kelas</p>
            </div>
        </div>

        <!-- Tata Tertib PDF Section -->
        <div class="card mb-14 anim-up" style="animation-delay:.15s">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 bg-red-100 rounded-xl flex items-center justify-center">
                        <i class="bi bi-file-earmark-pdf-fill text-red-500 text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Tata Tertib Sekolah</h2>
                        <p class="text-sm text-slate-500">Dokumen resmi terbaru</p>
                    </div>
                </div>
                <button onclick="togglePdfViewer()" id="btnTogglePdf" class="btn-outline !py-2 !px-4 text-sm">
                    <i class="bi bi-eye"></i> <span id="btnPdfText">Lihat PDF</span>
                </button>
            </div>
            
            <div id="pdfViewer" class="hidden">
                <div class="border-2 border-dashed border-slate-200 rounded-2xl p-10 text-center bg-gradient-to-b from-slate-50 to-white">
                    <div class="w-20 h-20 bg-red-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="bi bi-file-earmark-pdf text-red-400 text-3xl"></i>
                    </div>
                    <p class="text-lg font-bold text-slate-800">Tata Tertib SMA Negeri 11 Kab. Tangerang</p>
                    <p class="text-sm text-slate-500 mt-1 mb-5">Tahun Ajaran 2025/2026</p>
                    <div class="flex gap-3 justify-center">
                        <button class="btn-brand !py-2 !px-5 text-sm"><i class="bi bi-eye"></i> Fullscreen</button>
                        <button class="btn-outline !py-2 !px-5 text-sm"><i class="bi bi-download"></i> Unduh</button>
                    </div>
                </div>
            </div>

            <div id="pdfPlaceholder" class="border-2 border-dashed border-slate-200 rounded-2xl p-8 text-center bg-slate-50/50">
                <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <i class="bi bi-file-earmark-pdf text-slate-300 text-3xl"></i>
                </div>
                <p class="text-sm font-medium text-slate-500">PDF Tata Tertib akan ditampilkan di sini</p>
                <p class="text-xs text-slate-400 mt-1">Admin akan mengunggah dokumen resmi sekolah</p>
            </div>
        </div>

        <!-- Fitur Utama -->
        <div class="mb-14 anim-up" style="animation-delay:.2s">
            <div class="text-center mb-8">
                <p class="text-xs font-bold text-blue-600 uppercase tracking-widest mb-2">Fitur</p>
                <h2 class="section-title">Semua yang dibutuhkan</h2>
                <p class="text-sm text-slate-500 mt-2">Dirancang khusus untuk lingkungan sekolah</p>
            </div>
            
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <div class="card-sm hover:shadow-md transition-shadow group">
                    <div class="w-11 h-11 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i class="bi bi-geo-alt-fill text-blue-600 text-xl"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-1">Geofencing Presisi</h3>
                    <p class="text-sm text-slate-500">Validasi lokasi siswa hanya bisa dilakukan di area sekolah menggunakan polygon koordinat.</p>
                </div>
                <div class="card-sm hover:shadow-md transition-shadow group">
                    <div class="w-11 h-11 bg-gradient-to-br from-emerald-100 to-emerald-200 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i class="bi bi-camera-fill text-emerald-600 text-xl"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-1">Selfie Terverifikasi</h3>
                    <p class="text-sm text-slate-500">Foto selfie langsung tersimpan dan terhubung ke profil siswa sebagai bukti kehadiran.</p>
                </div>
                <div class="card-sm hover:shadow-md transition-shadow group">
                    <div class="w-11 h-11 bg-gradient-to-br from-violet-100 to-violet-200 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i class="bi bi-graph-up text-violet-600 text-xl"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-1">Laporan Real-time</h3>
                    <p class="text-sm text-slate-500">Rekap kehadiran harian, mingguan, dan bulanan tersedia secara otomatis untuk semua pihak.</p>
                </div>
                <div class="card-sm hover:shadow-md transition-shadow group">
                    <div class="w-11 h-11 bg-gradient-to-br from-amber-100 to-amber-200 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i class="bi bi-bell-fill text-amber-600 text-xl"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-1">Notifikasi WhatsApp</h3>
                    <p class="text-sm text-slate-500">Orang tua otomatis menerima laporan kehadiran anak melalui WhatsApp Gateway.</p>
                </div>
                <div class="card-sm hover:shadow-md transition-shadow group">
                    <div class="w-11 h-11 bg-gradient-to-br from-rose-100 to-rose-200 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i class="bi bi-shield-check text-rose-600 text-xl"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-1">Poin Pelanggaran</h3>
                    <p class="text-sm text-slate-500">Sistem poin pelanggaran dengan notifikasi mingguan ke orang tua via WhatsApp.</p>
                </div>
                <div class="card-sm hover:shadow-md transition-shadow group">
                    <div class="w-11 h-11 bg-gradient-to-br from-cyan-100 to-cyan-200 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i class="bi bi-file-earmark-spreadsheet text-cyan-600 text-xl"></i>
                    </div>
                    <h3 class="font-bold text-slate-900 mb-1">Master Data Terpusat</h3>
                    <p class="text-sm text-slate-500">Kelola data guru dan siswa, upload CSV/Excel, dan assign role dengan mudah.</p>
                </div>
            </div>
        </div>

        <!-- Role / Pengguna -->
        <div class="card bg-gradient-to-br from-slate-50 to-blue-50/50 anim-up" style="animation-delay:.25s">
            <div class="text-center mb-7">
                <p class="text-xs font-bold text-blue-600 uppercase tracking-widest mb-2">Akses</p>
                <h2 class="section-title">Pintu Masuk</h2>
                <p class="text-sm text-slate-500 mt-2">Pilih sesuai peran Anda</p>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                <a href="{{ route('auth.login') }}?role=kesiswaan" class="flex flex-col items-center gap-2 p-5 rounded-2xl border border-slate-200 bg-white hover:border-blue-300 hover:shadow-md hover:shadow-blue-500/5 transition-all group">
                    <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center group-hover:bg-blue-200 transition-colors mb-1">
                        <i class="bi bi-building-fill text-blue-600 text-xl"></i>
                    </div>
                    <p class="font-bold text-sm text-slate-800">Kesiswaan</p>
                    <p class="text-[11px] text-slate-500 text-center leading-tight">Super Admin<br>Master Data</p>
                </a>
                <a href="{{ route('auth.login') }}?role=bk" class="flex flex-col items-center gap-2 p-5 rounded-2xl border border-slate-200 bg-white hover:border-purple-300 hover:shadow-md hover:shadow-purple-500/5 transition-all group">
                    <div class="w-12 h-12 bg-purple-100 rounded-2xl flex items-center justify-center group-hover:bg-purple-200 transition-colors mb-1">
                        <i class="bi bi-heart-pulse-fill text-purple-600 text-xl"></i>
                    </div>
                    <p class="font-bold text-sm text-slate-800">BK</p>
                    <p class="text-[11px] text-slate-500 text-center leading-tight">Monitoring<br>Konseling</p>
                </a>
                <a href="{{ route('auth.login') }}?role=walikelas" class="flex flex-col items-center gap-2 p-5 rounded-2xl border border-slate-200 bg-white hover:border-emerald-300 hover:shadow-md hover:shadow-emerald-500/5 transition-all group">
                    <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center group-hover:bg-emerald-200 transition-colors mb-1">
                        <i class="bi bi-person-badge-fill text-emerald-600 text-xl"></i>
                    </div>
                    <p class="font-bold text-sm text-slate-800">Wali Kelas</p>
                    <p class="text-[11px] text-slate-500 text-center leading-tight">Presensi<br>Verifikasi</p>
                </a>
                <a href="{{ route('auth.login') }}?role=guru-mapel" class="flex flex-col items-center gap-2 p-5 rounded-2xl border border-slate-200 bg-white hover:border-amber-300 hover:shadow-md hover:shadow-amber-500/5 transition-all group">
                    <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center group-hover:bg-amber-200 transition-colors mb-1">
                        <i class="bi bi-book-fill text-amber-600 text-xl"></i>
                    </div>
                    <p class="font-bold text-sm text-slate-800">Guru Mapel</p>
                    <p class="text-[11px] text-slate-500 text-center leading-tight">Presensi<br>Kelas</p>
                </a>
                <a href="{{ route('auth.login') }}?role=siswa" class="flex flex-col items-center gap-2 p-5 rounded-2xl border border-slate-200 bg-white hover:border-rose-300 hover:shadow-md hover:shadow-rose-500/5 transition-all group">
                    <div class="w-12 h-12 bg-rose-100 rounded-2xl flex items-center justify-center group-hover:bg-rose-200 transition-colors mb-1">
                        <i class="bi bi-mortarboard-fill text-rose-600 text-xl"></i>
                    </div>
                    <p class="font-bold text-sm text-slate-800">Siswa</p>
                    <p class="text-[11px] text-slate-500 text-center leading-tight">Presensi<br>Selfie GPS</p>
                </a>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
function togglePdfViewer() {
    const viewer = document.getElementById('pdfViewer');
    const placeholder = document.getElementById('pdfPlaceholder');
    const btnText = document.getElementById('btnPdfText');
    if (viewer.classList.contains('hidden')) {
        viewer.classList.remove('hidden');
        placeholder.classList.add('hidden');
        btnText.textContent = 'Sembunyikan PDF';
    } else {
        viewer.classList.add('hidden');
        placeholder.classList.remove('hidden');
        btnText.textContent = 'Lihat PDF';
    }
}
</script>
@endpush