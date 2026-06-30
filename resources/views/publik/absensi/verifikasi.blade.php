@include('partials.head')
<body class="relative min-h-screen overflow-x-hidden bg-slate-50 font-sans antialiased flex items-center justify-center py-12 px-4">
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-primary/10 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-teal-500/5 blur-3xl"></div>
    </div>

    <div class="relative z-10 w-full max-w-md mx-auto">
        <div class="bg-white rounded-2xl shadow-xl border border-slate-100 p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-slate-800">Cek Absensi Siswa</html>
                <p class="text-slate-500 text-sm mt-1">Masukkan data siswa untuk melihat absensi hari ini</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('absensi.publik.cek', $token) }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">NIS atau NISN Siswa</label>
                    <input type="text" name="nis_nisn" value="{{ old('nis_nisn') }}" required
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                           placeholder="Masukkan NIS atau NISN">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Lengkap Siswa</label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary transition"
                           placeholder="Nama lengkap sesuai data sekolah">
                </div>
                <button type="submit"
                        class="w-full py-3 px-6 bg-primary text-white font-bold rounded-xl hover:bg-primary/90 transition active:scale-95 text-sm">
                    Lihat Absensi
                </button>
            </form>

            <p class="text-center text-xs text-slate-400 mt-6">
                Link ini hanya berlaku sampai akhir hari ini.
            </p>
        </div>
    </div>
</body>
</html>
