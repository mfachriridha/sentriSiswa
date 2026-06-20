<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Guru', href: '/admin/guru' },
            { title: 'Edit', href: '/admin/guru' },
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
        guru,
    }: {
        guru: { id: number; nama: string; email: string | null; peran: string };
    } = $props();
</script>

<AppHead title="Edit Guru" />

<div class="max-w-3xl space-y-6">
    <Heading title="Edit Guru" description={guru.nama} />

    <Card>
        <CardContent class="pt-6">
            <form
                method="post"
                action={`/admin/guru/${guru.id}`}
                onsubmit={(e) => {
                    e.preventDefault();
                    const form = e.target as HTMLFormElement;
                    const data = new FormData(form);
                    data.append('_method', 'PUT');
                    router.post(`/admin/guru/${guru.id}`, data);
                }}
                class="space-y-6"
            >
                <div class="grid gap-2">
                    <Label for="nama"
                        >Nama Lengkap <span class="text-destructive">*</span
                        ></Label
                    >
                    <Input id="nama" name="nama" required value={guru.nama} />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        name="email"
                        type="email"
                        value={guru.email ?? ''}
                    />
                </div>

                <div class="grid gap-2">
                    <Label for="peran"
                        >Peran <span class="text-destructive">*</span></Label
                    >
                    <select
                        id="peran"
                        name="peran"
                        required
                        class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm"
                    >
                        <option
                            value="wali_kelas"
                            selected={guru.peran === 'wali_kelas'}
                            >Wali Kelas</option
                        >
                        <option value="bk" selected={guru.peran === 'bk'}
                            >BK (Bimbingan Konseling)</option
                        >
                        <option
                            value="kesiswaan"
                            selected={guru.peran === 'kesiswaan'}
                            >Kesiswaan</option
                        >
                    </select>
                </div>

                <div class="grid gap-2">
                    <Label for="password">Kata Sandi Baru (opsional)</Label>
                    <Input
                        id="password"
                        name="password"
                        type="password"
                        placeholder="Kosongkan jika tidak diubah"
                    />
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
