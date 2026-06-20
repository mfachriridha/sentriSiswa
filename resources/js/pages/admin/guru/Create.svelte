<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Guru', href: '/admin/guru' },
            { title: 'Tambah', href: '/admin/guru/create' },
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
</script>

<AppHead title="Tambah Guru" />

<div class="max-w-3xl space-y-6">
    <Heading
        title="Tambah Guru"
        description="Tambahkan guru baru (Wali Kelas, BK, atau Kesiswaan)"
    />

    <Card>
        <CardContent class="pt-6">
            <form
                method="post"
                action="/admin/guru"
                onsubmit={(e) => {
                    e.preventDefault();
                    const form = e.target as HTMLFormElement;
                    router.post('/admin/guru', new FormData(form));
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
                        placeholder="Nama lengkap"
                    />
                </div>

                <div class="grid gap-2">
                    <Label for="email">Email</Label>
                    <Input
                        id="email"
                        name="email"
                        type="email"
                        placeholder="email@contoh.com"
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
                        <option value="">Pilih peran...</option>
                        <option value="wali_kelas">Wali Kelas</option>
                        <option value="bk">BK (Bimbingan Konseling)</option>
                        <option value="kesiswaan">Kesiswaan</option>
                    </select>
                </div>

                <div class="grid gap-2">
                    <Label for="password">Kata Sandi (opsional)</Label>
                    <Input
                        id="password"
                        name="password"
                        type="password"
                        placeholder="Kosongkan untuk default 'password'"
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
                    <Button type="submit">Simpan</Button>
                </div>
            </form>
        </CardContent>
    </Card>
</div>
