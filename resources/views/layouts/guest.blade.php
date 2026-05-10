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
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <style>
            :root {
                --primary: #001e40;
                --primary-hover: #002856;
                --primary-light: #d5e3ff;
                --secondary: #0062a0;
                --secondary-fixed: #d0e4ff;
                --surface: #ffffff;
                --surface-alt: #edeeef;
                --border: #c3c6d1;
                --text: #191c1d;
                --text-muted: #43474f;
                --text-soft: #64748b;
                --error: #ba1a1a;
                --bg: #f8f9fa;
                --radius: 0.5rem;
                --radius-lg: 1rem;
                --shadow: 0 1px 3px rgba(0,0,0,.08);
                --shadow-md: 0 4px 12px rgba(0,0,0,.1);
            }
            *{box-sizing:border-box}
            html,body{font-family:'Inter',system-ui,sans-serif}
            body{background:var(--bg);color:var(--text)}
            .text-soft{color:var(--text-soft)}
            .text-muted{color:var(--text-muted)}

            .btn-primary{background:var(--primary);color:#fff;border-radius:var(--radius);padding:.625rem 1.25rem;font-weight:700;font-size:.875rem;display:inline-flex;align-items:center;gap:.5rem;transition:all .15s;box-shadow:var(--shadow);border:1.5px solid transparent}
            .btn-primary:hover{background:var(--primary-hover);box-shadow:var(--shadow-md);transform:translateY(-1px)}

            .btn-outline{background:transparent;color:var(--secondary);border-radius:var(--radius);padding:.625rem 1.25rem;font-weight:700;font-size:.875rem;border:1.5px solid var(--secondary);display:inline-flex;align-items:center;gap:.5rem;transition:all .15s}
            .btn-outline:hover{background:var(--secondary-fixed);transform:translateY(-1px)}

            .btn-ghost{background:transparent;color:var(--text-muted);border-radius:var(--radius);padding:.5rem 1rem;font-weight:600;font-size:.875rem;display:inline-flex;align-items:center;gap:.5rem;transition:all .15s}
            .btn-ghost:hover{background:var(--surface-alt);color:var(--text)}

            .card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.5rem;box-shadow:var(--shadow)}

            .glass{background:rgba(255,255,255,.7);backdrop-filter:blur(12px)}

            .input-field{width:100%;border:1.5px solid var(--border);border-radius:var(--radius);padding:.625rem .875rem;outline:none;font-size:.875rem;transition:all .15s;background:var(--surface);color:var(--text)}
            .input-field:focus{border-color:var(--secondary);box-shadow:0 0 0 3px rgba(0,98,160,.1)}

            .stat-value{font-size:2rem;font-weight:800;line-height:1.1;letter-spacing:-.02em}
            .section-title{font-size:1.75rem;font-weight:800;letter-spacing:-.02em;color:var(--primary)}
            @keyframes slideUp{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:translateY(0)}}
            .anim-up{animation:slideUp .45s ease both}
            .anim-up:nth-child(2){animation-delay:.08s}.anim-up:nth-child(3){animation-delay:.16s}.anim-up:nth-child(4){animation-delay:.24s}.anim-up:nth-child(5){animation-delay:.32s}
        </style>
    @endif
    @stack('styles')
</head>
<body class="min-h-screen bg-[var(--bg)] text-[var(--text)] antialiased flex flex-col">
    <header class="sticky top-0 z-40 bg-white/80 glass border-b border-[var(--border)]/60 transition-colors">
        <div class="mx-auto flex h-14 sm:h-16 max-w-6xl items-center justify-between px-3 sm:px-5">
            <a href="{{ route('landing') }}" class="flex items-center gap-2 sm:gap-3">
                <div class="w-8 h-8 sm:w-10 sm:h-10 bg-[var(--primary)] rounded-lg flex items-center justify-center shadow-sm shrink-0">
                    <i class="bi bi-mortarboard-fill text-white text-sm sm:text-lg"></i>
                </div>
                <div>
                    <p class="text-base sm:text-lg font-black text-[var(--primary)] leading-tight">Sentri<span class="text-[var(--secondary)]">Siswa</span></p>
                    <p class="hidden sm:block text-[10px] text-soft font-medium">Manajemen Presensi</p>
                </div>
            </a>
            <nav class="flex items-center gap-1.5 sm:gap-2 text-xs sm:text-sm">
                <a href="{{ route('auth.register') }}" class="btn-outline !py-1.5 !px-2.5 sm:!py-2.5 sm:!px-4">Daftar</a>
                <a href="{{ route('auth.login') }}" class="btn-primary !py-1.5 !px-2.5 sm:!py-2.5 sm:!px-4"><i class="bi bi-box-arrow-in-right hidden sm:inline"></i> Masuk</a>
            </nav>
        </div>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="py-6 px-8 text-center border-t border-[var(--border)]/60 bg-[var(--surface)]">
        <p class="text-xs text-muted tracking-wider">&copy; {{ date('Y') }} <span class="font-semibold text-[var(--primary)]">Sentri Siswa</span> &mdash; SMA Negeri 11 Kab. Tangerang</p>
    </footer>

    @stack('scripts')
</body>
</html>