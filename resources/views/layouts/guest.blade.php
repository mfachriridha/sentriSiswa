@include('partials.head')
<body class="min-h-screen bg-gray-50 font-sans antialiased">
    @yield('content')
    @stack('scripts')
</body>
</html>