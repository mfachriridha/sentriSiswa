<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Siswa', href: '/admin/siswa' },
            { title: 'Detail', href: '/admin/siswa' },
        ],
    };
</script>

<script lang="ts">
    import { Link, router, useForm } from '@inertiajs/svelte';
    import { ArrowLeft, User, Trash2, Camera } from '@lucide/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
    import { Button } from '@/components/ui/button';
    import {
        Card,
        CardContent,
        CardHeader,
        CardTitle,
    } from '@/components/ui/card';
    import InputError from '@/components/InputError.svelte';
    import { Spinner } from '@/components/ui/spinner';

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

    let photoForm = useForm({
        foto: null as File | null,
    });

    function handlePhotoChange(e: Event) {
        const input = e.target as HTMLInputElement;
        if (input.files && input.files[0]) {
            photoForm.foto = input.files[0];
            photoForm.post(`/admin/siswa/${siswa.id}/foto`, {
                forceFormData: true,
                onSuccess: () => {
                    photoForm.reset();
                },
            });
        }
    }

    function handleDeletePhoto() {
        if (confirm('Yakin ingin menghapus foto siswa ini?')) {
            router.delete(`/admin/siswa/${siswa.id}/foto`);
        }
    }
</script>

<AppHead title="Detail Siswa" />

<div class="w-full space-y-6">
    <Heading
        title={siswa.pengguna?.nama ?? 'Detail Siswa'}
        description="Data lengkap siswa"
    >
        {#snippet actions()}
            <Button variant="outline" size="sm" asChild>
                {#snippet children(props)}
                    <Link href="/admin/siswa" class={props.class}>
                        <ArrowLeft class="size-4" />
                        Kembali
                    </Link>
                {/snippet}
            </Button>
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
                        Edit Data
                    </Link>
                {/snippet}
            </Button>
        {/snippet}
    </Heading>

    <div class="grid gap-6 md:grid-cols-3">
        <!-- Foto & Data Pokok Card -->
        <Card class="md:col-span-1">
            <CardContent class="pt-6">
                <!-- Foto Profil Section -->
                <div
                    class="flex flex-col items-center pb-6 border-b border-border mb-6 space-y-4"
                >
                    <div class="relative group">
                        <div
                            class="size-28 rounded-full border-2 border-border overflow-hidden bg-muted flex items-center justify-center relative"
                        >
                            {#if siswa.foto}
                                <img
                                    src={`/storage/${siswa.foto}`}
                                    alt={siswa.pengguna?.nama}
                                    class="size-full object-cover"
                                />
                            {:else}
                                <User class="size-16 text-muted-foreground" />
                            {/if}

                            {#if photoForm.processing}
                                <div
                                    class="absolute inset-0 bg-background/70 flex items-center justify-center"
                                >
                                    <Spinner
                                        class="size-6 animate-spin text-primary"
                                    />
                                </div>
                            {/if}
                        </div>
                        <label
                            for="foto-input"
                            class="absolute bottom-0 right-0 p-1.5 bg-primary text-primary-foreground rounded-full cursor-pointer shadow-md hover:bg-primary/90 transition-colors"
                        >
                            <Camera class="size-4" />
                            <input
                                id="foto-input"
                                type="file"
                                accept="image/*"
                                class="hidden"
                                onchange={handlePhotoChange}
                                disabled={photoForm.processing}
                            />
                        </label>
                    </div>

                    <div class="text-center space-y-1">
                        <h4 class="font-semibold text-base">
                            {siswa.pengguna?.nama}
                        </h4>
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
                    </div>

                    {#if siswa.foto}
                        <Button
                            variant="outline"
                            size="sm"
                            onclick={handleDeletePhoto}
                            class="text-destructive hover:bg-destructive/10 border-destructive"
                        >
                            <Trash2 class="size-4" />
                            Hapus Foto
                        </Button>
                    {/if}
                    <InputError
                        message={photoForm.errors.foto}
                        class="mt-1 text-center"
                    />
                </div>

                <!-- Kontak Ringkas -->
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-muted-foreground">Email</dt>
                        <dd
                            class="text-right font-medium overflow-hidden text-ellipsis whitespace-nowrap max-w-[180px]"
                        >
                            {siswa.pengguna?.email ?? 'Belum terdaftar'}
                        </dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-muted-foreground">Telepon</dt>
                        <dd class="text-right font-medium">
                            {siswa.telepon ?? '-'}
                        </dd>
                    </div>
                </dl>
            </CardContent>
        </Card>

        <!-- Data Pokok Card -->
        <Card class="md:col-span-1">
            <CardHeader>
                <CardTitle>Data Pokok</CardTitle>
            </CardHeader>
            <CardContent>
                <dl class="space-y-4 text-sm">
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
                        <dt class="text-muted-foreground">NISN</dt>
                        <dd class="text-right font-mono font-medium">
                            {siswa.nisn ?? '-'}
                        </dd>
                    </div>
                    <div
                        class="flex justify-between gap-4 border-b border-border pb-2"
                    >
                        <dt class="text-muted-foreground">NIS</dt>
                        <dd class="text-right font-mono font-medium">
                            {siswa.nis ?? '-'}
                        </dd>
                    </div>
                    <div
                        class="flex justify-between gap-4 border-b border-border pb-2"
                    >
                        <dt class="text-muted-foreground">Kelas</dt>
                        <dd class="text-right font-medium">
                            {#if siswa.kelas}
                                {siswa.kelas.nama} (Tingkat {siswa.kelas
                                    .tingkat})
                            {:else}
                                -
                            {/if}
                        </dd>
                    </div>
                    <div class="flex flex-col gap-1 pb-2">
                        <dt class="text-muted-foreground">Alamat</dt>
                        <dd class="text-left font-medium">
                            {siswa.alamat ?? '-'}
                        </dd>
                    </div>
                </dl>
            </CardContent>
        </Card>

        <!-- Biodata Card -->
        <Card class="md:col-span-1">
            <CardHeader>
                <CardTitle>Biodata</CardTitle>
            </CardHeader>
            <CardContent>
                {#if siswa.biodata}
                    <dl class="space-y-4 text-sm">
                        <div
                            class="flex justify-between gap-4 border-b border-border pb-2"
                        >
                            <dt class="text-muted-foreground">Tempat Lahir</dt>
                            <dd class="text-right font-medium">
                                {siswa.biodata.tempat_lahir ?? '-'}
                            </dd>
                        </div>
                        <div
                            class="flex justify-between gap-4 border-b border-border pb-2"
                        >
                            <dt class="text-muted-foreground">Tanggal Lahir</dt>
                            <dd class="text-right font-medium">
                                {siswa.biodata.tanggal_lahir ?? '-'}
                            </dd>
                        </div>
                        <div
                            class="flex justify-between gap-4 border-b border-border pb-2"
                        >
                            <dt class="text-muted-foreground">Jenis Kelamin</dt>
                            <dd class="text-right font-medium">
                                {siswa.biodata.jenis_kelamin === 'L'
                                    ? 'Laki-laki'
                                    : siswa.biodata.jenis_kelamin === 'P'
                                      ? 'Perempuan'
                                      : '-'}
                            </dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-muted-foreground">Agama</dt>
                            <dd class="text-right font-medium">
                                {siswa.biodata.agama ?? '-'}
                            </dd>
                        </div>
                    </dl>
                {:else}
                    <div
                        class="flex flex-col items-center justify-center py-6 text-center text-muted-foreground"
                    >
                        <User class="mb-2 size-8 opacity-50" />
                        <p class="text-xs">Biodata belum diisi.</p>
                        <Button
                            variant="outline"
                            size="sm"
                            class="mt-4"
                            asChild
                        >
                            {#snippet children(props)}
                                <Link
                                    href={`/admin/siswa/${siswa.id}/biodata/edit`}
                                    class={props.class}
                                >
                                    Isi Biodata
                                </Link>
                                {#if false}{/if}
                            {/snippet}
                        </Button>
                    </div>
                {/if}
            </CardContent>
        </Card>
    </div>
</div>
