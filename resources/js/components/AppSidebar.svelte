<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import {
        BookUser,
        GraduationCap,
        LayoutGrid,
        LogOut,
        MapPin,
        Settings,
        Timer,
        Users,
    } from '@lucide/svelte';
    import type { Snippet } from 'svelte';
    import NavUser from '@/components/NavUser.svelte';
    import { dashboard as adminDashboard } from '@/routes/admin';
    import { index as guruIndex } from '@/routes/admin/guru';
    import { index as kelasIndex } from '@/routes/admin/kelas';
    import { lokasiAbsen, waktuAbsen } from '@/routes/admin/pengaturan';
    import { index as siswaIndex } from '@/routes/admin/siswa';
    import {
        Sidebar,
        SidebarContent,
        SidebarFooter,
        SidebarGroup,
        SidebarGroupContent,
        SidebarGroupLabel,
        SidebarHeader,
        SidebarMenu,
        SidebarMenuButton,
        SidebarMenuItem,
    } from '@/components/ui/sidebar';
    import { cn } from '@/lib/utils';
    import { currentUrlState } from '@/lib/currentUrl.svelte';
    import { router } from '@inertiajs/svelte';
    import type { Component, Snippet as SnippetType } from 'svelte';

    type NavItem = {
        title: string;
        href: string;
        icon?: Component<SnippetType>;
    };

    let { children }: { children?: Snippet } = $props();

    const pengguna = $derived(page.props.auth?.user);
    const peran = $derived(pengguna?.peran ?? '');
    const url = currentUrlState();

    const menuUtama: NavItem[] = $derived(
        peran === 'admin'
            ? [
                  {
                      title: 'Dashboard',
                      href: adminDashboard(),
                      icon: LayoutGrid,
                  },
                  { title: 'Siswa', href: siswaIndex(), icon: GraduationCap },
                  { title: 'Guru', href: guruIndex(), icon: Users },
                  { title: 'Kelas', href: kelasIndex(), icon: BookUser },
              ]
            : peran === 'wali_kelas'
              ? [
                    {
                        title: 'Dashboard',
                        href: '/wali-kelas/dashboard',
                        icon: LayoutGrid,
                    },
                    {
                        title: 'Kelas Saya',
                        href: '/wali-kelas/kelas-saya',
                        icon: BookUser,
                    },
                ]
              : peran === 'bk'
                ? [
                      {
                          title: 'Dashboard',
                          href: '/bk/dashboard',
                          icon: LayoutGrid,
                      },
                      {
                          title: 'Monitoring',
                          href: '/bk/monitoring',
                          icon: GraduationCap,
                      },
                  ]
                : peran === 'kesiswaan'
                  ? [
                        {
                            title: 'Dashboard',
                            href: '/kesiswaan/dashboard',
                            icon: LayoutGrid,
                        },
                        {
                            title: 'Jenis Pelanggaran',
                            href: '/kesiswaan/jenis-pelanggaran',
                            icon: Settings,
                        },
                    ]
                  : [
                        {
                            title: 'Dashboard',
                            href: '/siswa/dashboard',
                            icon: LayoutGrid,
                        },
                        {
                            title: 'Absensi',
                            href: '/siswa/absensi',
                            icon: Timer,
                        },
                    ],
    );

    const menuPengaturan: NavItem[] = $derived(
        peran === 'admin'
            ? [
                  { title: 'Waktu Absen', href: waktuAbsen(), icon: Timer },
                  { title: 'Lokasi Absen', href: lokasiAbsen(), icon: MapPin },
              ]
            : [],
    );

    const labelMenu = $derived(peran === 'admin' ? 'Manajemen' : 'Menu');

    const handleLogout = () => {
        router.post('/logout');
    };

    function isActive(href: string): boolean {
        if (
            href === '/admin/dashboard' ||
            href === '/wali-kelas/dashboard' ||
            href === '/bk/dashboard' ||
            href === '/kesiswaan/dashboard' ||
            href === '/siswa/dashboard'
        ) {
            return url.currentUrl === href;
        }
        return url.currentUrl.startsWith(href);
    }
</script>

<Sidebar collapsible="none" variant="sidebar">
    <SidebarHeader class="border-b border-sidebar-border">
        <div class="flex h-16 items-center gap-3 px-6">
            <div
                class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary text-primary-foreground"
            >
                <span class="text-base font-bold">SS</span>
            </div>
            <div class="flex flex-col leading-tight">
                <span class="text-base font-semibold text-sidebar-foreground"
                    >Sentri Siswa</span
                >
                <span class="text-xs text-muted-foreground">
                    {peran === 'admin'
                        ? 'Administrator'
                        : peran === 'wali_kelas'
                          ? 'Wali Kelas'
                          : peran === 'bk'
                            ? 'Bimbingan Konseling'
                            : peran === 'kesiswaan'
                              ? 'Kesiswaan'
                              : 'Siswa'}
                </span>
            </div>
        </div>
    </SidebarHeader>

    <SidebarContent>
        <SidebarGroup>
            <SidebarGroupLabel>{labelMenu}</SidebarGroupLabel>
            <SidebarGroupContent>
                <SidebarMenu>
                    {#each menuUtama as item (item.href)}
                        <SidebarMenuItem>
                            <SidebarMenuButton
                                isActive={isActive(item.href)}
                                tooltip={item.title}
                            >
                                {#snippet children(props)}
                                    <Link
                                        {...props}
                                        href={item.href}
                                        class={cn(
                                            props.class,
                                            'flex items-center gap-3',
                                        )}
                                    >
                                        {#if item.icon}<item.icon
                                                class="size-5 shrink-0"
                                            />{/if}
                                        <span class="text-sm">{item.title}</span
                                        >
                                    </Link>
                                {/snippet}
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    {/each}
                </SidebarMenu>
            </SidebarGroupContent>
        </SidebarGroup>

        {#if menuPengaturan.length > 0}
            <SidebarGroup>
                <SidebarGroupLabel>Pengaturan</SidebarGroupLabel>
                <SidebarGroupContent>
                    <SidebarMenu>
                        {#each menuPengaturan as item (item.href)}
                            <SidebarMenuItem>
                                <SidebarMenuButton
                                    isActive={isActive(item.href)}
                                    tooltip={item.title}
                                >
                                    {#snippet children(props)}
                                        <Link
                                            {...props}
                                            href={item.href}
                                            class={cn(
                                                props.class,
                                                'flex items-center gap-3',
                                            )}
                                        >
                                            {#if item.icon}<item.icon
                                                    class="size-5 shrink-0"
                                                />{/if}
                                            <span class="text-sm"
                                                >{item.title}</span
                                            >
                                        </Link>
                                    {/snippet}
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                        {/each}
                    </SidebarMenu>
                </SidebarGroupContent>
            </SidebarGroup>
        {/if}
    </SidebarContent>

    <SidebarFooter class="border-t border-sidebar-border">
        <div class="p-4">
            <NavUser />
            <button
                type="button"
                onclick={handleLogout}
                class="mt-3 flex w-full items-center gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700 transition-colors hover:bg-red-100"
            >
                <LogOut class="size-5" />
                <span>Keluar</span>
            </button>
        </div>
    </SidebarFooter>
</Sidebar>
{@render children?.()}
