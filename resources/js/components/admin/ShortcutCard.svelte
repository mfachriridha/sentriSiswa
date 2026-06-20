<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import type { Snippet } from 'svelte';

    let {
        href,
        title,
        description,
        icon,
    }: {
        href: string;
        title: string;
        description: string;
        icon: Snippet;
    } = $props();
</script>

<Link
    {href}
    class="group flex items-start gap-4 rounded-xl border border-border bg-card p-5 shadow-xs transition-all duration-200 hover:-translate-y-0.5 hover:border-primary/20 hover:shadow-md"
>
    <div
        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-primary/5 text-primary transition-all duration-200 group-hover:bg-primary group-hover:text-primary-foreground group-hover:scale-105"
    >
        {@render icon()}
    </div>
    <div class="space-y-1">
        <h2
            class="text-sm font-semibold text-foreground transition-colors group-hover:text-primary"
        >
            {title}
        </h2>
        <p class="text-xs leading-relaxed text-muted-foreground">
            {description}
        </p>
    </div>
</Link>
