<script lang="ts">
    type ChartItem = {
        label: string;
        value: number;
        variant?:
            | 'primary'
            | 'success'
            | 'warning'
            | 'error'
            | 'info'
            | 'neutral';
    };

    let { title, items = [] }: { title: string; items?: ChartItem[] } =
        $props();

    const maxValue = $derived(Math.max(1, ...items.map((item) => item.value)));

    const variantColors: Record<string, string> = {
        primary: 'bg-primary shadow-xs shadow-primary/20',
        success: 'bg-emerald-500 shadow-xs shadow-emerald-500/20',
        warning: 'bg-amber-500 shadow-xs shadow-amber-500/20',
        error: 'bg-red-500 shadow-xs shadow-red-500/20',
        info: 'bg-sky-500 shadow-xs shadow-sky-500/20',
        neutral: 'bg-slate-500 shadow-xs shadow-slate-500/20',
    };

    function getWidth(value: number): number {
        return maxValue > 0 ? Math.max(4, (value / maxValue) * 100) : 4;
    }
</script>

<div
    class="rounded-xl border border-border bg-card p-5 shadow-xs transition-shadow hover:shadow-sm"
>
    <h2 class="text-sm font-semibold text-foreground tracking-tight">
        {title}
    </h2>
    <div class="mt-5 space-y-4">
        {#each items as item}
            {@const value = item.value ?? 0}
            {@const width = getWidth(value)}
            {@const colorClass =
                variantColors[item.variant ?? 'primary'] ??
                variantColors.primary}
            <div>
                <div
                    class="mb-1.5 flex items-center justify-between gap-3 text-xs"
                >
                    <span class="font-medium text-muted-foreground"
                        >{item.label ?? '-'}</span
                    >
                    <span class="font-semibold text-foreground">{value}</span>
                </div>
                <div class="h-2.5 overflow-hidden rounded-full bg-muted/60">
                    <div
                        class="h-full rounded-full transition-all duration-500 ease-out {colorClass}"
                        style="width: {width}%"
                    ></div>
                </div>
            </div>
        {:else}
            <p class="text-sm text-muted-foreground text-center py-6">
                Belum ada data grafik.
            </p>
        {/each}
    </div>
</div>
