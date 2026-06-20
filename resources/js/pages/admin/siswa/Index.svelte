<script module lang="ts">
    export const layout = {
        breadcrumbs: [{ title: 'Siswa', href: '/admin/siswa' }],
    };
</script>

<script lang="ts">
    import { Link, router } from '@inertiajs/svelte';
    import { GraduationCap, Plus, Upload } from '@lucide/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import type { PageProps } from '@inertiajs/core';

    let {
        siswa,
        cari = '',
    }: PageProps & {
        siswa: {
            data: Array<{
                id: number;
                nisn: string | null;
                nis: string | null;
                pengguna: { nama: string };
                kelas: { nama: string } | null;
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
    } = $props();

    let searchTimeout: ReturnType<typeof setTimeout>;

    function handleSearch(event: Event) {
        const value = (event.target as HTMLInputElement).value;
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            router.get(
                '/admin/siswa',
                { cari: value },
                { preserveScroll: true, preserveState: true },
            );
        }, 300);
    }

    function handleDelete(id: number) {
        if (confirm('Yakin ingin menghapus siswa ini?')) {
            router.delete(`/admin/siswa/${id}`);
        }
    }
</script>

<AppHead title="Manajemen Siswa" />

<div class="w-full space-y-6">
    <Heading title="Manajemen Siswa" description="Kelola data siswa sekolah">
        {#snippet actions()}
            <Button variant="outline" size="sm" asChild>
                {#snippet children(props)}
                    <Link href="/admin/siswa/impor" class={props.class}>
                        <Upload class="size-4" />
                        Impor
                    </Link>
                {/snippet}
            </Button>
            <Button size="sm" asChild>
                {#snippet children(props)}
                    <Link href="/admin/siswa/create" class={props.class}>
                        <Plus class="size-4" />
                        Tambah Siswa
                    </Link>
                {/snippet}
            </Button>
        {/snippet}
    </Heading>

    <div class="flex items-center gap-2">
        <Input
            type="text"
            placeholder="Cari nama, NISN, atau NIS..."
            value={cari}
            oninput={handleSearch}
            class="max-w-sm"
        />
    </div>

    <div class="overflow-x-auto rounded-lg border border-border bg-card">
        <table class="w-full text-sm">
            <thead class="border-b border-border bg-muted/50">
                <tr>
                    <th class="px-4 py-3 text-left font-medium">Nama</th>
                    <th class="px-4 py-3 text-left font-medium">NISN</th>
                    <th class="px-4 py-3 text-left font-medium">NIS</th>
                    <th class="px-4 py-3 text-left font-medium">Kelas</th>
                    <th class="px-4 py-3 text-right font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody>
                {#each siswa.data as item (item.id)}
                    <tr
                        class="border-b border-border last:border-b-0 hover:bg-muted/30"
                    >
                        <td class="px-4 py-3">{item.pengguna?.nama ?? '-'}</td>
                        <td class="px-4 py-3">{item.nisn ?? '-'}</td>
                        <td class="px-4 py-3">{item.nis ?? '-'}</td>
                        <td class="px-4 py-3">{item.kelas?.nama ?? '-'}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex justify-end gap-3 text-sm">
                                <Link
                                    href={`/admin/siswa/${item.id}`}
                                    class="text-primary hover:underline"
                                    >Detail</Link
                                >
                                <Link
                                    href={`/admin/siswa/${item.id}/edit`}
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
                            <GraduationCap
                                class="mx-auto mb-2 size-8 opacity-50"
                            />
                            Belum ada data siswa
                        </td>
                    </tr>
                {/each}
            </tbody>
        </table>
    </div>

    {#if siswa.last_page > 1}
        <div class="flex items-center justify-center gap-1">
            {#each siswa.links as link (link.label)}
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
                    >
                        {@html link.label}
                    </span>
                {/if}
            {/each}
        </div>
    {/if}
</div>
