@include('partials.head')
<body class="min-h-screen bg-gray-50 font-sans antialiased">
    <div class="flex min-h-screen">
        @include('partials.sidebar')

        <div class="flex min-h-screen flex-1 flex-col ml-64">
            @include('partials.header')

            <main class="flex-1 p-6">
                @yield('content')
            </main>

            @include('partials.footer')
        </div>
    </div>

    @include('partials.scripts')
</body>
</html>
