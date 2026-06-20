<script lang="ts">
    import { page } from '@inertiajs/svelte';
    import { User as UserIcon } from '@lucide/svelte';
    import {
        SidebarMenu,
        SidebarMenuButton,
        SidebarMenuItem,
    } from '@/components/ui/sidebar';

    const user = $derived(page.props.auth?.user);
</script>

{#if user}
    <SidebarMenu>
        <SidebarMenuItem>
            <SidebarMenuButton size="lg" class="cursor-default">
                <div
                    class="flex aspect-square size-9 items-center justify-center rounded-full bg-primary/10 text-primary"
                >
                    <UserIcon class="size-4" />
                </div>
                <div class="grid flex-1 text-left text-sm leading-tight">
                    <span class="truncate font-medium">{user.nama}</span>
                    <span class="truncate text-xs text-muted-foreground">
                        {user.peran === 'admin'
                            ? 'Administrator'
                            : user.peran === 'wali_kelas'
                              ? 'Wali Kelas'
                              : user.peran === 'bk'
                                ? 'Bimbingan Konseling'
                                : user.peran === 'kesiswaan'
                                  ? 'Kesiswaan'
                                  : 'Siswa'}
                    </span>
                </div>
            </SidebarMenuButton>
        </SidebarMenuItem>
    </SidebarMenu>
{/if}
