<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased flex flex-col">
    <header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/80 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-3 text-sm font-semibold text-slate-900">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-primary-600 text-white shadow-sm">
                    <i class="bi bi-shield-check"></i>
                </span>
                <span class="font-display text-base">Sentri Siswa</span>
            </a>
            <nav class="flex items-center gap-2 text-sm">
                <a href="{{ route('auth.login') }}" class="btn-ghost">Login</a>
                <a href="{{ route('auth.register') }}" class="btn-primary">Register</a>
            </nav>
        </div>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="border-t border-slate-200/70 bg-white/80 py-4 px-8 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Sentri Siswa
    </footer>

    @stack('scripts')
</body>
</html>
