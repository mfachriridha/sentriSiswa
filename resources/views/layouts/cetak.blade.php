@include('partials.head')
<body class="bg-slate-100 font-sans antialiased">

    {{-- Bilah ini hanya untuk di layar; saat dicetak ia disembunyikan. --}}
    <div class="cetak-sembunyi sticky top-0 z-10 border-b border-slate-200 bg-white/90 backdrop-blur">
        <div class="mx-auto flex max-w-5xl flex-wrap items-center justify-between gap-3 px-6 py-3">
            <div>
                <p class="text-sm font-semibold text-slate-800">@yield('judul-cetak')</p>
                <p class="text-xs text-slate-500">
                    Tekan Cetak, lalu pilih <span class="font-semibold">Simpan sebagai PDF</span> pada tujuan cetak.
                </p>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" onclick="window.close()"
                        class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">
                    Tutup
                </button>
                <button type="button" onclick="window.print()"
                        class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark transition-colors cursor-pointer">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Cetak / Simpan PDF
                </button>
            </div>
        </div>
    </div>

    <main class="mx-auto my-6 max-w-5xl bg-white p-10 shadow-sm print:my-0 print:max-w-none print:p-0 print:shadow-none">
        @yield('content')
    </main>

    @push('scripts')
        <script>
            // Dialog cetaknya dibuka sendiri supaya terasa seperti tombol ekspor yang
            // lama. Kalau ditutup, halamannya tetap ada beserta tombol Cetak.
            window.addEventListener('load', () => window.print());
        </script>
    @endpush

    @include('partials.scripts')
</body>
</html>
