<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Siswa', href: '/admin/siswa' },
            { title: 'Detail', href: '/admin/siswa' },
        ],
    };
</script>

<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
    import { Button } from '@/components/ui/button';
    import {
        Card,
        CardContent,
        CardHeader,
        CardTitle,
    } from '@/components/ui/card';

    let {
        siswa,
    }: {
        siswa: {
            id: number;
            nisn: string | null;
            nis: string | null;
            telepon: string | null;
            alamat: string | null;
            foto: string | null;
            pengguna: { nama: string; email: string | null; status: string };
            kelas: { nama: string; tingkat: string } | null;
            biodata: {
                tempat_lahir: string | null;
                tanggal_lahir: string | null;
                jenis_kelamin: string | null;
                agama: string | null;
            } | null;
        };
    } = $props();
</script>

<AppHead title="Detail Siswa" />

<div class="max-w-5xl space-y-6">
    <Heading
        title={siswa.pengguna?.nama ?? 'Detail Siswa'}
        description="Data lengkap siswa"
    >
        {#snippet actions()}
            <Button variant="outline" size="sm" asChild>
                {#snippet children(props)}
                    <Link
                        href={`/admin/siswa/${siswa.id}/biodata/edit`}
                        class={props.class}
                    >
                        Edit Biodata
                    </Link>
                {/snippet}
            </Button>
            <Button size="sm" asChild>
                {#snippet children(props)}
                    <Link
                        href={`/admin/siswa/${siswa.id}/edit`}
                        class={props.class}
                    >
                        Edit
                    </Link>
                {/snippet}
            </Button>
        {/snippet}
    </Heading>

    <div class="grid gap-6 md:grid-cols-2">
        <Card>
            <CardHeader>
                <CardTitle>Data Pokok</CardTitle>
            </CardHeader>
            <CardContent>
                <dl class="space-y-3 text-sm">
                    <div
                        class="flex justify-between gap-4 border-b border-border pb-2"
                    >
                        <dt class="text-muted-foreground">Nama</dt>
                        <dd class="text-right font-medium">
                            {siswa.pengguna?.nama ?? '-'}
                        </dd>
                    </div>
                    <div
                        class="flex justify-between gap-4 border-b border-border pb-2"
                    >
                        <dt class="text-muted-foreground">Email</dt>
                        <dd class="text-right">
                            {siswa.pengguna?.email ?? 'Belum terdaftar'}
                        </dd>
                    </div>
                    <div
                        class="flex justify-between gap-4 border-b border-border pb-2"
                    >
                        <dt class="text-muted-foreground">NISN</dt>
                        <dd class="text-right">{siswa.nisn ?? '-'}</dd>
                    </div>
                    <div
                        class="flex justify-between gap-4 border-b border-border pb-2"
                    >
                        <dt class="text-muted-foreground">NIS</dt>
                        <dd class="text-right">{siswa.nis ?? '-'}</dd>
                    </div>
                    <div
                        class="flex justify-between gap-4 border-b border-border pb-2"
                    >
                        <dt class="text-muted-foreground">Kelas</dt>
                        <dd class="text-right">{siswa.kelas?.nama ?? '-'}</dd>
                    </div>
                    <div
                        class="flex justify-between gap-4 border-b border-border pb-2"
                    >
                        <dt class="text-muted-foreground">Telepon</dt>
                        <dd class="text-right">{siswa.telepon ?? '-'}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-muted-foreground">Status</dt>
                        <dd class="text-right">
                            <span
                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {siswa
                                    .pengguna?.status === 'terdaftar'
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-yellow-100 text-yellow-700'}"
                            >
                                {siswa.pengguna?.status === 'terdaftar'
                                    ? 'Terdaftar'
                                    : 'Belum Terdaftar'}
                            </span>
                        </dd>
                    </div>
                </dl>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Biodata</CardTitle>
            </CardHeader>
            <CardContent>
                {#if siswa.biodata}
                    <dl class="space-y-3 text-sm">
                        <div
                            class="flex justify-between gap-4 border-b border-border pb-2"
                        >
                            <dt class="text-muted-foreground">Tempat Lahir</dt>
                            <dd class="text-right">
                                {siswa.biodata.tempat_lahir ?? '-'}
                            </dd>
                        </div>
                        <div
                            class="flex justify-between gap-4 border-b border-border pb-2"
                        >
                            <dt class="text-muted-foreground">Tanggal Lahir</dt>
                            <dd class="text-right">
                                {siswa.biodata.tanggal_lahir ?? '-'}
                            </dd>
                        </div>
                        <div
                            class="flex justify-between gap-4 border-b border-border pb-2"
                        >
                            <dt class="text-muted-foreground">Jenis Kelamin</dt>
                            <dd class="text-right">
                                {siswa.biodata.jenis_kelamin === 'L'
                                    ? 'Laki-laki'
                                    : siswa.biodata.jenis_kelamin === 'P'
                                      ? 'Perempuan'
                                      : '-'}
                            </dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-muted-foreground">Agama</dt>
                            <dd class="text-right">
                                {siswa.biodata.agama ?? '-'}
                            </dd>
                        </div>
                    </dl>
                {:else}
                    <p class="text-sm text-muted-foreground">
                        Biodata belum diisi.
                    </p>
                    <Button variant="outline" size="sm" class="mt-4" asChild>
                        {#snippet children(props)}
                            <Link
                                href={`/admin/siswa/${siswa.id}/biodata/edit`}
                                class={props.class}
                            >
                                Isi Biodata
                            </Link>
                        {/snippet}
                    </Button>
                {/if}
            </CardContent>
        </Card>
    </div>
</div>
