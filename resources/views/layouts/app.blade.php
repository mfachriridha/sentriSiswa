@include('partials.head')
<body class="min-h-screen bg-gray-50 font-sans antialiased">
    <div class="flex min-h-screen">
        @include('partials.sidebar')

        <div class="flex min-h-screen min-w-0 flex-1 flex-col lg:ml-56">
            @include('partials.header')

            {{-- Ruang bawah ekstra di ponsel supaya isi halaman tidak tertutup bilah navigasi. --}}
            <main class="flex-1 p-4 pb-24 sm:p-5 sm:pb-24 lg:p-6 lg:pb-6">
                @yield('content')
            </main>

            @include('partials.footer')
        </div>
    </div>

    @include('partials.bottom-nav')

    <x-confirm-modal />
    @include('partials.scripts')
</body>
</html>
