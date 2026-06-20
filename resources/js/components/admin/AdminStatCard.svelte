<script lang="ts">
    import type { Snippet } from 'svelte';

    type Tone = 'slate' | 'emerald' | 'sky' | 'amber' | 'rose';

    const toneClasses: Record<Tone, string> = {
        slate: 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-200',
        emerald:
            'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300',
        sky: 'bg-sky-100 text-sky-700 dark:bg-sky-950 dark:text-sky-300',
        amber: 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300',
        rose: 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300',
    };

    let {
        title,
        value,
        description = '',
        tone = 'slate',
        icon,
    }: {
        title: string;
        value: string | number;
        description?: string;
        tone?: Tone;
        icon?: Snippet;
    } = $props();
</script>

<div class="rounded-lg border border-border bg-card p-4 shadow-sm sm:p-5">
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <p class="text-sm font-medium text-muted-foreground">{title}</p>
            <p
                class="mt-2 text-2xl font-semibold tracking-tight text-card-foreground"
            >
                {value}
            </p>
        </div>
        {#if icon}
            <div
                class="flex size-10 shrink-0 items-center justify-center rounded-lg {toneClasses[
                    tone
                ]}"
            >
                {@render icon()}
            </div>
        {/if}
    </div>
    {#if description}
        <p class="mt-3 text-sm text-muted-foreground">{description}</p>
    {/if}
</div>
