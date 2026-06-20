<script module lang="ts">
    export const layout = {
        breadcrumbs: [{ title: 'Kelas', href: '/admin/kelas' }],
    };
</script>

<script lang="ts">
    import { Link, router } from '@inertiajs/svelte';
    import { BookUser, Plus } from '@lucide/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';

    let {
        kelas,
        cari = '',
    }: {
        kelas: {
            data: Array<{
                id: number;
                nama: string;
                tingkat: string;
                siswa_count: number;
                waliKelas: { nama: string } | null;
            }>;
            links: Array<{
                url: string | null;
                label: string;
                active: boolean;
            }>;
            last_page: number;
        };
        cari?: string;
    } = $props();

    let searchTimeout: ReturnType<typeof setTimeout>;

    function handleSearch(event: Event) {
        const value = (event.target as HTMLInputElement).value;
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            router.get(
                '/admin/kelas',
                { cari: value },
                { preserveScroll: true, preserveState: true },
            );
        }, 300);
    }

    function handleDelete(id: number) {
        if (confirm('Yakin ingin menghapus kelas ini?')) {
            router.delete(`/admin/kelas/${id}`);
        }
    }
</script>

<AppHead title="Manajemen Kelas" />

<div class="w-full space-y-6">
    <Heading title="Manajemen Kelas" description="Kelola data kelas sekolah">
        {#snippet actions()}
            <Button size="sm" asChild>
                {#snippet children(props)}
                    <Link href="/admin/kelas/create" class={props.class}>
                        <Plus class="size-4" />
                        Tambah Kelas
                    </Link>
                {/snippet}
            </Button>
        {/snippet}
    </Heading>

    <Input
        type="text"
        placeholder="Cari nama kelas..."
        value={cari}
        oninput={handleSearch}
        class="max-w-sm"
    />

    <div class="overflow-x-auto rounded-lg border border-border bg-card">
        <table class="w-full text-sm">
            <thead class="border-b border-border bg-muted/50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium">Nama Kelas</th>
                    <th class="px-4 py-3 text-left font-medium">Tingkat</th>
                    <th class="px-4 py-3 text-left font-medium">Wali Kelas</th>
                    <th class="px-4 py-3 text-left font-medium">Jumlah Siswa</th
                    >
                    <th class="px-4 py-3 text-right font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody>
                {#each kelas.data as item (item.id)}
                    <tr
                        class="border-b border-border last:border-b-0 hover:bg-muted/30"
                    >
                        <td class="px-4 py-3 font-medium">{item.nama}</td>
                        <td class="px-4 py-3">{item.tingkat}</td>
                        <td class="px-4 py-3">{item.waliKelas?.nama ?? '-'}</td>
                        <td class="px-4 py-3">{item.siswa_count}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-3 text-sm">
                                <Link
                                    href={`/admin/kelas/${item.id}`}
                                    class="text-primary hover:underline"
                                    >Detail</Link
                                >
                                <Link
                                    href={`/admin/kelas/${item.id}/edit`}
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
                            colspan="5"
                            class="px-4 py-12 text-center text-muted-foreground"
                        >
                            <BookUser class="mx-auto mb-2 size-8 opacity-50" />
                            Belum ada data kelas
                        </td>
                    </tr>
                {/each}
            </tbody>
        </table>
    </div>

    {#if kelas.last_page > 1}
        <div class="flex items-center justify-center gap-1">
            {#each kelas.links as link (link.label)}
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
