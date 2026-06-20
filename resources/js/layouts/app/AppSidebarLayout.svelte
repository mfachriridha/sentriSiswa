<script lang="ts">
    import type { Snippet } from 'svelte';
    import { Menu } from '@lucide/svelte';
    import AppContent from '@/components/AppContent.svelte';
    import AppShell from '@/components/AppShell.svelte';
    import AppSidebar from '@/components/AppSidebar.svelte';
    import AppSidebarHeader from '@/components/AppSidebarHeader.svelte';
    import { Toaster } from '@/components/ui/sonner';
    import { Button } from '@/components/ui/button';
    import { Sheet, SheetContent, SheetTrigger } from '@/components/ui/sheet';
    import { page } from '@inertiajs/svelte';
    import type { BreadcrumbItem } from '@/types';

    let {
        breadcrumbs = [],
        children,
    }: {
        breadcrumbs?: BreadcrumbItem[];
        children?: Snippet;
    } = $props();

    let menuMobileOpen = $state(false);

    $effect(() => {
        // Close mobile menu on page navigation (url change)
        const _url = page.url;
        menuMobileOpen = false;
    });
</script>

<AppShell variant="sidebar">
    <!-- Desktop sidebar -->
    <div class="hidden md:block">
        <AppSidebar />
    </div>

    <AppContent variant="sidebar" class="overflow-x-hidden">
        <!-- Mobile header -->
        <div
            class="flex h-14 items-center gap-3 border-b border-border bg-background px-4 md:hidden"
        >
            <Sheet bind:open={menuMobileOpen}>
                <SheetTrigger>
                    {#snippet children(props)}
                        <Button
                            variant="ghost"
                            size="icon"
                            onclick={props.onclick}
                            aria-label="Buka menu"
                        >
                            <Menu class="size-5" />
                        </Button>
                    {/snippet}
                </SheetTrigger>
                <SheetContent side="left" class="w-72 p-0">
                    <AppSidebar />
                </SheetContent>
            </Sheet>
            <span class="text-sm font-semibold">Sentri Siswa</span>
        </div>

        <!-- Desktop header -->
        <div class="hidden md:block">
            <AppSidebarHeader {breadcrumbs} />
        </div>

        <div class="flex flex-1 flex-col p-4 md:p-6 lg:p-8">
            {@render children?.()}
        </div>
    </AppContent>
    <Toaster />
</AppShell>
