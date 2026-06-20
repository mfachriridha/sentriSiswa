<script lang="ts">
    import type { Snippet } from 'svelte';
    import { cn } from '@/lib/utils';

    let {
        title = '',
        description = '',
        class: className = '',
        headerActions,
        children,
    }: {
        title?: string;
        description?: string;
        class?: string;
        headerActions?: Snippet;
        children?: Snippet;
    } = $props();
</script>

<section
    class={cn('rounded-lg border border-border bg-card shadow-sm', className)}
>
    {#if title || description || headerActions}
        <div
            class="flex flex-col gap-3 border-b border-border px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5"
        >
            <div class="min-w-0">
                {#if title}
                    <h2 class="text-base font-semibold text-card-foreground">
                        {title}
                    </h2>
                {/if}
                {#if description}
                    <p class="mt-1 text-sm text-muted-foreground">
                        {description}
                    </p>
                {/if}
            </div>
            {#if headerActions}
                <div class="flex shrink-0 items-center gap-2">
                    {@render headerActions()}
                </div>
            {/if}
        </div>
    {/if}

    <div class="p-4 sm:p-5">
        {@render children?.()}
    </div>
</section>
