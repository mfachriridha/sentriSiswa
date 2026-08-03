<header class="sticky top-0 z-30 shrink-0 border-b border-gray-200 bg-white">
    <div class="flex h-14 items-center justify-between gap-4 px-4 sm:px-5 lg:px-6">
        <div class="flex min-w-0 items-center gap-3">
            {{-- Logo cuma tampil di ponsel: di layar lebar sudah ada di kepala sidebar. --}}
            <img src="{{ asset('storage/assets/logo/logo-sidebar.png') }}" alt="Sentri Siswa"
                 class="h-8 w-auto shrink-0 rounded-lg border border-gray-200 lg:hidden">
            <h1 class="truncate text-base font-semibold text-gray-800">@yield('title', 'Sentri Siswa')</h1>
        </div>
        <div class="flex items-center gap-3">
            <span class="max-w-32 truncate text-sm text-gray-500 sm:max-w-64">{{ auth()->user()->nama }}</span>
            <form method="POST" action="{{ route('logout') }}" class="lg:hidden">
                @csrf
                <button type="submit" title="Keluar" class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-red-50 p-1.5 text-red-600 transition-colors hover:bg-red-100 focus:outline-none">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
</header>
