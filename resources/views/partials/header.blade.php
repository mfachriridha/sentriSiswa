<header class="sticky top-0 z-30 shrink-0 border-b border-gray-200 bg-white">
    <div class="flex h-14 items-center justify-between gap-4 px-4 sm:px-5 lg:px-6">
        <div class="flex min-w-0 items-center gap-3">
            <button type="button"
                    class="rounded-lg p-2 text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-700 lg:hidden"
                    @click="sidebarOpen = true"
                    aria-label="Buka menu navigasi">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <h1 class="truncate text-base font-semibold text-gray-800">@yield('title', 'Sentri Siswa')</h1>
        </div>
        <span class="max-w-40 truncate text-sm text-gray-500 sm:max-w-64">{{ auth()->user()->nama }}</span>
    </div>
</header>
