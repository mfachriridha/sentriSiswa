<script lang="ts">
    let {
        title,
        description = '',
        variant = 'default',
        actions = null,
    }: {
        title: string;
        description?: string;
        variant?: 'default' | 'small';
        actions?: import('svelte').Snippet;
    } = $props();
</script>

<header
    class={variant === 'small'
        ? 'mb-4 flex items-center justify-between gap-4'
        : 'mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between'}
>
    <div class="space-y-1">
        <h2
            class={variant === 'small'
                ? 'text-lg font-semibold'
                : 'text-2xl font-bold tracking-tight'}
        >
            {title}
        </h2>
        {#if description}
            <p class="text-sm text-muted-foreground">{description}</p>
        {/if}
    </div>
    {#if actions}
        <div class="flex shrink-0 items-center gap-2">
            {@render actions()}
        </div>
    {/if}
</header>
