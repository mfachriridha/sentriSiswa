@include('partials.head')
<body class="relative min-h-screen overflow-x-hidden bg-slate-50 font-sans antialiased flex items-center justify-center py-12 px-4">
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-red-500/5 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-slate-500/5 blur-3xl"></div>
    </div>

    <div class="relative z-10 w-full max-w-md mx-auto text-center">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-10">
            <div class="w-16 h-16 bg-red-100 rounded-2xl flex items-center justify-center mx-auto mb-5">
                <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-800 mb-2">Link Kedaluwarsa</h1>
            <p class="text-slate-500 text-sm leading-relaxed">
                Link akses absensi ini sudah tidak berlaku. Link hanya dapat digunakan pada hari yang sama dengan pengiriman laporan.
            </p>
            <p class="text-slate-400 text-xs mt-6">
                Hubungi wali kelas atau pihak sekolah untuk informasi absensi.
            </p>
        </div>
    </div>
</body>
</html>
