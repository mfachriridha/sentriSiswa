@include('partials.head')
<body class="relative min-h-screen overflow-x-hidden bg-slate-900 font-sans antialiased flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <!-- Decorative background elements -->
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-primary/20 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-teal-500/10 blur-3xl"></div>
        <div class="absolute top-1/2 left-1/4 w-80 h-80 rounded-full bg-indigo-500/10 blur-3xl"></div>
    </div>

    <!-- Floating Back Button -->
    <a href="{{ route('home') }}"
       class="absolute top-6 left-6 z-50 inline-flex items-center gap-2 rounded-xl border border-slate-700 bg-slate-800/80 px-4 py-2.5 text-xs font-bold text-slate-300 shadow-lg backdrop-blur-md transition-all duration-300 hover:border-slate-600 hover:bg-slate-700 hover:text-white active:scale-95">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Beranda
    </a>

    <div class="relative z-10 w-full">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
