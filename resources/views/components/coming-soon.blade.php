@props([
    'title',
    'description' => 'Halaman ini sedang disiapkan agar alur kerja lebih rapi dan intuitif.',
    'icon' => 'bi-stars',
])

<section class="card">
    <div class="flex items-start gap-4">
        <div class="mt-1 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
            <i class="bi {{ $icon }} text-xl"></i>
        </div>
        <div class="space-y-1">
            <h2 class="text-xl font-semibold text-slate-900">{{ $title }}</h2>
            <p class="text-sm text-slate-600">{{ $description }}</p>
        </div>
    </div>
</section>
