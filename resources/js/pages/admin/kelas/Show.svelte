<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Kelas', href: '/admin/kelas' },
            { title: 'Detail', href: '' },
        ],
    };
</script>

<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { ArrowLeft, BookUser, Mail, Shield, User } from '@lucide/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
    import { Button } from '@/components/ui/button';
    import {
        Card,
        CardContent,
        CardHeader,
        CardTitle,
    } from '@/components/ui/card';

    type SiswaItem = {
        id: number;
        nisn: string | null;
        nis: string | null;
        pengguna: {
            nama: string;
            email: string | null;
        };
    };

    let {
        kelas,
    }: {
        kelas: {
            id: number;
            nama: string;
            tingkat: string;
            wali_kelas_id: number | null;
            wali_kelas?: {
                id: number;
                nama: string;
                email: string | null;
            } | null;
            siswa?: SiswaItem[];
        };
    } = $props();

    const wali = $derived(kelas.wali_kelas);
    const daftarSiswa = $derived(kelas.siswa ?? []);
</script>

<AppHead title={`Kelas ${kelas.nama}`} />

<div class="w-full space-y-6">
    <Heading
        title={`Kelas ${kelas.nama}`}
        description={`Tingkat ${kelas.tingkat}`}
    >
        {#snippet actions()}
            <Button variant="outline" size="sm" asChild>
                {#snippet children(props)}
                    <Link href="/admin/kelas" class={props.class}>
                        <ArrowLeft class="size-4" />
                        Kembali
                    </Link>
                {/snippet}
            </Button>
            <Button size="sm" asChild>
                {#snippet children(props)}
                    <Link
                        href={`/admin/kelas/${kelas.id}/edit`}
                        class={props.class}
                    >
                        Edit Kelas
                    </Link>
                {/snippet}
            </Button>
        {/snippet}
    </Heading>

    <div class="grid gap-6 md:grid-cols-3">
        <!-- Informasi Kelas Card -->
        <Card class="md:col-span-1">
            <CardHeader>
                <CardTitle>Informasi Kelas</CardTitle>
            </CardHeader>
            <CardContent class="space-y-4">
                <div>
                    <span
                        class="text-xs font-medium text-muted-foreground uppercase"
                        >Nama Kelas</span
                    >
                    <p class="text-lg font-semibold text-foreground">
                        {kelas.nama}
                    </p>
                </div>
                <div>
                    <span
                        class="text-xs font-medium text-muted-foreground uppercase"
                        >Tingkat</span
                    >
                    <p class="text-lg font-semibold text-foreground">
                        {kelas.tingkat}
                    </p>
                </div>
                <div class="border-t border-border pt-4">
                    <span
                        class="text-xs font-medium text-muted-foreground uppercase block mb-2"
                        >Wali Kelas</span
                    >
                    {#if wali}
                        <div class="flex items-center gap-3">
                            <div
                                class="flex size-10 items-center justify-center rounded-full bg-primary/10"
                            >
                                <User class="size-5 text-primary" />
                            </div>
                            <div>
                                <p class="text-sm font-semibold">{wali.nama}</p>
                                <p class="text-xs text-muted-foreground">
                                    {wali.email ?? '-'}
                                </p>
                            </div>
                        </div>
                    {:else}
                        <p class="text-sm text-muted-foreground italic">
                            Belum ditentukan
                        </p>
                    {/if}
                </div>
            </CardContent>
        </Card>

        <!-- Daftar Siswa Card -->
        <Card class="md:col-span-2">
            <CardHeader class="flex flex-row items-center justify-between">
                <div>
                    <CardTitle>Daftar Siswa</CardTitle>
                    <p class="text-sm text-muted-foreground">
                        Total {daftarSiswa.length} siswa terdaftar di kelas ini
                    </p>
                </div>
            </CardHeader>
            <CardContent>
                {#if daftarSiswa.length > 0}
                    <div
                        class="overflow-x-auto rounded-lg border border-border"
                    >
                        <table class="w-full text-sm">
                            <thead class="bg-muted/50 border-b border-border">
                                <tr>
                                    <th
                                        class="px-4 py-2.5 text-left font-medium"
                                        >No.</th
                                    >
                                    <th
                                        class="px-4 py-2.5 text-left font-medium"
                                        >Nama</th
                                    >
                                    <th
                                        class="px-4 py-2.5 text-left font-medium"
                                        >NISN</th
                                    >
                                    <th
                                        class="px-4 py-2.5 text-left font-medium"
                                        >NIS</th
                                    >
                                    <th
                                        class="px-4 py-2.5 text-right font-medium"
                                        >Aksi</th
                                    >
                                </tr>
                            </thead>
                            <tbody>
                                {#each daftarSiswa as item, i (item.id)}
                                    <tr
                                        class="border-b border-border last:border-0 hover:bg-muted/30"
                                    >
                                        <td
                                            class="px-4 py-2.5 text-muted-foreground"
                                            >{i + 1}</td
                                        >
                                        <td class="px-4 py-2.5 font-medium"
                                            >{item.pengguna?.nama ?? '-'}</td
                                        >
                                        <td
                                            class="px-4 py-2.5 text-muted-foreground font-mono"
                                            >{item.nisn ?? '-'}</td
                                        >
                                        <td
                                            class="px-4 py-2.5 text-muted-foreground font-mono"
                                            >{item.nis ?? '-'}</td
                                        >
                                        <td class="px-4 py-2.5 text-right">
                                            <Link
                                                href={`/admin/siswa/${item.id}`}
                                                class="text-xs text-primary hover:underline font-semibold"
                                            >
                                                Detail
                                            </Link>
                                        </td>
                                    </tr>
                                {/each}
                            </tbody>
                        </table>
                    </div>
                {:else}
                    <div
                        class="flex flex-col items-center justify-center py-12 text-center text-muted-foreground"
                    >
                        <BookUser class="mb-2 size-8 opacity-50" />
                        <p class="text-sm">Belum ada siswa di kelas ini</p>
                    </div>
                {/if}
            </CardContent>
        </Card>
    </div>
</div>
