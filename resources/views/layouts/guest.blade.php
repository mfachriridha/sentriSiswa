<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Sentri Siswa' }}</title>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen flex flex-col">
    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="bg-white border-t border-gray-200 py-4 px-8 text-center text-xs text-gray-400">
        &copy; {{ date('Y') }} Sentri Siswa - Muhammad Fachri Ridha
    </footer>

    @stack('scripts')
</body>
</html>
