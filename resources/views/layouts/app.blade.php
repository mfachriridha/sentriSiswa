<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SentriSiswa')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @if (file_exists(public_path('build/manifest.json'))))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <style>
            :root {
                --brand: #2563eb; --brand-light: #dbeafe; --brand-dark: #1d4ed8;
                --surface: #ffffff; --surface-alt: #f8fafc; --border: #e2e8f0;
                --text: #0f172a; --text-muted: #64748b;
                --radius: .75rem; --radius-lg: 1rem; --radius-xl: 1.25rem;
                --shadow-sm: 0 1px 2px rgba(0,0,0,.04);
                --shadow: 0 1px 3px rgba(0,0,0,.08);
                --shadow-md: 0 4px 6px -1px rgba(0,0,0,.07);
            }
            *{box-sizing:border-box}
            body{font-family:'Plus Jakarta Sans',system-ui,sans-serif}
            .btn-brand{background:var(--brand);color:#fff;border-radius:var(--radius);padding:.625rem 1.25rem;font-weight:600;font-size:.8125rem;display:inline-flex;align-items:center;gap:.375rem;transition:all .15s;box-shadow:0 1px 2px rgba(37,99,235,.2)}
            .btn-brand:hover{background:var(--brand-dark);box-shadow:0 2px 4px rgba(37,99,235,.25)}
            .btn-outline{background:transparent;color:var(--text);border-radius:var(--radius);padding:.625rem 1.25rem;font-weight:600;font-size:.8125rem;border:1.5px solid var(--border);display:inline-flex;align-items:center;gap:.375rem;transition:all .15s}
            .btn-outline:hover{background:var(--surface-alt);border-color:#cbd5e1}
            .btn-danger{background:#ef4444;color:#fff;border-radius:var(--radius);padding:.625rem 1.25rem;font-weight:600;font-size:.8125rem;transition:all .15s}
            .btn-danger:hover{background:#dc2626}
            .card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-xl);padding:1.5rem;box-shadow:var(--shadow)}
            .card-hover:hover{border-color:var(--brand);box-shadow:var(--shadow-md)}
            .input-field{width:100%;border:1.5px solid var(--border);border-radius:var(--radius);padding:.625rem .875rem;outline:none;font-size:.8125rem;transition:all .15s;background:var(--surface)}
            .input-field:focus{border-color:var(--brand);box-shadow:0 0 0 3px rgba(37,99,235,.08)}
            .table-container{overflow:auto;border:1px solid var(--border);border-radius:var(--radius-lg);background:var(--surface)}
            .table-header{padding:.75rem 1rem;background:var(--surface-alt);color:var(--text-muted);font-size:.6875rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;border-bottom:1px solid var(--border)}
            .table-cell{padding:.75rem 1rem;font-size:.8125rem;color:var(--text);border-bottom:1px solid #f1f5f9}
            .badge{display:inline-flex;align-items:center;border-radius:9999px;padding:.15rem .625rem;font-size:.6875rem;font-weight:700}
            .badge-green{background:#dcfce7;color:#15803d}.badge-amber{background:#fef3c7;color:#a16207}
            .badge-red{background:#fee2e2;color:#dc2626}.badge-blue{background:var(--brand-light);color:var(--brand-dark)}
            .stat-value{font-size:1.75rem;font-weight:800;line-height:1.1;letter-spacing:-.02em}
            @keyframes slideUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
            .anim-up{animation:slideUp .4s ease both}
            .anim-up:nth-child(2){animation-delay:.08s}.anim-up:nth-child(3){animation-delay:.16s}.anim-up:nth-child(4){animation-delay:.24s}
            .sidebar-link{display:flex;align-items:center;gap:.625rem;border-radius:var(--radius);padding:.5rem .75rem;font-size:.8125rem;font-weight:600;transition:all .12s}
            .sidebar-link:hover{background:#f1f5f9}
            .sidebar-link.active{background:var(--brand);color:#fff}
        </style>
    @endif
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    <?php
    $currentRole = Session::get('user_role', request()->query('role', 'kesiswaan'));
    $userName = Session::get('user_name', 'Admin Kesiswaan');

    $roleLabels = [
        'kesiswaan' => 'Kesiswaan', 'bk' => 'BK', 'walikelas' => 'Wali Kelas',
        'guru-mapel' => 'Guru Mapel', 'siswa' => 'Siswa'
    ];
    $roleColors = [
        'kesiswaan' => 'blue', 'bk' => 'purple', 'walikelas' => 'emerald',
        'guru-mapel' => 'amber', 'siswa' => 'rose'
    ];
    $roleLabel = $roleLabels[$currentRole] ?? 'Kesiswaan';
    $roleColor = $roleColors[$currentRole] ?? 'blue';
    ?>

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 border-r bg-white -translate-x-full lg:translate-x-0 lg:relative transition-transform duration-300 flex flex-col">
            <div class="p-5 border-b border-slate-100">
                <a href="{{ route('landing') }}" class="flex items-center gap-2.5">
                    <span class="w-9 h-9 bg-gradient-to-br from-{{ $roleColor }}-600 to-{{ $roleColor }}-700 rounded-xl flex items-center justify-center text-white shadow-md">
                        <i class="bi bi-mortarboard-fill text-lg"></i>
                    </span>
                    <div>
                        <p class="font-extrabold text-sm tracking-tight">SentriSiswa</p>
                        <p class="text-[10px] text-slate-400 uppercase tracking-wider">SMA N 11 Kab. Tangerang</p>
                    </div>
                </a>
            </div>

            <nav class="flex-1 overflow-y-auto p-3 space-y-0.5">
                <p class="px-3 py-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Menu</p>

                @if($currentRole === 'kesiswaan')
                    <a href="{{ route('kesiswaan.dashboard') }}" class="sidebar-link {{ request()->routeIs('kesiswaan.*') ? 'active' : 'text-slate-700' }}"><i class="bi bi-grid"></i> Dashboard</a>
                    <a href="{{ route('kesiswaan.master-data') }}" class="sidebar-link {{ request()->routeIs('kesiswaan.master-data') ? 'active' : 'text-slate-700' }}"><i class="bi bi-database"></i> Master Data</a>
                    <a href="{{ route('kesiswaan.manajemen-role') }}" class="sidebar-link {{ request()->routeIs('kesiswaan.manajemen-role') ? 'active' : 'text-slate-700' }}"><i class="bi bi-person-gear"></i> Manajemen Role</a>
                    <a href="#" class="sidebar-link text-slate-700"><i class="bi bi-diagram-3"></i> Manajemen Kelas</a>
                    <a href="{{ route('kesiswaan.poin-pelanggaran') }}" class="sidebar-link {{ request()->routeIs('kesiswaan.poin-pelanggaran') ? 'active' : 'text-slate-700' }}"><i class="bi bi-exclamation-triangle"></i> Poin Pelanggaran</a>
                    <a href="#" class="sidebar-link text-slate-700"><i class="bi bi-file-pdf"></i> Upload Tata Tertib</a>

                @elseif($currentRole === 'walikelas')
                    <a href="{{ route('walikelas.dashboard') }}" class="sidebar-link {{ request()->routeIs('walikelas.dashboard') ? 'active' : 'text-slate-700' }}"><i class="bi bi-grid"></i> Dashboard</a>
                    <a href="#" class="sidebar-link text-slate-700"><i class="bi bi-camera"></i> Presensi Masuk</a>
                    <a href="#" class="sidebar-link text-slate-700"><i class="bi bi-box-arrow-left"></i> Presensi Pulang</a>
                    <a href="{{ route('walikelas.monitoring') }}" class="sidebar-link {{ request()->routeIs('walikelas.monitoring') ? 'active' : 'text-slate-700' }}"><i class="bi bi-clock-history"></i> Riwayat Presensi</a>

                @elseif($currentRole === 'guru-mapel')
                    <a href="{{ route('guru-mapel.dashboard') }}" class="sidebar-link {{ request()->routeIs('guru-mapel.*') ? 'active' : 'text-slate-700' }}"><i class="bi bi-grid"></i> Dashboard</a>
                    <a href="#" class="sidebar-link text-slate-700"><i class="bi bi-calendar-check"></i> Presensi Kelas</a>
                    <a href="#" class="sidebar-link text-slate-700"><i class="bi bi-clock-history"></i> Riwayat Presensi</a>

                @elseif($currentRole === 'bk')
                    <a href="{{ route('bk.dashboard') }}" class="sidebar-link {{ request()->routeIs('bk.dashboard') ? 'active' : 'text-slate-700' }}"><i class="bi bi-grid"></i> Dashboard</a>
                    <a href="{{ route('bk.monitoring') }}" class="sidebar-link {{ request()->routeIs('bk.monitoring') ? 'active' : 'text-slate-700' }}"><i class="bi bi-eye"></i> Monitoring</a>
                    <a href="{{ route('bk.rekap-presensi') }}" class="sidebar-link {{ request()->routeIs('bk.rekap-presensi') ? 'active' : 'text-slate-700' }}"><i class="bi bi-bar-chart"></i> Rekap Presensi</a>
                    <a href="#" class="sidebar-link text-slate-700"><i class="bi bi-exclamation-triangle"></i> Poin Pelanggaran</a>

                @elseif($currentRole === 'siswa')
                    <a href="{{ route('siswa.dashboard') }}" class="sidebar-link {{ request()->routeIs('siswa.dashboard') ? 'active' : 'text-slate-700' }}"><i class="bi bi-grid"></i> Dashboard</a>
                    <a href="{{ route('siswa.dashboard') }}" class="sidebar-link text-slate-700"><i class="bi bi-camera"></i> Presensi</a>
                    <a href="#" class="sidebar-link text-slate-700"><i class="bi bi-bar-chart"></i> Poin Saya</a>
                @endif

                <p class="px-3 py-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest pt-2">Akun</p>
                <a href="{{ route('profile.index') }}" class="sidebar-link text-slate-700"><i class="bi bi-person-circle"></i> Profil</a>
                <a href="{{ route('landing') }}" class="sidebar-link text-rose-600 hover:bg-rose-50"><i class="bi bi-box-arrow-right"></i> Keluar</a>
            </nav>
        </aside>

        <div id="sidebar-overlay" class="fixed inset-0 z-40 bg-slate-900/20 lg:hidden hidden" onclick="closeSidebar()"></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white border-b border-slate-100 h-16 flex items-center justify-between px-4 lg:px-6 shrink-0">
                <button id="toggle-sidebar" class="lg:hidden p-2 hover:bg-slate-100 rounded-lg">
                    <i class="bi bi-list text-2xl text-slate-700"></i>
                </button>

                <div class="hidden lg:flex items-center gap-2 text-sm text-slate-500">
                    <i class="bi bi-calendar3 text-blue-500"></i>
                    <span class="font-medium">{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>

                <div class="flex items-center gap-3">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-slate-800">{{ $userName }}</p>
                        <p class="text-[11px] text-slate-400 uppercase tracking-wider">{{ $roleLabel }}</p>
                    </div>
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-{{ $roleColor }}-100 to-{{ $roleColor }}-200 flex items-center justify-center">
                        <i class="bi bi-person-fill text-{{ $roleColor }}-600"></i>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-5 lg:p-7 bg-slate-50">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>

            <footer class="bg-white border-t border-slate-100 py-3 px-6 text-center text-[11px] text-slate-400">
                &copy; {{ date('Y') }} <span class="font-semibold text-slate-600">SentriSiswa</span> &mdash; SMA Negeri 11 Kab. Tangerang
            </footer>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        document.getElementById('toggle-sidebar')?.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full'); overlay.classList.toggle('hidden');
        });
        function closeSidebar(){ sidebar.classList.add('-translate-x-full'); overlay.classList.add('hidden'); }
    </script>
    @stack('scripts')
</body>
</html>