@include('partials.head')
<body class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-primary/5 font-sans antialiased">
    <a href="{{ route('home') }}"
       class="absolute top-4 left-4 z-50 inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-600 shadow-sm transition-colors hover:border-gray-300 hover:bg-gray-50 hover:text-primary">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Kembali ke Beranda
    </a>
    @yield('content')
    @stack('scripts')
</body>
</html>
