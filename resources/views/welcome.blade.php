<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="sentri">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sentri Siswa SMAN 11 Kabupaten Tangerang - sistem absensi, monitoring, dan pelanggaran siswa.">

    <title>Sentri Siswa | SMAN 11 Kabupaten Tangerang</title>

    @fonts

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
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

    <header class="sticky top-0 z-30 border-b border-white/70 bg-white/85 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-sm font-bold text-white shadow-sm">SS</span>
                <span>
                    <span class="block text-sm font-bold leading-tight text-slate-900">Sentri Siswa</span>
                    <span class="block text-xs text-slate-500">SMAN 11 Kabupaten Tangerang</span>
                </span>
            </a>

            <nav class="hidden items-center gap-6 text-sm font-medium text-slate-600 md:flex">
                <a href="#fitur" class="hover:text-primary">Fitur</a>
                <a href="#sekolah" class="hover:text-primary">Sekolah</a>
                <a href="#kontak" class="hover:text-primary">Kontak</a>
            </nav>

            <div class="flex items-center gap-2">
                <a href="{{ route('login') }}" class="btn btn-primary btn-sm rounded-lg px-4">Masuk</a>
            </div>
        </div>
    </header>

    <main>
        <section class="relative isolate overflow-hidden min-h-[640px] lg:min-h-[720px]">
            <div class="absolute inset-0 -z-10" aria-hidden="true">
                @foreach ($heroImages as $index => $image)
                    <div class="hero-slide absolute inset-0 bg-cover bg-center"
                         style="background-image: url('{{ $image['src'] }}'); animation-delay: {{ $index * 5 }}s;"></div>
                @endforeach
                <div class="absolute inset-0 bg-slate-950/55"></div>
                <div class="absolute inset-0 bg-gradient-to-br from-primary/30 via-transparent to-teal-900/40"></div>
            </div>

            <div class="relative mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8 lg:py-28">
                <div class="max-w-2xl text-white">
                    <div class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1 text-xs font-semibold text-white shadow-sm backdrop-blur">
                        <span class="h-2 w-2 rounded-full bg-primary"></span>
                        Sistem kedisiplinan dan absensi sekolah
                    </div>

                    <h1 class="mt-6 max-w-3xl text-4xl font-extrabold tracking-tight drop-shadow-sm sm:text-5xl lg:text-6xl">
                        Sentri Siswa untuk SMAN 11 Kabupaten Tangerang
                    </h1>

                    <p class="mt-4 max-w-2xl text-base leading-7 text-white/90 drop-shadow-sm sm:text-lg">
                        Platform internal untuk absensi siswa, monitoring kelas, rekap laporan, poin pelanggaran, dan tata tertib sekolah dalam satu sistem yang rapi.
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('login') }}" class="btn btn-primary rounded-xl px-6">Masuk</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-outline rounded-xl border-white/40 bg-white/10 px-6 text-white backdrop-blur hover:border-white hover:bg-white hover:text-primary">Daftar</a>
                        @endif
                    </div>

                    <div class="mt-8 grid grid-cols-2 gap-3">
                        <div class="rounded-2xl border border-white/20 bg-white/10 p-4 shadow-sm backdrop-blur">
                            <p class="text-2xl font-bold text-white">{{ number_format($studentCount ?? 0, 0, ',', '.') }}</p>
                            <p class="mt-1 text-xs text-white/70">Siswa</p>
                        </div>
                        <div class="rounded-2xl border border-white/20 bg-white/10 p-4 shadow-sm backdrop-blur">
                            <p class="text-2xl font-bold text-white">{{ number_format($teacherCount ?? 0, 0, ',', '.') }}</p>
                            <p class="mt-1 text-xs text-white/70">Guru</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="fitur" class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-sm font-semibold uppercase tracking-wide text-primary">Fitur utama</p>
                <h2 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">Satu sistem untuk data kehadiran dan kedisiplinan</h2>
                <p class="mt-3 text-sm leading-6 text-slate-600">Dibuat untuk kebutuhan siswa, wali kelas, BK, kesiswaan, dan admin sekolah tanpa memecah data ke banyak sistem.</p>
            </div>

            <div class="mt-8 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <article class="ui-card p-5">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 text-primary">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 11l3 3L22 4"></path>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                        </svg>
                    </div>
                    <h3 class="mt-4 font-semibold text-slate-950">Absensi harian</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Siswa absen, wali kelas memantau status harian, dan rekap bisa ditarik untuk laporan.</p>
                </article>

                <article class="ui-card p-5">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 text-primary">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <h3 class="mt-4 font-semibold text-slate-950">Monitoring kelas</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Guru melihat data siswa sesuai akses: kelas sendiri, tingkat BK, atau seluruh kelas kesiswaan.</p>
                </article>

                <article class="ui-card p-5">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 text-primary">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 9v4"></path>
                            <path d="M12 17h.01"></path>
                            <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"></path>
                        </svg>
                    </div>
                    <h3 class="mt-4 font-semibold text-slate-950">Poin pelanggaran</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Pengajuan pelanggaran BK dan approval kesiswaan membuat poin final tetap terkontrol.</p>
                </article>

                <article class="ui-card p-5">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 text-primary">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <path d="M14 2v6h6"></path>
                            <path d="M16 13H8"></path>
                            <path d="M16 17H8"></path>
                            <path d="M10 9H8"></path>
                        </svg>
                    </div>
                    <h3 class="mt-4 font-semibold text-slate-950">Tata tertib digital</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">PDF tata tertib aktif bisa dipublikasikan dan dibaca siswa langsung dari portal.</p>
                </article>
            </div>
        </section>

        <section id="sekolah" class="bg-white">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 py-12 sm:px-6 lg:grid-cols-[0.9fr_1.1fr] lg:px-8">
                <div class="space-y-4">
                    <p class="text-sm font-semibold uppercase tracking-wide text-primary">Profil sekolah</p>
                    <h2 class="text-3xl font-bold tracking-tight text-slate-950">SMAN 11 Kabupaten Tangerang</h2>
                    <p class="text-sm leading-6 text-slate-600">
                        SMAN 11 Kabupaten Tangerang menggunakan Sentri Siswa untuk membantu pencatatan kehadiran, pemantauan data siswa, dan administrasi kedisiplinan agar lebih terpusat dan mudah diakses oleh warga sekolah.
                    </p>
                </div>

                <div id="kontak" class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <p class="text-sm font-semibold text-slate-950">Lokasi</p>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Jl. K.H. Hasyim Ashari No.Km.1, Sepatan, Kec. Sepatan, Kabupaten Tangerang, Banten 15520</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                        <p class="text-sm font-semibold text-slate-950">Kontak sekolah</p>
                        <p class="mt-2 text-sm leading-6 text-slate-600">(021) 59371391</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-slate-200 bg-slate-950 text-white">
        <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-6 text-sm sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
            <p>Sentri Siswa - SMAN 11 Kabupaten Tangerang</p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('privacy-policy') }}" class="font-semibold text-teal-200 hover:text-white">Kebijakan Privasi</a>
                <a href="{{ route('terms-of-service') }}" class="font-semibold text-teal-200 hover:text-white">Ketentuan Layanan</a>
                <a href="{{ route('login') }}" class="font-semibold text-teal-200 hover:text-white">Masuk ke aplikasi</a>
            </div>
        </div>
    </footer>
</body>
</html>
