<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sentri Siswa')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @if (file_exists(public_path('build/manifest.json'))))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <style>
            :root {
                --primary: #001e40;
                --on-primary: #ffffff;
                --primary-container: #003366;
                --primary-fixed: #d5e3ff;
                --secondary: #0062a0;
                --on-secondary: #ffffff;
                --secondary-container: #32a3fd;
                --secondary-fixed: #d0e4ff;
                --surface: #ffffff;
                --surface-dim: #d9dadb;
                --surface-container: #edeeef;
                --surface-container-high: #e7e8e9;
                --surface-container-highest: #e1e3e4;
                --outline: #737780;
                --outline-variant: #c3c6d1;
                --on-surface: #191c1d;
                --on-surface-variant: #43474f;
                --error: #ba1a1a;
                --error-container: #ffdad6;
                --bg: #f8f9fa;
                --radius: 0.5rem;
                --radius-lg: 1rem;
                --shadow: 0 1px 3px rgba(0,0,0,.08);
                --shadow-md: 0 4px 12px rgba(0,0,0,.06);
            }
            *{box-sizing:border-box}
            body{font-family:'Inter',system-ui,sans-serif}
            .btn-primary{background:var(--primary);color:#fff;border-radius:var(--radius);padding:.625rem 1.25rem;font-weight:700;font-size:.875rem;display:inline-flex;align-items:center;gap:.5rem;transition:all .15s;box-shadow:var(--shadow)}
            .btn-primary:hover{background:#002856;box-shadow:var(--shadow-md)}
            .btn-outline{background:transparent;color:var(--secondary);border-radius:var(--radius);padding:.625rem 1.25rem;font-weight:700;font-size:.875rem;border:1.5px solid var(--secondary);display:inline-flex;align-items:center;gap:.5rem;transition:all .15s}
            .btn-outline:hover{background:var(--secondary-fixed)}
            .btn-ghost{background:transparent;color:var(--on-surface-variant);border-radius:var(--radius);padding:.5rem 1rem;font-weight:600;font-size:.875rem;display:inline-flex;align-items:center;gap:.5rem;transition:all .15s}
            .btn-ghost:hover{background:var(--surface-container)}
            .card{background:var(--surface);border:1px solid var(--outline-variant);border-radius:var(--radius-lg);padding:1.5rem;box-shadow:var(--shadow)}
            .glass{background:rgba(255,255,255,.7);backdrop-filter:blur(12px);-webkit-backdrop-filter:blur(12px)}
            .input-field{width:100%;border:1.5px solid var(--outline-variant);border-radius:var(--radius);padding:.625rem .875rem;outline:none;font-size:.875rem;transition:all .15s;background:var(--surface)}
            .input-field:focus{border-color:var(--secondary);box-shadow:0 0 0 3px rgba(0,98,160,.1)}
            .stat-value{font-size:2rem;font-weight:800;line-height:1.1;letter-spacing:-.02em}
            .section-title{font-size:1.75rem;font-weight:800;letter-spacing:-.02em;color:var(--primary)}
            @keyframes slideUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
            @keyframes fadeIn{from{opacity:0}to{opacity:1}}
            .anim-up{animation:slideUp .45s ease both}
            .anim-up:nth-child(2){animation-delay:.08s}.anim-up:nth-child(3){animation-delay:.16s}.anim-up:nth-child(4){animation-delay:.24s}.anim-up:nth-child(5){animation-delay:.32s}
        </style>
    @endif
    @stack('styles')
</head>
<body class="min-h-screen bg-[#f8f9fa] text-[#191c1d] antialiased flex flex-col">
    <header class="sticky top-0 z-40 bg-white/80 glass border-b border-[#e1e3e4]/60 transition-colors">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-5">
            <a href="{{ route('landing') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 bg-[#001e40] rounded-lg flex items-center justify-center shadow-sm">
                    <i class="bi bi-mortarboard-fill text-white text-lg"></i>
                </div>
                <div>
                    <p class="text-lg font-black text-[#001e40] leading-tight">Sentri<span class="text-[#0062a0]">Siswa</span></p>
                    <p class="text-[10px] text-slate-500 font-medium">Manajemen Presensi</p>
                </div>
            </a>
            <nav class="flex items-center gap-3 text-sm">
                <a href="{{ route('auth.register') }}" class="btn-outline">Daftar</a>
                <a href="{{ route('auth.login') }}" class="btn-primary"><i class="bi bi-box-arrow-in-right"></i> Masuk</a>
            </nav>
        </div>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="py-6 px-8 text-center border-t border-[#e1e3e4]/60 bg-white">
        <p class="text-xs text-slate-400 tracking-wider">&copy; {{ date('Y') }} <span class="font-semibold text-[#001e40]">Sentri Siswa</span> &mdash; SMA Negeri 11 Kab. Tangerang</p>
    </footer>

    @stack('scripts')
</body>
</html>