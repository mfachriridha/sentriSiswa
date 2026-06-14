@include('partials.head')
<body class="min-h-screen bg-gray-50 font-sans antialiased">
    <div class="flex min-h-screen" x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false">
        <div x-show="sidebarOpen"
             x-cloak
             x-transition.opacity
             class="fixed inset-0 z-[1000] bg-gray-900/40 lg:hidden"
             @click="sidebarOpen = false"></div>
        @include('partials.sidebar')

        <div class="flex min-h-screen min-w-0 flex-1 flex-col lg:ml-56">
            @include('partials.header')

            <main class="flex-1 p-4 sm:p-5 lg:p-6">
                @yield('content')
            </main>

            @include('partials.footer')
        </div>
    </div>

    <x-confirm-modal />
    @include('partials.scripts')
</body>
</html>
