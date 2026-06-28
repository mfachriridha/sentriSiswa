<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="sentri">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sentri Siswa SMAN 11 Kabupaten Tangerang - sistem absensi, monitoring, dan pelanggaran siswa.">

    <title>Sentri Siswa | SMAN 11 Kabupaten Tangerang</title>

    @fonts

    <link rel="stylesheet" href="{{ asset('css/landing-hero.css') }}">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased font-sans">
    @php
        $heroImages = [
            [
                'src' => asset('images/landing/lapangan-2.jpg'),
                'alt' => 'Area lapangan dan koridor SMAN 11 Kabupaten Tangerang',
                'caption' => 'Lingkungan sekolah yang aktif dan tertata',
            ],
            [
                'src' => asset('images/landing/lapangan-1.jpg'),
                'alt' => 'Lapangan utama SMAN 11 Kabupaten Tangerang',
                'caption' => 'Monitoring kehadiran untuk aktivitas sekolah harian',
            ],
            [
                'src' => asset('images/landing/kelas-11-ipa-6.jpg'),
                'alt' => 'Kegiatan belajar di kelas SMAN 11 Kabupaten Tangerang',
                'caption' => 'Data siswa lebih mudah dipantau oleh sekolah',
            ],
        ];
    @endphp

    <!-- Header Navigation -->
    <header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/90 backdrop-blur-md transition-all duration-300">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="group flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-tr from-primary to-accent text-sm font-bold text-white shadow-md transition-all duration-300 group-hover:scale-105">SS</span>
                <span>
                    <span class="block text-sm font-extrabold leading-tight text-slate-900 tracking-tight transition-colors group-hover:text-primary">Sentri Siswa</span>
                    <span class="block text-[10px] text-slate-500 font-medium tracking-wider uppercase">SMAN 11 Kabupaten Tangerang</span>
                </span>
            </a>

            <nav class="hidden items-center gap-1 text-sm font-semibold text-slate-600 md:flex">
                <a href="#fitur" class="hover:text-primary transition-colors py-1.5 px-3 rounded-lg hover:bg-slate-50">Fitur</a>
                <a href="#sekolah" class="hover:text-primary transition-colors py-1.5 px-3 rounded-lg hover:bg-slate-50">Sekolah</a>
                <a href="#kontak" class="hover:text-primary transition-colors py-1.5 px-3 rounded-lg hover:bg-slate-50">Kontak</a>
            </nav>

            <div class="flex items-center gap-3">
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 shadow-sm transition-all duration-300 hover:bg-slate-50 active:scale-95">Masuk</a>
            </div>
        </div>
    </header>

    <main>
        <!-- Hero Section -->
        <section class="hero-section flex items-center relative">
            <div class="hero-bg" aria-hidden="true">
                @foreach ($heroImages as $index => $image)
                    <div class="hero-slide"
                         style="background-image: url('{{ $image['src'] }}'); animation-delay: {{ $index * 5 }}s;"></div>
                @endforeach
                <div class="hero-overlay"></div>
                <div class="hero-gradient"></div>
            </div>

            <div class="relative w-full mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28">
                <div class="max-w-2xl backdrop-blur-md bg-slate-950/40 border border-white/10 rounded-3xl p-8 sm:p-10 shadow-2xl text-white relative overflow-hidden">
                    <!-- Ambient Glow inside Card -->
                    <div class="absolute -right-20 -top-20 h-40 w-40 rounded-full bg-primary/20 blur-3xl"></div>

                    <div class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3.5 py-1.5 text-xs font-semibold text-teal-200 shadow-sm backdrop-blur-sm">
                        <span class="h-2 w-2 rounded-full bg-teal-400 animate-pulse"></span>
                        Sistem kedisiplinan dan absensi sekolah
                    </div>

                    <h1 class="mt-6 text-3xl font-extrabold tracking-tight sm:text-4xl lg:text-5xl leading-[1.15] bg-gradient-to-r from-white via-slate-100 to-teal-100 bg-clip-text text-transparent">
                        Sentri Siswa untuk SMAN 11 Kabupaten Tangerang
                    </h1>

                    <p class="mt-4 text-sm sm:text-base text-slate-200 leading-relaxed font-medium">
                        Platform internal untuk absensi siswa, monitoring kelas, rekap laporan, poin pelanggaran, dan tata tertib sekolah dalam satu sistem yang rapi.
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-bold text-white shadow-lg transition-all duration-300 hover:bg-primary-dark hover:scale-[1.02] active:scale-95">
                            Masuk
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/30 bg-white/10 px-6 py-3 text-sm font-bold text-white backdrop-blur-sm transition-all duration-300 hover:border-white hover:bg-white hover:text-primary hover:scale-[1.02] active:scale-95">
                                Daftar
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <!-- Floating Metrics Section -->
        <div class="relative z-20 -mt-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl border border-slate-200/80 bg-white p-6 shadow-xl backdrop-blur-sm sm:p-8">
                <div class="grid gap-6 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-slate-100">
                    <!-- Students Card -->
                    <div class="flex items-center gap-4 sm:justify-center pb-4 sm:pb-0">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($studentCount ?? 0, 0, ',', '.') }}</p>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Siswa</p>
                        </div>
                    </div>

                    <!-- Teachers Card -->
                    <div class="flex items-center gap-4 sm:justify-center pt-4 sm:pt-0 sm:pl-6 pb-4 sm:pb-0">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-teal-600/10 text-teal-600">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="8.5" cy="7" r="4"></circle>
                                <line x1="18" y1="8" x2="22" y2="12"></line>
                                <line x1="22" y1="8" x2="18" y2="12"></line>
                            </svg>
                        </div>
                        <div>
                            <p class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($teacherCount ?? 0, 0, ',', '.') }}</p>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Guru</p>
                        </div>
                    </div>

                    <!-- Classes Card -->
                    <div class="flex items-center gap-4 sm:justify-center pt-4 sm:pt-0 sm:pl-6">
                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-600/10 text-amber-600">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="9" y1="3" x2="9" y2="21"></line>
                                <line x1="15" y1="3" x2="15" y2="21"></line>
                                <line x1="3" y1="9" x2="21" y2="9"></line>
                                <line x1="3" y1="15" x2="21" y2="15"></line>
                            </svg>
                        </div>
                        <div>
                            <p class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($classCount ?? 0, 0, ',', '.') }}</p>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kelas Aktif</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <section id="fitur" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
            <div class="max-w-3xl text-center mx-auto mb-16">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-primary/10 text-primary uppercase tracking-wider">Fitur Utama</span>
                <h2 class="mt-4 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">Satu sistem untuk data kehadiran dan kedisiplinan</h2>
                <p class="mt-4 text-base text-slate-600 leading-relaxed">Dibuat untuk kebutuhan siswa, wali kelas, BK, kesiswaan, dan admin sekolah tanpa memecah data ke banyak sistem.</p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Feature 1 -->
                <article class="group relative rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:shadow-md hover:border-primary/30">
                    <div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10 text-primary transition-all duration-300 group-hover:scale-110 group-hover:bg-primary group-hover:text-white">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 11l3 3L22 4"></path>
                                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                            </svg>
                        </div>
                        <h3 class="mt-5 font-bold text-slate-900 text-lg leading-snug">Absensi harian</h3>
                        <p class="mt-2.5 text-sm leading-relaxed text-slate-600">Siswa absen, wali kelas memantau status harian, dan rekap bisa ditarik untuk laporan.</p>
                    </div>
                </article>

                <!-- Feature 2 -->
                <article class="group relative rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:shadow-md hover:border-primary/30">
                    <div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10 text-primary transition-all duration-300 group-hover:scale-110 group-hover:bg-primary group-hover:text-white">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <h3 class="mt-5 font-bold text-slate-900 text-lg leading-snug">Monitoring kelas</h3>
                        <p class="mt-2.5 text-sm leading-relaxed text-slate-600">Guru melihat data siswa sesuai akses: kelas sendiri, tingkat BK, atau seluruh kelas kesiswaan.</p>
                    </div>
                </article>

                <!-- Feature 3 -->
                <article class="group relative rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:shadow-md hover:border-primary/30">
                    <div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10 text-primary transition-all duration-300 group-hover:scale-110 group-hover:bg-primary group-hover:text-white">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 9v4"></path>
                                <path d="M12 17h.01"></path>
                                <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"></path>
                            </svg>
                        </div>
                        <h3 class="mt-5 font-bold text-slate-900 text-lg leading-snug">Poin pelanggaran</h3>
                        <p class="mt-2.5 text-sm leading-relaxed text-slate-600">Pengajuan pelanggaran BK dan approval kesiswaan membuat poin final tetap terkontrol.</p>
                    </div>
                </article>

                <!-- Feature 4 -->
                <article class="group relative rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm transition-all duration-300 hover:-translate-y-1.5 hover:shadow-md hover:border-primary/30">
                    <div>
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-primary/10 text-primary transition-all duration-300 group-hover:scale-110 group-hover:bg-primary group-hover:text-white">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <path d="M14 2v6h6"></path>
                                <path d="M16 13H8"></path>
                                <path d="M16 17H8"></path>
                                <path d="M10 9H8"></path>
                            </svg>
                        </div>
                        <h3 class="mt-5 font-bold text-slate-900 text-lg leading-snug">Tata tertib digital</h3>
                        <p class="mt-2.5 text-sm leading-relaxed text-slate-600">PDF tata tertib aktif bisa dipublikasikan dan dibaca siswa langsung dari portal.</p>
                    </div>
                </article>
            </div>
        </section>

        <!-- School Profile Section -->
        <section id="sekolah" class="bg-white border-y border-slate-200/60 py-16 sm:py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-12 lg:grid-cols-12 lg:items-center">
                    <!-- Text Info Column -->
                    <div class="lg:col-span-6 space-y-6">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-teal-100 text-teal-800 uppercase tracking-wider">Profil Sekolah</span>
                        <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">SMAN 11 Kabupaten Tangerang</h2>
                        <p class="text-base text-slate-600 leading-relaxed">
                            SMAN 11 Kabupaten Tangerang merupakan sekolah menengah atas negeri terkemuka di Kabupaten Tangerang yang berkomitmen tinggi dalam mencetak lulusan berprestasi, berkarakter mulia, dan berdisiplin tinggi.
                        </p>
                        <p class="text-base text-slate-600 leading-relaxed">
                            Sekolah kami mengadopsi platform <strong>Sentri Siswa</strong> guna menyatukan sistem absensi digital dan memantau kehadiran siswa secara real-time guna mendukung ketertiban lingkungan belajar yang optimal.
                        </p>
                    </div>

                    <!-- Visual Frame Column -->
                    <div class="lg:col-span-6">
                        <div class="relative mx-auto max-w-md lg:max-w-none">
                            <!-- Background Accent Spot -->
                            <div class="absolute -inset-2 rounded-3xl bg-gradient-to-tr from-primary to-accent opacity-20 blur-xl -z-10"></div>
                            <!-- Image Frame -->
                            <div class="relative rounded-3xl overflow-hidden shadow-2xl border-4 border-white bg-slate-100 aspect-video lg:aspect-[4/3] rotate-1 hover:rotate-0 transition-transform duration-500">
                                <img src="{{ asset('images/landing/kelas-11-ipa-6.jpg') }}" alt="Gedung Sekolah SMAN 11 Kabupaten Tangerang" class="h-full w-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-transparent"></div>
                                <div class="absolute bottom-5 left-5 right-5 text-white">
                                    <p class="text-xs font-bold text-teal-300 uppercase tracking-wider">Suasana Kelas</p>
                                    <p class="text-base font-semibold mt-1">Kegiatan Belajar & Aktivitas Siswa</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kontak & Lokasi Cards Grid -->
                <div id="kontak" class="mt-16 sm:mt-24 grid gap-6 sm:grid-cols-2">
                    <!-- Address Card -->
                    <div class="rounded-3xl border border-slate-200/80 bg-slate-50/50 p-6 sm:p-8 flex flex-col justify-between hover:bg-slate-50 transition-colors duration-300">
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Lokasi Sekolah</h3>
                                <p class="mt-2 text-sm leading-relaxed text-slate-600">Jl. K.H. Hasyim Ashari No.Km.1, Sepatan, Kec. Sepatan, Kabupaten Tangerang, Banten 15520</p>
                            </div>
                        </div>
                        <div class="mt-6 border-t border-slate-200/80 pt-4">
                            <a href="https://maps.google.com/?q=SMAN+11+Kabupaten+Tangerang" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-xs font-bold text-primary hover:text-primary-dark transition-colors">
                                Buka di Google Maps
                                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14 21 3"/></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Contact Details Card -->
                    <div class="rounded-3xl border border-slate-200/80 bg-slate-50/50 p-6 sm:p-8 flex flex-col justify-between hover:bg-slate-50 transition-colors duration-300">
                        <div class="flex items-start gap-4">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-teal-600/10 text-teal-600">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Hubungi Kami</h3>
                                <p class="mt-2 text-sm leading-relaxed text-slate-600">Hubungi tata usaha atau bagian administrasi SMAN 11 Kabupaten Tangerang untuk pertanyaan terkait akademis dan sistem.</p>
                            </div>
                        </div>
                        <div class="mt-6 border-t border-slate-200/80 pt-4 flex flex-wrap gap-4 text-xs font-semibold text-slate-700">
                            <span class="flex items-center gap-1.5">
                                <svg class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                Telp: (021) 59371391
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer Section -->
    <footer class="border-t border-slate-900 bg-slate-950 text-white py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary text-xs font-bold text-white shadow-sm">SS</span>
                        <span class="font-bold tracking-tight text-slate-100">Sentri Siswa</span>
                    </div>
                    <p class="mt-2 text-xs text-slate-400">© {{ date('Y') }} SMAN 11 Kabupaten Tangerang. Hak Cipta Dilindungi.</p>
                </div>
                
                <div class="flex flex-wrap gap-x-6 gap-y-3 text-xs font-medium">
                    <a href="{{ route('privacy-policy') }}" class="text-slate-400 hover:text-white transition-colors">Kebijakan Privasi</a>
                    <a href="{{ route('terms-of-service') }}" class="text-slate-400 hover:text-white transition-colors">Ketentuan Layanan</a>
                    <a href="{{ route('login') }}" class="text-slate-400 hover:text-white transition-colors">Masuk ke aplikasi</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
