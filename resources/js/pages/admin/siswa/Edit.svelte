<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Siswa', href: '/admin/siswa' },
            { title: 'Edit', href: '/admin/siswa' },
        ],
    };
</script>

<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent } from '@/components/ui/card';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';

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
</script>

<AppHead title="Edit Siswa" />

<div class="max-w-3xl space-y-6">
    <Heading title="Edit Siswa" description={siswa.pengguna?.nama} />

    <Card>
        <CardContent class="pt-6">
            <form
                method="post"
                action={`/admin/siswa/${siswa.id}`}
                onsubmit={(e) => {
                    e.preventDefault();
                    const form = e.target as HTMLFormElement;
                    const data = new FormData(form);
                    data.append('_method', 'PUT');
                    router.post(`/admin/siswa/${siswa.id}`, data);
                }}
                class="space-y-6"
            >
                <div class="grid gap-2">
                    <Label for="nama"
                        >Nama Lengkap <span class="text-destructive">*</span
                        ></Label
                    >
                    <Input
                        id="nama"
                        name="nama"
                        required
                        value={siswa.pengguna?.nama ?? ''}
                    />
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="nisn">NISN</Label>
                        <Input
                            id="nisn"
                            name="nisn"
                            maxlength={10}
                            value={siswa.nisn ?? ''}
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label for="nis">NIS</Label>
                        <Input id="nis" name="nis" value={siswa.nis ?? ''} />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="kelas_id">Kelas</Label>
                    <select
                        id="kelas_id"
                        name="kelas_id"
                        class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm"
                    >
                        <option value="">Pilih kelas...</option>
                        {#each kelas as k (k.id)}
                            <option
                                value={k.id}
                                selected={siswa.kelas_id === k.id}
                            >
                                {k.nama} (Tingkat {k.tingkat})
                            </option>
                        {/each}
                    </select>
                </div>

                <div class="grid gap-2">
                    <Label for="telepon">Telepon</Label>
                    <Input
                        id="telepon"
                        name="telepon"
                        value={siswa.telepon ?? ''}
                    />
                </div>

                <div class="grid gap-2">
                    <Label for="alamat">Alamat</Label>
                    <Input
                        id="alamat"
                        name="alamat"
                        value={siswa.alamat ?? ''}
                    />
                </div>

                <div
                    class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"
                >
                    <Button
                        type="button"
                        variant="outline"
                        onclick={() => router.back()}
                    >
                        Batal
                    </Button>
                    <Button type="submit">Simpan Perubahan</Button>
                </div>
            </form>
        </CardContent>
    </Card>
</div>
