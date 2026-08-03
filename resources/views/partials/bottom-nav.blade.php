@php
    $menu = \App\Services\MenuNavigasi::untuk(auth()->user());
    $menuUtama = \App\Services\MenuNavigasi::utama($menu);
    $menuLainnya = \App\Services\MenuNavigasi::selebihnya($menu);
    $adaLainnyaAktif = collect($menuLainnya)->contains(fn ($item) => request()->routeIs(...$item['aktif']));
    $badgeLainnya = collect($menuLainnya)->sum(fn ($item) => $item['badge'] ?? 0);
@endphp

{{--
    Navigasi ponsel. Ditaruh di bawah supaya terjangkau jempol; sidebar kiri
    hanya dipakai di layar lebar.
--}}
<div x-data="{ sheetTerbuka: false }" class="lg:hidden">
    @if ($menuLainnya)
        {{-- Lembar "Lainnya": menu yang tidak kebagian tempat di bilah bawah. --}}
        <div x-show="sheetTerbuka" x-cloak x-transition.opacity
             class="fixed inset-0 z-40 bg-gray-900/40"
             @click="sheetTerbuka = false"></div>

        <div x-show="sheetTerbuka" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full"
             class="fixed inset-x-0 bottom-0 z-50 max-h-[75vh] overflow-y-auto rounded-t-2xl border-t border-gray-200 bg-white pb-[env(safe-area-inset-bottom)] shadow-2xl">
            <div class="sticky top-0 flex items-center justify-between border-b border-gray-100 bg-white px-5 py-4">
                <div class="flex min-w-0 items-center gap-3">
                    @if($fotoPengguna = \App\Services\FotoPengguna::url(auth()->user()))
                        <img src="{{ $fotoPengguna }}" alt="{{ auth()->user()->nama }}" class="h-10 w-10 rounded-full object-cover">
                    @else
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 text-sm font-bold text-primary">
                            {{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}
                        </div>
                    @endif
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-gray-900">{{ auth()->user()->nama }}</p>
                        <p class="truncate text-xs text-gray-500">{{ auth()->user()->roleLabel() }}</p>
                    </div>
                </div>
                <button type="button" @click="sheetTerbuka = false"
                        class="rounded-lg p-2 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600"
                        aria-label="Tutup menu lainnya">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <nav class="px-3 py-3">
                <ul class="space-y-1">
                    @foreach ($menuLainnya as $item)
                        @php $aktif = request()->routeIs(...$item['aktif']); @endphp
                        <li>
                            <a href="{{ route($item['route']) }}"
                               class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium transition-colors
                                      {{ $aktif ? 'bg-primary/10 text-primary font-semibold' : 'text-slate-700 hover:bg-slate-50' }}">
                                <x-nav-icon :name="$item['icon']" />
                                <span class="flex-1">{{ $item['label'] }}</span>
                                @if (($item['badge'] ?? 0) > 0)
                                    <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-amber-500 px-1.5 text-xs font-bold leading-none text-white">
                                        {{ $item['badge'] }}
                                    </span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <div class="border-t border-gray-100 px-5 py-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-lg border border-red-200 bg-red-50 px-3 py-3 text-sm font-semibold text-red-700 transition-colors hover:bg-red-100">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    @endif

    <nav class="fixed inset-x-0 bottom-0 z-30 border-t border-gray-200 bg-white shadow-[0_-1px_3px_rgba(0,0,0,0.05)]">
        <ul class="flex h-14 items-stretch">
            @foreach ($menuUtama as $item)
                @php $aktif = request()->routeIs(...$item['aktif']); @endphp
                <li class="flex-1">
                    <a href="{{ route($item['route']) }}"
                       class="relative flex h-full flex-col items-center justify-center gap-0.5 px-1 text-center transition-colors
                              {{ $aktif ? 'text-primary' : 'text-gray-500' }}">
                        <span class="relative">
                            <x-nav-icon :name="$item['icon']" class="h-5 w-5 shrink-0" />
                            @if (($item['badge'] ?? 0) > 0)
                                <span class="absolute -right-2 -top-1 inline-flex h-4 min-w-4 items-center justify-center rounded-full bg-amber-500 px-1 text-[10px] font-bold leading-none text-white">
                                    {{ $item['badge'] }}
                                </span>
                            @endif
                        </span>
                        <span class="text-[10px] font-medium leading-tight {{ $aktif ? 'font-semibold' : '' }}">{{ $item['label'] }}</span>
                        @if ($aktif)
                            <span class="absolute inset-x-3 top-0 h-0.5 rounded-full bg-primary"></span>
                        @endif
                    </a>
                </li>
            @endforeach

            @if ($menuLainnya)
                <li class="flex-1">
                    <button type="button" @click="sheetTerbuka = true"
                            class="relative flex h-full w-full flex-col items-center justify-center gap-0.5 px-1 text-center transition-colors
                                   {{ $adaLainnyaAktif ? 'text-primary' : 'text-gray-500' }}">
                        <span class="relative">
                            <x-nav-icon name="lainnya" class="h-5 w-5 shrink-0" />
                            @if ($badgeLainnya > 0)
                                <span class="absolute -right-2 -top-1 inline-flex h-4 min-w-4 items-center justify-center rounded-full bg-amber-500 px-1 text-[10px] font-bold leading-none text-white">
                                    {{ $badgeLainnya }}
                                </span>
                            @endif
                        </span>
                        <span class="text-[10px] font-medium leading-tight {{ $adaLainnyaAktif ? 'font-semibold' : '' }}">Lainnya</span>
                        @if ($adaLainnyaAktif)
                            <span class="absolute inset-x-3 top-0 h-0.5 rounded-full bg-primary"></span>
                        @endif
                    </button>
                </li>
            @endif
        </ul>
        <div class="h-[env(safe-area-inset-bottom,0px)] bg-white"></div>
    </nav>
</div>
