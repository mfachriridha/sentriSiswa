@props(['name', 'title' => null, 'maxWidth' => 'max-w-lg'])

<div x-data="{ open: false }"
     x-on:open-modal.window="open = $event.detail === '{{ $name }}'"
     x-on:close-modal.window="open = false"
     x-on:keydown.escape.window="open = false">
    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/40 p-4">
        <div x-show="open"
             x-transition
             @click.outside="open = false"
             class="ui-card w-full {{ $maxWidth }} p-5">
            @if ($title)
                <h2 class="text-base font-semibold text-gray-900">{{ $title }}</h2>
            @endif

            <div class="{{ $title ? 'mt-4' : '' }}">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
