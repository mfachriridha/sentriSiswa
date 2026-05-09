```html
<!DOCTYPE html><html class="light" lang="id"><head>
<meta charset="utf-8">
<meta content="width=device-width, initial-scale=1.0" name="viewport">
<title>Tata Tertib - Kurator Akademik</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "tertiary-fixed-dim": "#88d982",
                        "primary-fixed-dim": "#a7c8ff",
                        "outline-variant": "#c3c6d1",
                        "on-primary": "#ffffff",
                        "inverse-primary": "#a7c8ff",
                        "error": "#ba1a1a",
                        "secondary": "#0062a0",
                        "surface-container-highest": "#e1e3e4",
                        "secondary-container": "#32a3fd",
                        "on-secondary": "#ffffff",
                        "on-primary-fixed": "#001b3c",
                        "on-error-container": "#93000a",
                        "on-error": "#ffffff",
                        "surface-bright": "#f8f9fa",
                        "primary-container": "#003366",
                        "secondary-fixed": "#d0e4ff",
                        "on-secondary-fixed-variant": "#00497a",
                        "surface-tint": "#3a5f94",
                        "on-tertiary": "#ffffff",
                        "surface-container": "#edeeef",
                        "primary": "#001e40",
                        "on-tertiary-container": "#5ead5c",
                        "tertiary-fixed": "#a3f69c",
                        "surface-dim": "#d9dadb",
                        "outline": "#737780",
                        "on-primary-container": "#799dd6",
                        "inverse-on-surface": "#f0f1f2",
                        "surface-container-low": "#f3f4f5",
                        "secondary-fixed-dim": "#9ccaff",
                        "tertiary-container": "#003d0b",
                        "surface-variant": "#e1e3e4",
                        "on-primary-fixed-variant": "#1f477b",
                        "on-secondary-fixed": "#001d35",
                        "inverse-surface": "#2e3132",
                        "tertiary": "#002504",
                        "background": "#f8f9fa",
                        "surface-container-lowest": "#ffffff",
                        "on-background": "#191c1d",
                        "primary-fixed": "#d5e3ff",
                        "on-surface": "#191c1d",
                        "error-container": "#ffdad6",
                        "surface-container-high": "#e7e8e9",
                        "on-surface-variant": "#43474f",
                        "on-secondary-container": "#00375e",
                        "surface": "#f8f9fa",
                        "on-tertiary-fixed-variant": "#005312",
                        "on-tertiary-fixed": "#002204"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.125rem",
                        "lg": "0.25rem",
                        "xl": "0.5rem",
                        "full": "0.75rem"
                    },
                    "fontFamily": {
                        "headline": ["Inter"],
                        "body": ["Inter"],
                        "label": ["Inter"]
                    }
                },
            },
        }
    </script>
<style>
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
    </style>
</head>
<body class="bg-surface text-on-surface min-h-screen flex flex-col">
<!-- TopNavBar -->
<header class="w-full sticky top-0 z-40 bg-white dark:bg-[#001e40] shadow-sm transition-colors flex justify-between items-center w-full px-6 py-4">
<div class="flex items-center gap-3">
<div class="w-10 h-10 bg-primary-container rounded-lg flex items-center justify-center">
<span class="material-symbols-outlined text-white" data-icon="school">school</span>
</div>
<div>
<h1 class="text-lg font-black text-[#001e40] dark:text-white leading-tight">Portal SMA</h1>
<p class="text-xs text-slate-500 font-medium">Manajemen Absensi</p>
</div>
</div>
<div class="flex items-center gap-4">
<div class="flex items-center gap-3">
<button class="px-4 py-2 text-sm font-bold text-secondary border border-secondary rounded-lg hover:bg-secondary/5 transition-colors">Daftar</button>
<button class="px-4 py-2 text-sm font-bold text-white bg-primary rounded-lg hover:bg-primary-container transition-colors shadow-sm">Masuk</button>
</div>
</div>
</header>
<!-- Main Content -->
<main class="flex-1 p-4 md:p-12 bg-surface-container flex flex-col items-center">
<div class="max-w-5xl w-full">
<div class="mb-12 text-center">
<h2 class="text-4xl md:text-5xl font-black text-primary tracking-tight mb-4">Selamat Datang di Portal Absensi SMA</h2>
<h3 class="text-2xl font-bold text-secondary mb-4">Komitmen Kedisiplinan</h3>
<p class="text-on-surface-variant body-md max-w-2xl mx-auto">Silakan tinjau dokumen tata tertib sekolah berikut untuk memahami hak dan kewajiban Anda sebagai bagian dari komunitas akademik sebelum melanjutkan ke dashboard.</p>
</div>
<!-- Document Viewer (Bento Style) -->
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
<!-- Main PDF Area -->
<div class="lg:col-span-3 bg-surface-container-lowest rounded-xl shadow-sm overflow-hidden flex flex-col h-[700px]">
<div class="bg-surface-container-high px-6 py-4 flex items-center justify-between">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined text-error" data-icon="picture_as_pdf">picture_as_pdf</span>
<span class="font-semibold text-primary">TATA_TERBIT_2024_2025.pdf</span>
</div>
<div class="flex items-center gap-2">
<button class="p-2 hover:bg-surface-dim rounded-lg transition-colors">
<span class="material-symbols-outlined text-on-surface-variant" data-icon="zoom_out">zoom_out</span>
</button>
<span class="text-sm font-medium">100%</span>
<button class="p-2 hover:bg-surface-dim rounded-lg transition-colors">
<span class="material-symbols-outlined text-on-surface-variant" data-icon="zoom_in">zoom_in</span>
</button>
<div class="h-6 w-px bg-outline-variant mx-2"></div>
<button class="p-2 hover:bg-surface-dim rounded-lg transition-colors">
<span class="material-symbols-outlined text-on-surface-variant" data-icon="download">download</span>
</button>
</div>
</div>
<div class="flex-1 bg-surface-dim p-4 md:p-8 overflow-y-auto flex justify-center">
<!-- Simulated PDF Page -->
<div class="w-full max-w-3xl bg-white shadow-2xl p-8 md:p-16 min-h-[1000px] flex flex-col gap-8 relative">
<div class="text-center border-b-2 border-primary pb-8">
<h4 class="text-2xl font-bold text-primary uppercase tracking-widest mb-2">Kurator Akademik</h4>
<p class="text-sm font-medium text-secondary">KEPUTUSAN KEPALA SEKOLAH NOMOR: 421.3/SK/2024</p>
</div>
<div class="space-y-6">
<div class="space-y-2">
<h5 class="font-bold text-primary">BAB I: KETENTUAN UMUM</h5>
<div class="h-2 w-3/4 bg-surface-container-highest rounded-full"></div>
<div class="h-2 w-full bg-surface-container-high rounded-full opacity-60"></div>
<div class="h-2 w-5/6 bg-surface-container-high rounded-full opacity-60"></div>
</div>
<div class="space-y-2">
<h5 class="font-bold text-primary">BAB II: KEHADIRAN DAN KETERLAMBATAN</h5>
<div class="h-2 w-1/2 bg-surface-container-highest rounded-full"></div>
<div class="h-2 w-full bg-surface-container-high rounded-full opacity-60"></div>
<div class="h-2 w-full bg-surface-container-high rounded-full opacity-60"></div>
<div class="h-2 w-4/5 bg-surface-container-high rounded-full opacity-60"></div>
</div>
<div class="space-y-2">
<h5 class="font-bold text-primary">BAB III: PENGGUNAAN SERAGAM</h5>
<div class="h-2 w-2/3 bg-surface-container-highest rounded-full"></div>
<div class="h-2 w-full bg-surface-container-high rounded-full opacity-60"></div>
<div class="h-2 w-11/12 bg-surface-container-high rounded-full opacity-60"></div>
</div>
<div class="py-8">
<div class="w-full h-48 bg-surface-container rounded-xl flex items-center justify-center overflow-hidden">
<img alt="Academic Integrity" class="w-full h-full object-cover opacity-30 grayscale" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBCyEVuC1P0d1cDBpNDQv1gpGZhlU-BS_61-vAwq1JI82XXPwD-kOg8BgoM7m0giytT6HxDd3IarMQ8u5SxjeNo_RnwLFnB83G-J0CiHDgYiUZ8y6NFqgkFbMlI1sV9EsAle4r7HyP3Tpo96UdcZtBpFFEvft_sirK_NZxYrv3nf_YqJMGqP2R_r1KABpYusRcGTgF0qEgAWej4wouod3EfkW3khmOYjbg7Cwi6cUyc2HrkEnrfLKwIWC5ok7qApZvrPpJ_Oq3Iq_4">
</div>
</div>
<div class="space-y-2">
<h5 class="font-bold text-primary">BAB IV: SANKSI DAN PENGHARGAAN</h5>
<div class="h-2 w-3/4 bg-surface-container-highest rounded-full"></div>
<div class="h-2 w-full bg-surface-container-high rounded-full opacity-60"></div>
<div class="h-2 w-5/6 bg-surface-container-high rounded-full opacity-60"></div>
</div>
</div>
</div>
</div>
</div>
<!-- Sidebar Summary -->
<div class="space-y-6">
<div class="bg-primary text-white p-6 rounded-xl shadow-md relative overflow-hidden">
<div class="relative z-10">
<span class="material-symbols-outlined text-3xl mb-4" data-icon="info">info</span>
<h4 class="font-bold text-lg mb-2">Ringkasan Utama</h4>
<ul class="text-sm space-y-3 opacity-90">
<li class="flex gap-2">
<span class="material-symbols-outlined text-[14px]" data-icon="check_circle">check_circle</span>
                                    Toleransi keterlambatan 15 menit.
                                </li>
<li class="flex gap-2">
<span class="material-symbols-outlined text-[14px]" data-icon="check_circle">check_circle</span>
                                    Wajib melakukan absen selfie.
                                </li>
<li class="flex gap-2">
<span class="material-symbols-outlined text-[14px]" data-icon="check_circle">check_circle</span>
                                    Minimal kehadiran 85%.
                                </li>
</ul>
</div>
<div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
</div>
<div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/15 space-y-4">
<h4 class="font-bold text-primary">Kontak Bantuan</h4>
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-full bg-secondary-fixed flex items-center justify-center">
<span class="material-symbols-outlined text-on-secondary-fixed-variant" data-icon="support_agent">support_agent</span>
</div>
<div>
<p class="text-xs text-on-surface-variant">Admin Kesiswaan</p>
<p class="text-sm font-bold text-primary">admin@kurator.id</p>
</div>
</div>
</div>
<div class="bg-tertiary-fixed text-on-tertiary-fixed-variant p-6 rounded-xl flex items-start gap-4">
<span class="material-symbols-outlined text-on-tertiary-fixed-variant" data-icon="verified_user" style="font-variation-settings: &quot;FILL&quot; 1;">verified_user</span>
<div>
<p class="text-xs uppercase font-bold tracking-wider mb-1">Status Dokumen</p>
<p class="text-sm font-semibold">Tervalidasi &amp; Berlaku</p>
</div>
</div>
</div>
</div>
<!-- Action Footer -->
</div>
</main>
<!-- Footer -->
<footer class="w-full py-8 bg-white dark:bg-[#001e40] border-t border-slate-100 dark:border-slate-800 flex flex-col md:flex-row justify-between items-center px-12">
<div class="mb-4 md:mb-0">
<p class="font-inter text-xs uppercase tracking-widest text-slate-400">© 2024 Sistem Absensi Kurator Akademik. Hak Cipta Dilindungi.</p>
</div>
<div class="flex gap-6">
<a class="font-inter text-xs uppercase tracking-widest text-slate-400 hover:text-[#001e40] dark:hover:text-white transition-opacity duration-200" href="#"><br></a>
<a class="font-inter text-xs uppercase tracking-widest text-slate-400 hover:text-[#001e40] dark:hover:text-white transition-opacity duration-200" href="#"><br></a>
<a class="font-inter text-xs uppercase tracking-widest text-slate-400 hover:text-[#001e40] dark:hover:text-white transition-opacity duration-200" href="#"><br></a>
</div>
</footer>


</body></html>
```
