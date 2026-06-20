<script lang="ts">
    import Breadcrumbs from '@/components/Breadcrumbs.svelte';
    import type { BreadcrumbItem } from '@/types';

    let {
        breadcrumbs = [],
    }: {
        breadcrumbs?: BreadcrumbItem[];
    } = $props();
</script>

<header
    class="flex h-14 shrink-0 items-center gap-2 border-b border-border bg-background px-4 md:px-6"
>
    <div class="flex items-center gap-2 min-w-0">
        {#if breadcrumbs && breadcrumbs.length > 0}
            <Breadcrumbs {breadcrumbs} />
        {/if}
    </div>
</header>
