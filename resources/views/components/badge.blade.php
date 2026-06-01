@props(['variant' => 'neutral'])

@php
    $colors = match ($variant) {
        'success' => 'bg-green-100 text-green-700',
        'warning' => 'bg-amber-100 text-amber-700',
        'error' => 'bg-red-100 text-red-700',
        'info' => 'bg-blue-100 text-blue-700',
        default => 'bg-gray-100 text-gray-700',
    };
@endphp

<span {{ $attributes->class(['inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium', $colors]) }}>
    {{ $slot }}
</span>
