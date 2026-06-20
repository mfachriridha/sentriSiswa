<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Kelas', href: '/admin/kelas' },
            { title: 'Tambah', href: '/admin/kelas/create' },
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
        waliKelas = [],
    }: {
        waliKelas: Array<{ id: number; nama: string }>;
    } = $props();

    let form = useForm({
        nama: '',
        tingkat: '',
        wali_kelas_id: '',
    });

    function handleSubmit(e: SubmitEvent) {
        e.preventDefault();
        form.post('/admin/kelas');
    }
</script>

<AppHead title="Tambah Kelas" />

<div class="max-w-3xl space-y-6">
    <Heading title="Tambah Kelas" description="Tambahkan kelas baru" />

    <Card>
        <CardContent class="pt-6">
            <form onsubmit={handleSubmit} class="space-y-6">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="nama">
                            Nama Kelas <span class="text-destructive">*</span>
                        </Label>
                        <Input
                            id="nama"
                            name="nama"
                            required
                            placeholder="Contoh: X IPA 1"
                            bind:value={form.nama}
                        />
                        <InputError message={form.errors.nama} class="mt-1" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="tingkat">
                            Tingkat <span class="text-destructive">*</span>
                        </Label>
                        <select
                            id="tingkat"
                            name="tingkat"
                            required
                            bind:value={form.tingkat}
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                        >
                            <option
                                value=""
                                class="bg-background text-foreground"
                                >Pilih...</option
                            >
                            <option
                                value="10"
                                class="bg-background text-foreground">10</option
                            >
                            <option
                                value="11"
                                class="bg-background text-foreground">11</option
                            >
                            <option
                                value="12"
                                class="bg-background text-foreground">12</option
                            >
                        </select>
                        <InputError
                            message={form.errors.tingkat}
                            class="mt-1"
                        />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="wali_kelas_id">Wali Kelas</Label>
                    <select
                        id="wali_kelas_id"
                        name="wali_kelas_id"
                        bind:value={form.wali_kelas_id}
                        class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                    >
                        <option value="" class="bg-background text-foreground"
                            >Pilih wali kelas...</option
                        >
                        {#each waliKelas as w (w.id)}
                            <option
                                value={String(w.id)}
                                class="bg-background text-foreground"
                                >{w.nama}</option
                            >
                        {/each}
                    </select>
                    <InputError
                        message={form.errors.wali_kelas_id}
                        class="mt-1"
                    />
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
                        Simpan
                    </Button>
                </div>
            </form>
        </CardContent>
    </Card>
</div>
