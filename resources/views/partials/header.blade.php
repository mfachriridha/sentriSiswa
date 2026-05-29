<header class="sticky top-0 z-30 shrink-0 border-b border-gray-200 bg-white">
        <div class="flex items-center justify-between px-6 py-5">
            <h1 class="text-xl font-semibold text-gray-800">@yield('title', 'Sentri Siswa')</h1>
            <div class="flex items-center gap-4">
                <span class="text-base text-gray-600">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-600 transition-colors hover:border-red-300 hover:bg-red-50 hover:text-red-700">
                    Keluar
                </button>
            </form>
        </div>
    </div>
</header>
