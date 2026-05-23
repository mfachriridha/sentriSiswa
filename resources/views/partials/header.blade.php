<header class="bg-white border-b border-gray-200">
        <div class="flex items-center justify-between px-6 py-5">
            <h1 class="text-xl font-semibold text-gray-800">@yield('title', 'Sentri Siswa')</h1>
            <div class="flex items-center gap-4">
                <span class="text-base text-gray-600">{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 transition-colors">
                    Keluar
                </button>
            </form>
        </div>
    </div>
</header>
