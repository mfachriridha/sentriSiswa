<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Siswa', href: '/admin/siswa' },
            { title: 'Edit', href: '/admin/siswa' },
        ],
    };
</script>

<script lang="ts">
    import { useForm, router } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent } from '@/components/ui/card';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import InputError from '@/components/InputError.svelte';
    import { Spinner } from '@/components/ui/spinner';

    let {
        siswa,
        kelas = [],
    }: {
        siswa: {
            id: number;
            nisn: string | null;
            nis: string | null;
            telepon: string | null;
            alamat: string | null;
            pengguna: { nama: string };
            kelas_id: number | null;
        };
        kelas: Array<{ id: number; nama: string; tingkat: string }>;
    } = $props();

    let form = useForm({
        nama: siswa.pengguna?.nama ?? '',
        nisn: siswa.nisn ?? '',
        nis: siswa.nis ?? '',
        kelas_id: siswa.kelas_id !== null ? String(siswa.kelas_id) : '',
        telepon: siswa.telepon ?? '',
        alamat: siswa.alamat ?? '',
    });

    function handleSubmit(e: SubmitEvent) {
        e.preventDefault();
        form.put(`/admin/siswa/${siswa.id}`);
    }
</script>

<AppHead title="Edit Siswa" />

<div class="max-w-3xl space-y-6">
    <Heading title="Edit Siswa" description={siswa.pengguna?.nama} />

    <Card>
        <CardContent class="pt-6">
            <form onsubmit={handleSubmit} class="space-y-6">
                <div class="grid gap-2">
                    <Label for="nama">
                        Nama Lengkap <span class="text-destructive">*</span>
                    </Label>
                    <Input
                        id="nama"
                        name="nama"
                        required
                        bind:value={form.nama}
                    />
                    <InputError message={form.errors.nama} class="mt-1" />
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="nisn">NISN</Label>
                        <Input
                            id="nisn"
                            name="nisn"
                            maxlength={10}
                            bind:value={form.nisn}
                        />
                        <InputError message={form.errors.nisn} class="mt-1" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="nis">NIS</Label>
                        <Input id="nis" name="nis" bind:value={form.nis} />
                        <InputError message={form.errors.nis} class="mt-1" />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="kelas_id">Kelas</Label>
                    <select
                        id="kelas_id"
                        name="kelas_id"
                        bind:value={form.kelas_id}
                        class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                    >
                        <option value="" class="bg-background text-foreground"
                            >Pilih kelas...</option
                        >
                        {#each kelas as k (k.id)}
                            <option
                                value={String(k.id)}
                                class="bg-background text-foreground"
                            >
                                {k.nama} (Tingkat {k.tingkat})
                            </option>
                        {/each}
                    </select>
                    <InputError message={form.errors.kelas_id} class="mt-1" />
                </div>

                <div class="grid gap-2">
                    <Label for="telepon">Telepon</Label>
                    <Input
                        id="telepon"
                        name="telepon"
                        bind:value={form.telepon}
                    />
                    <InputError message={form.errors.telepon} class="mt-1" />
                </div>

                <div class="grid gap-2">
                    <Label for="alamat">Alamat</Label>
                    <Input id="alamat" name="alamat" bind:value={form.alamat} />
                    <InputError message={form.errors.alamat} class="mt-1" />
                </div>

                <div
                    class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"
                >
                    <Button
                        type="button"
                        variant="outline"
                        onclick={() => router.back()}
                        disabled={form.processing}
                    >
                        Batal
                    </Button>
                    <Button type="submit" disabled={form.processing}>
                        {#if form.processing}
                            <Spinner class="mr-2 size-4 animate-spin" />
                        {/if}
                        Simpan Perubahan
                    </Button>
                </div>
            </form>
        </CardContent>
    </Card>
</div>
