<script lang="ts">
    import type { Snippet } from 'svelte';

    let {
        children,
        footer,
    }: {
        children?: Snippet;
        footer?: Snippet;
    } = $props();
</script>

<div class="overflow-hidden rounded-lg border border-border bg-card shadow-sm">
    <div class="overflow-x-auto">
        {@render children?.()}
    </div>
    {#if footer}
        {@render footer()}
    {/if}
</div>
