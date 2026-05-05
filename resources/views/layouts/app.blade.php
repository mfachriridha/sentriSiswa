<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'Sentri Siswa' }}</title>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Custom scrollbar agar tetap minimalis */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #888; border-radius: 10px; }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-900">

    <div class="flex h-screen overflow-hidden">
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-white transform -translate-x-full lg:translate-x-0 lg:relative transition-transform duration-300 ease-in-out">
            <div class="p-6">
                <h1 class="text-xl font-bold flex items-center gap-2">
                    <i class="bi bi-shield-check text-blue-400"></i>
                    <span>Sentri Siswa</span>
                </h1>
                <p class="text-xs text-slate-400 mt-1 uppercase tracking-widest">SMAN 11 Kab. Tangerang</p>
            </div>

            <nav class="mt-4 px-4 space-y-2">
                <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-blue-600 text-white font-medium transition">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>

                <div class="pt-4 pb-2 text-xs font-semibold text-slate-500 uppercase px-4">Menu Utama</div>

                <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
                    <i class="bi bi-camera-fill"></i> Presensi
                </a>

                <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
                    <i class="bi bi-clock-history"></i> Riwayat Presensi
                </a>

                <div class="pt-4 pb-2 text-xs font-semibold text-slate-500 uppercase px-4">Akun</div>
                <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
                    <i class="bi bi-person-circle"></i> Profil
                </a>
                <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg text-slate-300 hover:bg-slate-800 hover:text-white transition">
                    <i class="bi bi-gear-fill"></i> Pengaturan
                </a>

                <form action="#" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-lg text-red-400 hover:bg-red-900/20 hover:text-red-300 transition">
                        <i class="bi bi-box-arrow-right"></i> Keluar
                    </button>
                </form>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 lg:px-8 shrink-0">
                <button id="toggle-sidebar" class="lg:hidden p-2 rounded-md hover:bg-gray-100 transition">
                    <i class="bi bi-list text-2xl"></i>
                </button>

                <div class="hidden lg:block text-sm text-gray-500 font-medium">
                    Hari ini: <span class="text-gray-800">{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-semibold text-gray-800">Nama Pengguna</p>
                        <p class="text-xs text-gray-500 uppercase">Siswa / Wali Kelas</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center border border-blue-200">
                        <i class="bi bi-person text-blue-600"></i>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 lg:p-8 bg-gray-50">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>

            <footer class="bg-white border-t border-gray-200 py-4 px-8 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} Muhammad Fachri Ridha.
            </footer>
        </div>
    </div>

    <script>
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggle-sidebar');

        if(toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full');
            });
        }
    </script>

    @stack('scripts')
</body>
</html>
