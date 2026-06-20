<script lang="ts">
    import type { Snippet } from 'svelte';

    let {
        title,
        description = '',
        icon,
        action,
    }: {
        title: string;
        description?: string;
        icon?: Snippet;
        action?: Snippet;
    } = $props();
</script>

<div class="flex flex-col items-center justify-center px-4 py-14 text-center">
    {#if icon}
        <div
            class="mb-4 flex size-12 items-center justify-center rounded-lg bg-muted text-muted-foreground"
        >
            {@render icon()}
        </div>
    {/if}
    <h3 class="text-sm font-semibold text-foreground">{title}</h3>
    {#if description}
        <p class="mt-1 max-w-sm text-sm leading-6 text-muted-foreground">
            {description}
        </p>
    {/if}
    {#if action}
        <div class="mt-5">
            {@render action()}
        </div>
    {/if}
</div>
