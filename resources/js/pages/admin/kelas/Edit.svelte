<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Kelas', href: '/admin/kelas' },
            { title: 'Edit', href: '/admin/kelas' },
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
        kelas,
        waliKelas = [],
    }: {
        kelas: {
            id: number;
            nama: string;
            tingkat: string;
            wali_kelas_id: number | null;
        };
        waliKelas: Array<{ id: number; nama: string }>;
    } = $props();
</script>

<AppHead title="Edit Kelas" />

<div class="max-w-3xl space-y-6">
    <Heading title="Edit Kelas" description={kelas.nama} />

    <Card>
        <CardContent class="pt-6">
            <form
                method="post"
                action={`/admin/kelas/${kelas.id}`}
                onsubmit={(e) => {
                    e.preventDefault();
                    const form = e.target as HTMLFormElement;
                    const data = new FormData(form);
                    data.append('_method', 'PUT');
                    router.post(`/admin/kelas/${kelas.id}`, data);
                }}
                class="space-y-6"
            >
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="nama"
                            >Nama Kelas <span class="text-destructive">*</span
                            ></Label
                        >
                        <Input
                            id="nama"
                            name="nama"
                            required
                            value={kelas.nama}
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label for="tingkat"
                            >Tingkat <span class="text-destructive">*</span
                            ></Label
                        >
                        <select
                            id="tingkat"
                            name="tingkat"
                            required
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm"
                        >
                            <option value="10" selected={kelas.tingkat === '10'}
                                >10</option
                            >
                            <option value="11" selected={kelas.tingkat === '11'}
                                >11</option
                            >
                            <option value="12" selected={kelas.tingkat === '12'}
                                >12</option
                            >
                        </select>
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="wali_kelas_id">Wali Kelas</Label>
                    <select
                        id="wali_kelas_id"
                        name="wali_kelas_id"
                        class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm"
                    >
                        <option value="">Pilih wali kelas...</option>
                        {#each waliKelas as w (w.id)}
                            <option
                                value={w.id}
                                selected={kelas.wali_kelas_id === w.id}
                                >{w.nama}</option
                            >
                        {/each}
                    </select>
                </div>

                <div
                    class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"
                >
                    <Button
                        type="button"
                        variant="outline"
                        onclick={() => router.back()}>Batal</Button
                    >
                    <Button type="submit">Simpan Perubahan</Button>
                </div>
            </form>
        </CardContent>
    </Card>
</div>
