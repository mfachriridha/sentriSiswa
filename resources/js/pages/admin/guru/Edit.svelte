<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Guru', href: '/admin/guru' },
            { title: 'Edit', href: '/admin/guru' },
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
        guru,
    }: {
        guru: { id: number; nama: string; email: string | null; peran: string };
    } = $props();

    let form = useForm({
        nama: guru.nama,
        email: guru.email ?? '',
        peran: guru.peran,
        password: '',
    });

    function handleSubmit(e: SubmitEvent) {
        e.preventDefault();
        // Since we are using PUT, we can use Inertia put method or POST method with _method=PUT.
        // useForm.put() handles putting data directly.
        form.put(`/admin/guru/${guru.id}`);
    }
</script>

<AppHead title="Edit Guru" />

<div class="max-w-3xl space-y-6">
    <Heading title="Edit Guru" description={guru.nama} />

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

                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        name="email"
                        type="email"
                        bind:value={form.email}
                    />
                    <InputError message={form.errors.email} class="mt-1" />
                </div>

                <div class="grid gap-2">
                    <Label for="peran">
                        Peran <span class="text-destructive">*</span>
                    </Label>
                    <select
                        id="peran"
                        name="peran"
                        required
                        bind:value={form.peran}
                        class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                    >
                        <option
                            value="wali_kelas"
                            class="bg-background text-foreground"
                            >Wali Kelas</option
                        >
                        <option value="bk" class="bg-background text-foreground"
                            >BK (Bimbingan Konseling)</option
                        >
                        <option
                            value="kesiswaan"
                            class="bg-background text-foreground"
                            >Kesiswaan</option
                        >
                    </select>
                    <InputError message={form.errors.peran} class="mt-1" />
                </div>

                <div class="grid gap-2">
                    <Label for="password">Kata Sandi Baru (opsional)</Label>
                    <Input
                        id="password"
                        name="password"
                        type="password"
                        placeholder="Kosongkan jika tidak diubah"
                        bind:value={form.password}
                    />
                    <InputError message={form.errors.password} class="mt-1" />
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
