@props(['href', 'title', 'description'])

<a href="{{ $href }}"
   {{ $attributes->class(['group ui-card flex items-start gap-3 p-4 transition hover:-translate-y-0.5 hover:border-primary/30 hover:shadow-md']) }}>
    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary transition-colors group-hover:bg-primary group-hover:text-white">
        {{ $icon }}
    </div>
    <div>
        <h2 class="text-sm font-semibold text-gray-900">{{ $title }}</h2>
        <p class="mt-1 text-xs leading-5 text-gray-500">{{ $description }}</p>
    </div>
</a>
