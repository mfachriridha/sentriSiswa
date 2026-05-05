<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sentri Siswa')</title>

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
                --brand: #2563eb;
                --brand-light: #dbeafe;
                --brand-dark: #1d4ed8;
                --surface: #ffffff;
                --surface-alt: #f8fafc;
                --border: #e2e8f0;
                --text: #0f172a;
                --text-muted: #64748b;
                --radius-sm: .625rem;
                --radius: .875rem;
                --radius-lg: 1.25rem;
                --radius-xl: 1.5rem;
                --shadow-sm: 0 1px 2px rgba(0,0,0,.05);
                --shadow: 0 1px 3px rgba(0,0,0,.1),0 1px 2px rgba(0,0,0,.06);
                --shadow-md: 0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -2px rgba(0,0,0,.1);
                --shadow-lg: 0 10px 15px -3px rgba(0,0,0,.1),0 4px 6px -4px rgba(0,0,0,.1);
            }
            *{box-sizing:border-box}
            body{font-family:'Plus Jakarta Sans',system-ui,sans-serif}
            .btn-brand{background:var(--brand);color:#fff;border-radius:var(--radius);padding:.75rem 1.5rem;font-weight:600;font-size:.875rem;display:inline-flex;align-items:center;gap:.5rem;transition:all .2s;box-shadow:0 1px 2px rgba(37,99,235,.3)}
            .btn-brand:hover{background:var(--brand-dark);box-shadow:0 4px 6px rgba(37,99,235,.25);transform:translateY(-1px)}
            .btn-outline{background:transparent;color:var(--text);border-radius:var(--radius);padding:.75rem 1.5rem;font-weight:600;font-size:.875rem;border:1.5px solid var(--border);display:inline-flex;align-items:center;gap:.5rem;transition:all .2s}
            .btn-outline:hover{background:var(--surface-alt);border-color:#cbd5e1;transform:translateY(-1px)}
            .btn-ghost{background:transparent;color:var(--text-muted);border-radius:var(--radius);padding:.625rem 1rem;font-weight:600;font-size:.875rem;display:inline-flex;align-items:center;gap:.5rem;transition:all .2s}
            .btn-ghost:hover{background:var(--surface-alt);color:var(--text)}
            .btn-danger{background:#ef4444;color:#fff;border-radius:var(--radius);padding:.75rem 1.5rem;font-weight:600;font-size:.875rem;transition:all .2s}
            .btn-danger:hover{background:#dc2626}
            .card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-xl);padding:1.75rem;box-shadow:var(--shadow-md)}
            .card-sm{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:1.25rem;box-shadow:var(--shadow-sm)}
            .input-field{width:100%;border:1.5px solid var(--border);border-radius:var(--radius);padding:.75rem 1rem;outline:none;font-size:.875rem;transition:all .2s;background:var(--surface)}
            .input-field:focus{border-color:var(--brand);box-shadow:0 0 0 3px rgba(37,99,235,.1)}
            .table-container{overflow:auto;border:1px solid var(--border);border-radius:var(--radius-lg);background:var(--surface)}
            .table-header{padding:.875rem 1.25rem;background:var(--surface-alt);color:var(--text-muted);font-size:.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;text-align:left;border-bottom:1px solid var(--border)}
            .table-cell{padding:.875rem 1.25rem;font-size:.875rem;color:var(--text);border-bottom:1px solid #f1f5f9}
            .badge{display:inline-flex;align-items:center;border-radius:9999px;padding:.2rem .75rem;font-size:.75rem;font-weight:600;letter-spacing:.01em}
            .badge-green{background:#dcfce7;color:#15803d}
            .badge-amber{background:#fef3c7;color:#a16207}
            .badge-red{background:#fee2e2;color:#dc2626}
            .badge-blue{background:var(--brand-light);color:var(--brand-dark)}
            .stat-value{font-size:2rem;font-weight:800;line-height:1.2;letter-spacing:-.02em}
            .section-title{font-size:1.75rem;font-weight:700;letter-spacing:-.02em;line-height:1.2}
            @keyframes slideUp{from{opacity:0;transform:translateY(16px)}to{opacity:1;transform:translateY(0)}}
            @keyframes fadeIn{from{opacity:0}to{opacity:1}}
            .anim-up{animation:slideUp .5s ease both}
            .anim-up:nth-child(2){animation-delay:.1s}
            .anim-up:nth-child(3){animation-delay:.2s}
            .anim-up:nth-child(4){animation-delay:.3s}
            .anim-up:nth-child(5){animation-delay:.4s}
            .anim-fade{animation:fadeIn .4s ease both}
        </style>
    @endif

    @stack('styles')
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
    <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-xl border-b border-slate-200/60">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-5">
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-2.5">
                <span class="w-9 h-9 bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl flex items-center justify-center text-white shadow-md shadow-blue-500/20">
                    <i class="bi bi-mortarboard-fill text-lg"></i>
                </span>
                <span class="font-bold text-lg tracking-tight text-slate-900">Sentri<span class="text-blue-600">Siswa</span></span>
            </a>
            <nav class="flex items-center gap-3 text-sm">
                <a href="{{ route('auth.login') }}" class="btn-ghost">Masuk</a>
                <a href="{{ route('auth.register') }}" class="btn-brand"><i class="bi bi-person-plus"></i> Daftar</a>
            </nav>
        </div>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="bg-white border-t border-slate-200/60 py-6 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} <span class="font-semibold text-slate-700">Sentri Siswa</span> &mdash; SMA Negeri 11 Kab. Tangerang
    </footer>

    @stack('scripts')
</body>
</html>