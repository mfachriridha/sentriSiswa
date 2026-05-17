<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Portal Absensi - {{ config('sekolah.nama', 'SMA Negeri 1 ...') }}</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    @fonts
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.css" />
    <style>
        .swiper { width: 100%; height: 100vh; max-height: 600px; }
        .swiper-slide { position: relative; overflow: hidden; }
        .swiper-slide img { width: 100%; height: 100%; object-fit: cover; }
        .swiper-overlay {
            position: absolute; inset: 0;
            background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.6));
            display: flex; align-items: center; justify-content: center;
        }
        .swiper-pagination-bullet-active { background: #1c6880 !important; }
    </style>
</head>
<body class="bg-white text-zinc-800 antialiased">

    {{-- Navbar --}}
    <header class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur border-b border-zinc-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-xl text-brand-700 no-underline">
                    <svg class="w-8 h-8 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    {{ config('sekolah.nama', 'Portal Absensi') }}
                </a>
                <nav class="hidden md:flex items-center gap-1 text-sm">
                    <a href="#fitur" class="px-4 py-2 text-zinc-600 hover:text-brand-700 rounded-lg transition">Fitur</a>
                    <a href="#cara-kerja" class="px-4 py-2 text-zinc-600 hover:text-brand-700 rounded-lg transition">Cara Kerja</a>
                    <a href="#kontak" class="px-4 py-2 text-zinc-600 hover:text-brand-700 rounded-lg transition">Kontak</a>
                </nav>
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-5 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 transition no-underline">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-5 py-2 text-brand-700 border border-brand-300 rounded-lg text-sm font-medium hover:bg-brand-50 transition no-underline">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="px-5 py-2 bg-brand-600 text-white rounded-lg text-sm font-medium hover:bg-brand-700 transition no-underline">
                            Daftar
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    {{-- Hero Slider --}}
    <section class="pt-16">
        <div class="swiper hero-swiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <img src="{{ asset('images/hero/Kelas11Ipa6.jpg') }}" alt="Kelas" />
                    <div class="swiper-overlay">
                        <div class="text-center text-white px-4">
                            <h1 class="text-4xl md:text-6xl font-bold mb-4">Portal Absensi Siswa</h1>
                            <p class="text-lg md:text-xl text-white/80 max-w-2xl mx-auto">
                                Absensi modern dengan selfie & geofence. Pantau kehadiran siswa secara real-time.
                            </p>
                            <div class="mt-8 flex gap-4 justify-center">
                                @auth
                                    <a href="{{ route('dashboard') }}" class="px-8 py-3 bg-brand-600 text-white rounded-lg font-medium hover:bg-brand-700 transition no-underline text-lg">Dashboard</a>
                                @else
                                    <a href="{{ route('register') }}" class="px-8 py-3 bg-brand-600 text-white rounded-lg font-medium hover:bg-brand-700 transition no-underline text-lg">Mulai Absen</a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <img src="{{ asset('images/hero/Lapangan1.jpg') }}" alt="Lapangan" />
                    <div class="swiper-overlay">
                        <div class="text-center text-white px-4">
                            <h1 class="text-4xl md:text-6xl font-bold mb-4">Geofence Area Sekolah</h1>
                            <p class="text-lg md:text-xl text-white/80 max-w-2xl mx-auto">
                                Absensi hanya bisa dilakukan di dalam area sekolah. Akurat & terpercaya.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <img src="{{ asset('images/hero/Lapangan2.jpg') }}" alt="Lapangan 2" />
                    <div class="swiper-overlay">
                        <div class="text-center text-white px-4">
                            <h1 class="text-4xl md:text-6xl font-bold mb-4">Notifikasi WhatsApp</h1>
                            <p class="text-lg md:text-xl text-white/80 max-w-2xl mx-auto">
                                Wali kelas menerima laporan absensi langsung via WhatsApp. Komunikasi mudah dengan wali murid.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-prev !text-white/70 hover:!text-white"></div>
            <div class="swiper-button-next !text-white/70 hover:!text-white"></div>
        </div>
    </section>

    {{-- Fitur Utama --}}
    <section id="fitur" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-zinc-900 mb-4">Fitur Utama</h2>
                <p class="text-zinc-500 text-lg max-w-2xl mx-auto">Portal absensi modern yang memudahkan proses absensi dan monitoring kehadiran siswa.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center p-8 rounded-2xl border border-zinc-100 hover:border-brand-200 hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-brand-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Absen Selfie</h3>
                    <p class="text-zinc-500">Siswa melakukan absensi dengan foto selfie menggunakan kamera smartphone. Cepat, mudah, dan akurat.</p>
                </div>
                <div class="text-center p-8 rounded-2xl border border-zinc-100 hover:border-brand-200 hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-brand-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Geofence Sekolah</h3>
                    <p class="text-zinc-500">Absensi hanya berlaku di dalam area sekolah. Teknologi geolokasi memastikan siswa benar-benar berada di sekolah.</p>
                </div>
                <div class="text-center p-8 rounded-2xl border border-zinc-100 hover:border-brand-200 hover:shadow-lg transition">
                    <div class="w-16 h-16 bg-brand-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-3">Notifikasi WhatsApp</h3>
                    <p class="text-zinc-500">Laporan absensi otomatis terkirim ke WhatsApp wali kelas. Siap diteruskan ke grup wali murid.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Cara Kerja --}}
    <section id="cara-kerja" class="py-20 bg-zinc-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-zinc-900 mb-4">Cara Kerja</h2>
                <p class="text-zinc-500 text-lg max-w-2xl mx-auto">Empat langkah mudah untuk melakukan absensi.</p>
            </div>
            <div class="grid md:grid-cols-4 gap-8">
                <div class="text-center relative">
                    <div class="w-14 h-14 bg-brand-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">1</div>
                    <h4 class="font-semibold mb-2">Buka Aplikasi</h4>
                    <p class="text-sm text-zinc-500">Siswa membuka portal absensi melalui smartphone.</p>
                </div>
                <div class="text-center relative">
                    <div class="w-14 h-14 bg-brand-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">2</div>
                    <h4 class="font-semibold mb-2">Izinkan Lokasi</h4>
                    <p class="text-sm text-zinc-500">Izinkan akses lokasi untuk verifikasi area sekolah.</p>
                </div>
                <div class="text-center relative">
                    <div class="w-14 h-14 bg-brand-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">3</div>
                    <h4 class="font-semibold mb-2">Ambil Selfie</h4>
                    <p class="text-sm text-zinc-500">Ambil foto selfie sebagai bukti kehadiran.</p>
                </div>
                <div class="text-center relative">
                    <div class="w-14 h-14 bg-brand-600 text-white rounded-full flex items-center justify-center text-2xl font-bold mx-auto mb-4">4</div>
                    <h4 class="font-semibold mb-2">Absen Tercatat</h4>
                    <p class="text-sm text-zinc-500">Absensi tersimpan & notifikasi terkirim ke wali kelas.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Sistem Poin --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-zinc-900 mb-6">Sistem Poin Pelanggaran</h2>
                    <p class="text-zinc-500 mb-6 text-lg">Setiap siswa memiliki 100 poin awal. Poin berkurang saat terjadi pelanggaran. Wali kelas dapat mengajukan penambahan poin untuk siswa yang menunjukkan perbaikan.</p>
                    <ul class="space-y-3">
                        <li class="flex items-center gap-3"><span class="w-2 h-2 bg-brand-500 rounded-full"></span> Pelanggaran tercatat otomatis oleh admin</li>
                        <li class="flex items-center gap-3"><span class="w-2 h-2 bg-brand-500 rounded-full"></span> Poin real-time terlihat di dashboard siswa</li>
                        <li class="flex items-center gap-3"><span class="w-2 h-2 bg-brand-500 rounded-full"></span> Wali kelas dapat mengajukan tambahan poin</li>
                        <li class="flex items-center gap-3"><span class="w-2 h-2 bg-brand-500 rounded-full"></span> BK memonitor siswa dengan poin rendah</li>
                    </ul>
                </div>
                <div class="bg-zinc-50 rounded-2xl p-8 border border-zinc-200">
                    <div class="grid grid-cols-2 gap-6 text-center">
                        <div class="p-4 bg-white rounded-xl shadow-sm">
                            <div class="text-4xl font-bold text-brand-600">100</div>
                            <div class="text-sm text-zinc-500 mt-1">Poin Awal</div>
                        </div>
                        <div class="p-4 bg-white rounded-xl shadow-sm">
                            <div class="text-4xl font-bold text-red-500">4</div>
                            <div class="text-sm text-zinc-500 mt-1">Kategori Pelanggaran</div>
                        </div>
                        <div class="p-4 bg-white rounded-xl shadow-sm">
                            <div class="text-4xl font-bold text-brand-600">36</div>
                            <div class="text-sm text-zinc-500 mt-1">Jenis Pelanggaran</div>
                        </div>
                        <div class="p-4 bg-white rounded-xl shadow-sm">
                            <div class="text-4xl font-bold text-amber-500">30</div>
                            <div class="text-sm text-zinc-500 mt-1">Batas Warning</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer id="kontak" class="bg-brand-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <h4 class="font-bold text-lg mb-4">{{ config('sekolah.nama', 'SMA Negeri 1 ...') }}</h4>
                    <p class="text-brand-300 text-sm">Portal absensi modern untuk monitoring kehadiran siswa secara real-time.</p>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4">Tautan</h4>
                    <ul class="space-y-2 text-sm text-brand-300">
                        <li><a href="{{ route('login') }}" class="hover:text-white transition">Masuk</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white transition">Daftar</a></li>
                        <li><a href="#fitur" class="hover:text-white transition">Fitur</a></li>
                        <li><a href="#cara-kerja" class="hover:text-white transition">Cara Kerja</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold text-lg mb-4">Kontak</h4>
                    <ul class="space-y-2 text-sm text-brand-300">
                        <li>Email: sekolah@sch.id</li>
                        <li>Telp: (021) 12345678</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-brand-700 mt-8 pt-8 text-center text-sm text-brand-400">
                &copy; {{ date('Y') }} {{ config('sekolah.nama', 'Portal Absensi') }}. All rights reserved.
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/swiper@12/swiper-bundle.min.js"></script>
    <script>
        new Swiper('.hero-swiper', {
            loop: true,
            autoplay: { delay: 5000, disableOnInteraction: false },
            pagination: { el: '.swiper-pagination', clickable: true },
            navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        });
    </script>
</body>
</html>
