<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Sentri Siswa')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <style>
            .btn-primary{background:#f04916;color:#fff;border-radius:9999px;padding:.625rem 1.25rem;font-weight:600;display:inline-flex;align-items:center;gap:.5rem}
            .btn-secondary{background:#fff;color:#0f172a;border-radius:9999px;padding:.625rem 1.25rem;font-weight:600;border:1px solid rgba(148,163,184,.7);display:inline-flex;align-items:center;gap:.5rem}
            .btn-outline{background:rgba(255,255,255,.1);color:#fff;border-radius:9999px;padding:.625rem 1.25rem;font-weight:600;border:1px solid rgba(255,255,255,.3);display:inline-flex;align-items:center;gap:.5rem}
            .btn-ghost{background:transparent;color:#475569;border-radius:9999px;padding:.5rem 1rem;font-weight:600;display:inline-flex;align-items:center;gap:.5rem}
            .btn-danger{background:#dc2626;color:#fff;border-radius:.75rem;padding:.625rem 1rem;font-weight:600}
            .card{background:rgba(255,255,255,.9);border:1px solid rgba(226,232,240,.7);border-radius:1.5rem;padding:1.5rem;box-shadow:0 10px 30px -24px rgba(15,23,42,.5)}
            .glass-panel{background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);border-radius:1.5rem;padding:1.5rem;backdrop-filter:blur(16px);box-shadow:0 20px 60px -40px rgba(8,15,30,.9)}
            .stat-card{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:1.25rem;padding:1rem}
            .feature-card{background:#fff;border:1px solid rgba(226,232,240,.7);border-radius:1.25rem;padding:1.5rem;box-shadow:0 2px 8px rgba(15,23,42,.06)}
            .chip{display:inline-flex;align-items:center;gap:.5rem;border-radius:9999px;padding:.25rem .75rem;border:1px solid rgba(255,255,255,.15);background:rgba(255,255,255,.1);font-size:.6875rem;font-weight:600;text-transform:uppercase;letter-spacing:.28em;color:#fff}
            .font-display{font-family:'Space Grotesk','IBM Plex Sans',system-ui,sans-serif;letter-spacing:-.02em}
            .badge-success{background:#dcfce7;color:#166534;border-radius:9999px;padding:.125rem .625rem;font-size:.75rem;font-weight:600}
            .badge-warning{background:#fef3c7;color:#92400e;border-radius:9999px;padding:.125rem .625rem;font-size:.75rem;font-weight:600}
            .badge-danger{background:#fee2e2;color:#991b1b;border-radius:9999px;padding:.125rem .625rem;font-size:.75rem;font-weight:600}
            .badge-info{background:#dbeafe;color:#1e40af;border-radius:9999px;padding:.125rem .625rem;font-size:.75rem;font-weight:600}
            .input-field{width:100%;border:1px solid #cbd5e1;border-radius:.75rem;padding:.625rem .875rem;outline:none}
            .table-container{overflow:auto;border:1px solid #e2e8f0;border-radius:1rem;background:#fff}
            .table-header{padding:.875rem 1rem;background:#f8fafc;color:#475569;font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;text-align:left}
            .table-cell{padding:.875rem 1rem;font-size:.875rem;color:#0f172a}
            @keyframes rise{0%{opacity:0;transform:translateY(18px)}100%{opacity:1;transform:translateY(0)}}
            @keyframes float{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}
            .reveal-up{animation:rise .8s ease both}
            .float-slow{animation:float 8s ease-in-out infinite}
            .delay-1{animation-delay:.1s}
            .delay-2{animation-delay:.2s}
            .delay-3{animation-delay:.3s}
        </style>
    @endif

    @stack('styles')
</head>
<body class="bg-slate-100 text-slate-900 antialiased">

    <div class="flex h-screen overflow-hidden">
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 border-r border-slate-800/60 bg-slate-950 text-white transform -translate-x-full lg:translate-x-0 lg:relative transition-transform duration-300 ease-in-out">
            <div class="p-6">
                <h1 class="text-xl font-bold flex items-center gap-2">
                    <i class="bi bi-shield-check text-blue-400"></i>
                    <span>Sentri Siswa</span>
                </h1>
                <p class="text-xs text-slate-400 mt-1 uppercase tracking-widest">SMAN 11 Kab. Tangerang</p>
            </div>

            <nav class="mt-4 px-4 space-y-2">
                <p class="px-4 text-[11px] font-semibold uppercase tracking-widest text-slate-500">Navigasi Cepat</p>

                <a href="{{ route('kesiswaan.dashboard') }}" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition {{ request()->routeIs('kesiswaan.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="bi bi-building"></i> Kesiswaan
                </a>
                <a href="{{ route('walikelas.dashboard') }}" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition {{ request()->routeIs('walikelas.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="bi bi-person-badge"></i> Wali Kelas
                </a>
                <a href="{{ route('guru-mapel.dashboard') }}" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition {{ request()->routeIs('guru-mapel.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="bi bi-journal-bookmark"></i> Guru Mapel
                </a>
                <a href="{{ route('bk.dashboard') }}" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition {{ request()->routeIs('bk.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="bi bi-activity"></i> BK
                </a>
                <a href="{{ route('siswa.dashboard') }}" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition {{ request()->routeIs('siswa.*') ? 'bg-blue-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="bi bi-mortarboard"></i> Siswa
                </a>

                <p class="pt-4 px-4 text-[11px] font-semibold uppercase tracking-widest text-slate-500">Profil</p>
                <a href="{{ route('profile.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition {{ request()->routeIs('profile.*') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                    <i class="bi bi-person-circle"></i> Data Profil
                </a>
                <a href="{{ route('landing') }}" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm text-rose-300 transition hover:bg-rose-500/10 hover:text-rose-200">
                    <i class="bi bi-box-arrow-right"></i> Kembali ke Landing
                </a>
            </nav>
        </aside>

        <div id="sidebar-overlay" class="fixed inset-0 z-40 hidden bg-slate-950/40 lg:hidden"></div>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-4 lg:px-8 shrink-0">
                <button id="toggle-sidebar" class="lg:hidden p-2 rounded-md hover:bg-gray-100 transition">
                    <i class="bi bi-list text-2xl"></i>
                </button>

                <div class="hidden lg:block text-sm text-slate-500 font-medium">
                    Hari ini: <span class="text-slate-800">{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-semibold text-slate-800">Pengguna Demo</p>
                        <p class="text-xs text-slate-500 uppercase">Sentri Siswa</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center border border-blue-200">
                        <i class="bi bi-person text-blue-600"></i>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 lg:p-8 bg-gradient-to-br from-slate-50 via-white to-blue-50">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>

            <footer class="bg-white border-t border-slate-200 py-4 px-8 text-center text-xs text-slate-500">
                &copy; {{ date('Y') }} Sentri Siswa
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
