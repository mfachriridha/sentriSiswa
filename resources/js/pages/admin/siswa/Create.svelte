<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Siswa', href: '/admin/siswa' },
            { title: 'Tambah', href: '/admin/siswa/create' },
        ],
    };
</script>

<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
    import InputError from '@/components/InputError.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent } from '@/components/ui/card';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';

    let {
        kelas = [],
    }: {
        kelas: Array<{ id: number; nama: string; tingkat: string }>;
    } = $props();
</script>

<AppHead title="Tambah Siswa" />

<div class="max-w-3xl space-y-6">
    <Heading title="Tambah Siswa" description="Isi data siswa baru" />

    <Card>
        <CardContent class="pt-6">
            <form
                method="post"
                action="/admin/siswa"
                onsubmit={(e) => {
                    e.preventDefault();
                    const form = e.target as HTMLFormElement;
                    router.post('/admin/siswa', new FormData(form));
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
                        placeholder="Nama lengkap siswa"
                    />
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="nisn">NISN</Label>
                        <Input
                            id="nisn"
                            name="nisn"
                            maxlength={10}
                            placeholder="10 digit NISN"
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label for="nis">NIS</Label>
                        <Input id="nis" name="nis" placeholder="NIS" />
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
                            <option value={k.id}
                                >{k.nama} (Tingkat {k.tingkat})</option
                            >
                        {/each}
                    </select>
                </div>

                <div class="grid gap-2">
                    <Label for="telepon">Telepon</Label>
                    <Input
                        id="telepon"
                        name="telepon"
                        placeholder="No. telepon"
                    />
                </div>

                <div class="grid gap-2">
                    <Label for="alamat">Alamat</Label>
                    <Input id="alamat" name="alamat" placeholder="Alamat" />
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
                    <Button type="submit">Simpan</Button>
                </div>
            </form>
        </CardContent>
    </Card>
</div>
