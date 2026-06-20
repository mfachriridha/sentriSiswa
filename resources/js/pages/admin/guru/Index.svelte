<script module lang="ts">
    export const layout = {
        breadcrumbs: [{ title: 'Guru', href: '/admin/guru' }],
    };
</script>

<script lang="ts">
    import { Link, router } from '@inertiajs/svelte';
    import { Plus, Users } from '@lucide/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';

    let {
        guru,
        cari = '',
        peranFilter = '',
    }: {
        guru: {
            data: Array<{
                id: number;
                nama: string;
                email: string | null;
                peran: string;
            }>;
            links: Array<{
                url: string | null;
                label: string;
                active: boolean;
            }>;
            current_page: number;
            last_page: number;
        };
        cari?: string;
        peranFilter?: string;
    } = $props();

    let searchTimeout: ReturnType<typeof setTimeout>;

    function handleSearch(event: Event) {
        const value = (event.target as HTMLInputElement).value;
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            router.get(
                '/admin/guru',
                { cari: value, peran: peranFilter },
                { preserveScroll: true, preserveState: true },
            );
        }, 300);
    }

    function handlePeranFilter(value: string) {
        router.get(
            '/admin/guru',
            { cari, peran: value },
            { preserveScroll: true, preserveState: true },
        );
    }

    const labelPeran: Record<string, string> = {
        wali_kelas: 'Wali Kelas',
        bk: 'BK',
        kesiswaan: 'Kesiswaan',
    };

    function handleDelete(id: number) {
        if (confirm('Yakin ingin menghapus guru ini?')) {
            router.delete(`/admin/guru/${id}`);
        }
    }
</script>

<AppHead title="Manajemen Guru" />

<div class="w-full space-y-6">
    <Heading
        title="Manajemen Guru"
        description="Kelola data guru (Wali Kelas, BK, Kesiswaan)"
    >
        {#snippet actions()}
            <Button size="sm" asChild>
                {#snippet children(props)}
                    <Link href="/admin/guru/create" class={props.class}>
                        <Plus class="size-4" />
                        Tambah Guru
                    </Link>
                {/snippet}
            </Button>
        {/snippet}
    </Heading>

    <div class="flex flex-col gap-2 sm:flex-row">
        <Input
            type="text"
            placeholder="Cari nama atau email..."
            value={cari}
            oninput={handleSearch}
            class="max-w-sm"
        />
        <select
            class="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm"
            value={peranFilter}
            onchange={(e) =>
                handlePeranFilter((e.target as HTMLSelectElement).value)}
        >
            <option value="">Semua Peran</option>
            <option value="wali_kelas">Wali Kelas</option>
            <option value="bk">BK</option>
            <option value="kesiswaan">Kesiswaan</option>
        </select>
    </div>

    <div class="overflow-x-auto rounded-lg border border-border bg-card">
        <table class="w-full text-sm">
            <thead class="border-b border-border bg-muted/50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium">Nama</th>
                    <th class="px-4 py-3 text-left font-medium">Email</th>
                    <th class="px-4 py-3 text-left font-medium">Peran</th>
                    <th class="px-4 py-3 text-right font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody>
                {#each guru.data as item (item.id)}
                    <tr
                        class="border-b border-border last:border-b-0 hover:bg-muted/30"
                    >
                        <td class="px-4 py-3 font-medium">{item.nama}</td>
                        <td class="px-4 py-3">{item.email ?? '-'}</td>
                        <td class="px-4 py-3"
                            >{labelPeran[item.peran] ?? item.peran}</td
                        >
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-3 text-sm">
                                <Link
                                    href={`/admin/guru/${item.id}/edit`}
                                    class="text-primary hover:underline"
                                    >Edit</Link
                                >
                                <button
                                    type="button"
                                    onclick={() => handleDelete(item.id)}
                                    class="text-destructive hover:underline"
                                    >Hapus</button
                                >
                            </div>
                        </td>
                    </tr>
                {:else}
                    <tr>
                        <td
                            colspan="4"
                            class="px-4 py-12 text-center text-muted-foreground"
                        >
                            <Users class="mx-auto mb-2 size-8 opacity-50" />
                            Belum ada data guru
                        </td>
                    </tr>
                {/each}
            </tbody>
        </table>
    </div>

    {#if guru.last_page > 1}
        <div class="flex items-center justify-center gap-1">
            {#each guru.links as link (link.label)}
                {#if link.url}
                    <Link
                        href={link.url}
                        preserveScroll
                        preserveState
                        class="rounded-md border border-border px-3 py-1.5 text-sm transition-colors hover:bg-muted {link.active
                            ? 'bg-primary text-primary-foreground'
                            : ''}"
                    >
                        {@html link.label}
                    </Link>
                {:else}
                    <span
                        class="rounded-md border border-border px-3 py-1.5 text-sm text-muted-foreground"
                        >{@html link.label}</span
                    >
                {/if}
            {/each}
        </div>
    {/if}
</div>
