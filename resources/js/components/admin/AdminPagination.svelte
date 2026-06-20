<script lang="ts">
    import { Link } from '@inertiajs/svelte';

    let {
        links = [],
    }: {
        links: Array<{ url: string | null; label: string; active: boolean }>;
    } = $props();
</script>

{#if links.length > 0}
    <nav
        class="flex flex-wrap items-center justify-center gap-2 border-t border-border px-4 py-3"
    >
        {#each links as link, index (`${link.label}-${index}`)}
            {#if link.url}
                <Link
                    href={link.url}
                    preserveScroll
                    preserveState
                    class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border px-3 text-sm transition-colors {link.active
                        ? 'border-primary bg-primary text-primary-foreground'
                        : 'border-border bg-background text-foreground hover:bg-muted'}"
                >
                    {@html link.label}
                </Link>
            {:else}
                <span
                    class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border border-border px-3 text-sm text-muted-foreground"
                >
                    {@html link.label}
                </span>
            {/if}
        {/each}
    </nav>
{/if}
