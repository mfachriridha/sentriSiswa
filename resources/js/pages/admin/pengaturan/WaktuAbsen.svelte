<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Pengaturan', href: '/admin/pengaturan/waktu-absen' },
            { title: 'Waktu Absen', href: '/admin/pengaturan/waktu-absen' },
        ],
    };
</script>

<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent } from '@/components/ui/card';
    import { Label } from '@/components/ui/label';

    let {
        waktuMulai = '06:30',
        waktuSelesai = '07:00',
        toleransiTerlambat = 15,
    }: {
        waktuMulai?: string;
        waktuSelesai?: string;
        toleransiTerlambat?: number;
    } = $props();

    const jamMulai = $derived(waktuMulai.split(':')[0] ?? '06');
    const menitMulai = $derived(waktuMulai.split(':')[1] ?? '30');
    const jamSelesai = $derived(waktuSelesai.split(':')[0] ?? '07');
    const menitSelesai = $derived(waktuSelesai.split(':')[1] ?? '00');

    const jamOptions = Array.from({ length: 24 }, (_, i) =>
        String(i).padStart(2, '0'),
    );
    const menitOptions = Array.from({ length: 60 }, (_, i) =>
        String(i).padStart(2, '0'),
    );
    const toleransiOptions = [0, 5, 10, 15, 20, 30, 45, 60, 90, 120];
</script>

<AppHead title="Pengaturan Waktu Absen" />

<div class="max-w-3xl space-y-6">
    <Heading
        title="Waktu Absen"
        description="Atur jam mulai, jam selesai, dan toleransi terlambat"
    />

    <Card>
        <CardContent class="pt-6">
            <form
                method="post"
                action="/admin/pengaturan/waktu-absen"
                onsubmit={(e) => {
                    e.preventDefault();
                    const form = e.target as HTMLFormElement;
                    const data = new FormData(form);
                    data.append('_method', 'PUT');
                    router.post('/admin/pengaturan/waktu-absen', data);
                }}
                class="space-y-6"
            >
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label>Jam Mulai</Label>
                        <div class="flex gap-2">
                            <select
                                name="jam_mulai"
                                class="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm"
                            >
                                {#each jamOptions as jam (jam)}
                                    <option
                                        value={jam}
                                        selected={jam === jamMulai}
                                        >{jam}</option
                                    >
                                {/each}
                            </select>
                            <span class="self-center">:</span>
                            <select
                                name="menit_mulai"
                                class="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm"
                            >
                                {#each menitOptions as menit (menit)}
                                    <option
                                        value={menit}
                                        selected={menit === menitMulai}
                                        >{menit}</option
                                    >
                                {/each}
                            </select>
                        </div>
                    </div>
                    <div class="grid gap-2">
                        <Label>Jam Selesai</Label>
                        <div class="flex gap-2">
                            <select
                                name="jam_selesai"
                                class="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm"
                            >
                                {#each jamOptions as jam (jam)}
                                    <option
                                        value={jam}
                                        selected={jam === jamSelesai}
                                        >{jam}</option
                                    >
                                {/each}
                            </select>
                            <span class="self-center">:</span>
                            <select
                                name="menit_selesai"
                                class="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm"
                            >
                                {#each menitOptions as menit (menit)}
                                    <option
                                        value={menit}
                                        selected={menit === menitSelesai}
                                        >{menit}</option
                                    >
                                {/each}
                            </select>
                        </div>
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="toleransi_terlambat"
                        >Toleransi Terlambat (menit)</Label
                    >
                    <select
                        id="toleransi_terlambat"
                        name="toleransi_terlambat"
                        class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm"
                    >
                        {#each toleransiOptions as t (t)}
                            <option
                                value={t}
                                selected={t === toleransiTerlambat}
                                >{t} menit</option
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
                        onclick={() => router.back()}>Kembali</Button
                    >
                    <Button type="submit">Simpan</Button>
                </div>
            </form>
        </CardContent>
    </Card>
</div>
