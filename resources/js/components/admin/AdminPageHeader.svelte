<script lang="ts">
    import type { Snippet } from 'svelte';

    let {
        title,
        description = '',
        eyebrow = '',
        actions,
        meta,
    }: {
        title: string;
        description?: string;
        eyebrow?: string;
        actions?: Snippet;
        meta?: Snippet;
    } = $props();
</script>

<div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
    <div class="min-w-0 space-y-2">
        {#if eyebrow}
            <p
                class="text-xs font-semibold uppercase tracking-wide text-muted-foreground"
            >
                {eyebrow}
            </p>
        {/if}
        <div class="space-y-1">
            <h1
                class="text-2xl font-semibold tracking-tight text-foreground sm:text-3xl"
            >
                {title}
            </h1>
            {#if description}
                <p class="max-w-2xl text-sm leading-6 text-muted-foreground">
                    {description}
                </p>
            {/if}
        </div>
        {#if meta}
            <div class="flex flex-wrap items-center gap-2 pt-1">
                {@render meta()}
            </div>
        {/if}
    </div>

    {#if actions}
        <div
            class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center sm:justify-end"
        >
            {@render actions()}
        </div>
    {/if}
</div>
