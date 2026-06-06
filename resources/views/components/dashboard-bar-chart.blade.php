@props(['title', 'items' => []])

@php
    $maxValue = max(1, collect($items)->pluck('value')->max() ?? 1);
    $colors = [
        'primary' => 'bg-primary',
        'success' => 'bg-green-500',
        'warning' => 'bg-amber-500',
        'error' => 'bg-red-500',
        'info' => 'bg-blue-500',
        'neutral' => 'bg-gray-500',
    ];
@endphp

<div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
    <h2 class="text-sm font-semibold text-gray-900">{{ $title }}</h2>
    <div class="mt-4 space-y-3">
        @forelse ($items as $item)
            @php
                $value = (int) ($item['value'] ?? 0);
                $width = $maxValue > 0 ? max(4, ($value / $maxValue) * 100) : 4;
                $color = $colors[$item['variant'] ?? 'primary'] ?? $colors['primary'];
            @endphp
            <div>
                <div class="mb-1 flex items-center justify-between gap-3 text-xs">
                    <span class="font-medium text-gray-600">{{ $item['label'] ?? '-' }}</span>
                    <span class="font-semibold text-gray-900">{{ $value }}</span>
                </div>
                <div class="h-2.5 overflow-hidden rounded-full bg-gray-100">
                    <div class="h-full rounded-full {{ $color }}" style="width: {{ $width }}%"></div>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-500">Belum ada data grafik.</p>
        @endforelse
    </div>
</div>
