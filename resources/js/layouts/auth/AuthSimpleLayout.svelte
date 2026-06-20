<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import type { Snippet } from 'svelte';
    import AppLogoIcon from '@/components/AppLogoIcon.svelte';

    let {
        title = '',
        description = '',
        children,
    }: {
        title?: string;
        description?: string;
        children?: Snippet;
    } = $props();
</script>

<div
    class="flex min-h-svh flex-col items-center justify-center gap-6 bg-slate-50/50 p-6 md:p-10"
>
    <div class="w-full max-w-sm">
        <div class="flex flex-col gap-6">
            <div class="flex flex-col items-center gap-2">
                <Link
                    href="/login"
                    class="flex items-center gap-2 font-semibold text-lg text-primary"
                >
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary text-primary-foreground shadow-md shadow-primary/20"
                    >
                        <span class="text-base font-bold">SS</span>
                    </div>
                    <span
                        class="text-xl font-bold tracking-tight text-foreground"
                        >SentriSiswa</span
                    >
                </Link>
            </div>
            {@render children?.()}
        </div>
    </div>
</div>
