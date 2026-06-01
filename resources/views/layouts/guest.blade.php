@include('partials.head')
<body class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-primary/5 font-sans antialiased">
    @yield('content')
    @stack('scripts')
</body>
</html>
