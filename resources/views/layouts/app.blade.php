<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SentriSiswa')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <style>
            :root{--brand:#1c6880;--brand-light:#d4e8ee;--brand-dark:#155066;--secondary:#0062a0;--surface:#fff;--surface-alt:#f8fafc;--border:#c3c6d1;--text:#191c1d;--text-muted:#43474f}
            *{box-sizing:border-box}
            body{font-family:'Inter',system-ui,sans-serif;background:#f8f9fa;color:var(--text)}
            .text-muted{color:var(--text-muted)}
            .btn-brand,.btn-primary{background:var(--brand);color:#fff;border-radius:.5rem;padding:.625rem 1.25rem;font-weight:700;font-size:.8125rem;display:inline-flex;align-items:center;gap:.375rem;border:1.5px solid transparent;transition:all .15s;box-shadow:0 1px 3px rgba(0,0,0,.08)}
            .btn-brand:hover,.btn-primary:hover{background:var(--brand-dark);transform:translateY(-1px)}
            .btn-outline{background:transparent;color:var(--secondary);border-radius:.5rem;padding:.625rem 1.25rem;font-weight:600;font-size:.8125rem;border:1.5px solid var(--border);display:inline-flex;align-items:center;gap:.375rem;transition:all .15s}
            .btn-outline:hover{background:var(--brand-light);border-color:var(--secondary)}
            .btn-ghost{background:transparent;color:var(--text-muted);border-radius:.5rem;padding:.5rem 1rem;font-weight:600;font-size:.8125rem;display:inline-flex;align-items:center;gap:.375rem;transition:all .15s}
            .btn-ghost:hover{background:var(--surface-alt);color:var(--text)}
            .btn-danger{background:#ba1a1a;color:#fff;border-radius:.5rem;padding:.625rem 1.25rem;font-weight:600;font-size:.8125rem;transition:all .15s}
            .card{background:var(--surface);border:1px solid var(--border);border-radius:1rem;padding:1.5rem}
            .input-field{width:100%;border:1.5px solid var(--border);border-radius:.5rem;padding:.625rem .875rem;outline:none;font-size:.875rem;transition:all .15s;background:var(--surface);color:var(--text)}
            .input-field:focus{border-color:var(--secondary);box-shadow:0 0 0 3px rgba(0,98,160,.08)}
            .table-container{overflow:auto;border:1px solid var(--border);border-radius:.75rem;background:var(--surface)}
            .table-header{padding:.75rem 1rem;background:var(--surface-alt);color:var(--text-muted);font-size:.6875rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;border-bottom:1px solid var(--border)}
            .table-cell{padding:.75rem 1rem;font-size:.8125rem;color:var(--text);border-bottom:1px solid var(--border);border-right:1px solid var(--border)}.table-cell:last-child{border-right:none}.table-cell-aksi{white-space:nowrap;text-align:center;padding:.75rem 1rem;font-size:.8125rem;color:var(--text);border-bottom:1px solid var(--border);border-right:1px solid var(--border)}.table-cell-aksi:last-child{border-right:none}
            .badge{display:inline-flex;align-items:center;border-radius:9999px;padding:.15rem .625rem;font-size:.6875rem;font-weight:700}
            .badge-green{background:#dcfce7;color:#15803d}.badge-amber{background:#fef3c7;color:#a16207}.badge-red{background:#fee2e2;color:#ba1a1a}.badge-blue{background:var(--brand-light);color:var(--brand-dark)}
            .stat-value{font-size:1.75rem;font-weight:800;line-height:1.1;letter-spacing:-.02em}
            @keyframes slideUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
            .anim-up{animation:slideUp .4s ease both}
            .anim-up:nth-child(2){animation-delay:.08s}.anim-up:nth-child(3){animation-delay:.16s}.anim-up:nth-child(4){animation-delay:.24s}
            .sidebar-link{display:flex;align-items:center;gap:.625rem;border-radius:.5rem;padding:.5rem .75rem;font-size:.8125rem;font-weight:600;transition:all .12s;color:var(--text)}
            .sidebar-link:hover{background:var(--brand-light)}
            .sidebar-link.active{background:var(--brand);color:#fff}
        </style>
    @endif
    @stack('styles')
</head>
<body class="bg-[#f8f9fa] text-[#191c1d] antialiased">
    <?php
    $user = Auth::user();
    $currentRole = $user?->role ?? 'siswa';
    $userName = $user?->name ?? 'Pengguna';

    $roleLabels = [
        'bk' => 'BK', 'wali_kelas' => 'Wali Kelas',
        'siswa' => 'Siswa', 'admin' => 'Admin'
    ];
    $roleLabel = $roleLabels[$currentRole] ?? 'Admin';

    $dashboardRoute = match ($currentRole) {
        'admin' => route('admin.dashboard'),
        'wali_kelas' => route('wali-kelas.dashboard'),
        'bk' => route('bk.dashboard'),
        default => route('siswa.dashboard'),
    };

    $profileRoute = match ($currentRole) {
        'admin' => route('admin.profile'),
        'wali_kelas' => route('wali-kelas.dashboard'),
        'bk' => route('bk.dashboard'),
        default => route('siswa.pengaturan'),
    };
    ?>

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 border-r border-[#c3c6d1]/30 bg-white -translate-x-full lg:translate-x-0 lg:relative transition-transform duration-300 flex flex-col">
            <div class="p-5 border-b border-[#c3c6d1]/30">
                <a href="{{ $dashboardRoute }}" class="flex items-center gap-2.5">
                    <span class="w-9 h-9 bg-[#1c6880] rounded-xl flex items-center justify-center text-white shadow-sm">
                        <i class="bi bi-mortarboard-fill text-lg"></i>
                    </span>
                    <div>
                        <p class="font-extrabold text-sm text-[#1c6880] tracking-tight">SentriSiswa</p>
                        <p class="text-[10px] text-muted uppercase tracking-wider">SMA N 11 Kab. Tangerang</p>
                    </div>
                </a>
            </div>

            <nav class="flex-1 overflow-y-auto p-3 space-y-0.5">
                <p class="px-3 py-2 text-[10px] font-bold text-muted uppercase tracking-widest">Menu</p>

                @if($currentRole === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="bi bi-grid"></i> Dashboard</a>
                    <a href="{{ route('admin.guru') }}" class="sidebar-link {{ request()->routeIs('admin.guru') ? 'active' : '' }}"><i class="bi bi-people"></i> Manajemen Guru</a>
                    <a href="{{ route('admin.siswa') }}" class="sidebar-link {{ request()->routeIs('admin.siswa') ? 'active' : '' }}"><i class="bi bi-mortarboard"></i> Manajemen Siswa</a>
                    <a href="{{ route('admin.kelas') }}" class="sidebar-link {{ request()->routeIs('admin.kelas') ? 'active' : '' }}"><i class="bi bi-diagram-3"></i> Manajemen Kelas</a>
                    <a href="{{ route('admin.tata-tertib') }}" class="sidebar-link {{ request()->routeIs('admin.tata-tertib') ? 'active' : '' }}"><i class="bi bi-file-pdf"></i> Tata Tertib</a>
                    <a href="{{ route('admin.poin') }}" class="sidebar-link {{ request()->routeIs('admin.poin') ? 'active' : '' }}"><i class="bi bi-exclamation-triangle"></i> Poin Pelanggaran</a>
                    <a href="{{ route('admin.laporan') }}" class="sidebar-link {{ request()->routeIs('admin.laporan') ? 'active' : '' }}"><i class="bi bi-bar-chart"></i> Laporan</a>
                    <a href="{{ route('admin.integrasi') }}" class="sidebar-link {{ request()->routeIs('admin.integrasi') ? 'active' : '' }}"><i class="bi bi-plug"></i> Integrasi</a>
                @elseif($currentRole === 'wali_kelas')
                    <a href="{{ route('wali-kelas.dashboard') }}" class="sidebar-link {{ request()->routeIs('wali-kelas.dashboard') ? 'active' : '' }}"><i class="bi bi-grid"></i> Dashboard</a>
                    <a href="#" class="sidebar-link opacity-50"><i class="bi bi-camera"></i> Verifikasi Selfie</a>
                    <a href="#" class="sidebar-link opacity-50"><i class="bi bi-bar-chart"></i> Rekap Kehadiran</a>
                @elseif($currentRole === 'bk')
                    <a href="{{ route('bk.dashboard') }}" class="sidebar-link {{ request()->routeIs('bk.dashboard') ? 'active' : '' }}"><i class="bi bi-grid"></i> Dashboard</a>
                    <a href="#" class="sidebar-link opacity-50"><i class="bi bi-eye"></i> Monitoring Kelas</a>
                    <a href="#" class="sidebar-link opacity-50"><i class="bi bi-bar-chart"></i> Rekap Kehadiran</a>
                @elseif($currentRole === 'siswa')
                    <a href="{{ route('siswa.dashboard') }}" class="sidebar-link {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}"><i class="bi bi-grid"></i> Dashboard</a>
                    <a href="{{ route('siswa.kehadiran') }}" class="sidebar-link {{ request()->routeIs('siswa.kehadiran') ? 'active' : '' }}"><i class="bi bi-camera"></i> Kehadiran</a>
                    <a href="{{ route('siswa.riwayat') }}" class="sidebar-link {{ request()->routeIs('siswa.riwayat') ? 'active' : '' }}"><i class="bi bi-clock-history"></i> Riwayat Kehadiran</a>
                    <a href="{{ route('siswa.poin') }}" class="sidebar-link {{ request()->routeIs('siswa.poin') ? 'active' : '' }}"><i class="bi bi-star"></i> Poin Saya</a>
                @endif

                <p class="px-3 py-2 text-[10px] font-bold text-muted uppercase tracking-widest pt-2">Akun</p>
                @if($currentRole === 'admin')
                <a href="{{ route('admin.profile') }}" class="sidebar-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}"><i class="bi bi-person-circle"></i> Profil</a>
                @elseif($currentRole === 'siswa')
                <a href="{{ route('siswa.pengaturan') }}" class="sidebar-link {{ request()->routeIs('siswa.pengaturan') ? 'active' : '' }}"><i class="bi bi-gear"></i> Pengaturan</a>
                @endif
                <a href="{{ route('auth.logout') }}" class="sidebar-link text-[#ba1a1a] hover:bg-[#ffdad6]"><i class="bi bi-box-arrow-right"></i> Keluar</a>
            </nav>
        </aside>

        <div id="sidebar-overlay" class="fixed inset-0 z-40 bg-black/20 lg:hidden hidden"></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <header class="bg-white border-b border-[#c3c6d1]/30 h-14 flex items-center justify-between px-2 sm:px-4 shrink-0">
                <div class="flex items-center gap-2 min-w-0">
                    <button id="toggle-sidebar" class="lg:hidden p-1.5 hover:bg-[#edeeef] rounded-lg shrink-0">
                        <i class="bi bi-list text-xl text-[#43474f]"></i>
                    </button>
                    <span class="font-bold text-sm sm:text-base text-[#1c6880] truncate">@yield('page-title', now()->translatedFormat('l, d F Y'))</span>
                </div>

                <div id="avatar-btn" class="flex items-center gap-2 sm:gap-3 shrink-0 cursor-pointer hover:bg-[#edeeef] rounded-lg px-2 py-1.5 transition">
                    <div class="text-right">
                        <p class="text-xs sm:text-sm font-bold text-[#191c1d] truncate max-w-24 sm:max-w-none">{{ $userName }}</p>
                        <p class="text-[10px] sm:text-[11px] text-muted uppercase tracking-wider">{{ $roleLabel }}</p>
                    </div>
                    <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-[#1c6880] flex items-center justify-center shrink-0">
                        <i class="bi bi-person-fill text-white text-sm sm:text-base"></i>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-3 lg:p-4">
                <div>
                    @yield('content')
                </div>
            </main>

            <footer class="bg-white border-t border-[#c3c6d1]/30 py-2 sm:py-3 px-3 sm:px-6 text-center text-[10px] sm:text-[11px] text-muted">
                &copy; {{ date('Y') }} <span class="font-semibold text-[#1c6880]">SentriSiswa</span> &mdash; SMA Negeri 11 Kab. Tangerang
            </footer>
        </div>
    </div>

    <!-- Avatar Dropdown -->
    <div id="avatar-dropdown" class="fixed z-[100] w-44 bg-white border border-[#c3c6d1]/30 rounded-lg shadow-lg py-1" style="display:none">
        <a href="{{ $profileRoute }}" class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-[#191c1d] hover:bg-[#d4e8ee] transition">
            <i class="bi bi-gear"></i> {{ $currentRole === 'admin' ? 'Profil' : 'Pengaturan' }}
        </a>
        <hr class="border-[#c3c6d1]/30 mx-3">
        <a href="{{ route('auth.logout') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-[#ba1a1a] hover:bg-[#ffdad6] transition">
            <i class="bi bi-box-arrow-right"></i> Keluar
        </a>
    </div>

    <script>
        // Sidebar toggle
        function t(e,i){document.getElementById(e)?.classList.toggle(i)}
        document.getElementById('toggle-sidebar')?.addEventListener('click',()=>{t('sidebar','-translate-x-full');t('sidebar-overlay','hidden')});
        document.getElementById('sidebar-overlay')?.addEventListener('click',()=>{document.getElementById('sidebar')?.classList.add('-translate-x-full');document.getElementById('sidebar-overlay')?.classList.add('hidden')});
        // Avatar dropdown
        let dd=document.getElementById('avatar-dropdown'),ab=document.getElementById('avatar-btn');
        if(ab&&dd){
            ab.addEventListener('click',function(e){
                e.stopPropagation();
                let r=ab.getBoundingClientRect();
                dd.style.top=(r.bottom+6)+'px';dd.style.right=(window.innerWidth-r.right)+'px';
                dd.style.display=dd.style.display==='none'?'block':'none';
            });
            document.addEventListener('click',function(){dd.style.display='none'});
            dd.addEventListener('click',function(e){e.stopPropagation()});
        }
    </script>
    @include('components.alert-toast')
    @include('components.loading-overlay')
    @stack('scripts')
</body>
</html>