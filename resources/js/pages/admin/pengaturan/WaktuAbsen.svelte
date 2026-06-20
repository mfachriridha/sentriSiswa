<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Pengaturan', href: '/admin/pengaturan/waktu-absen' },
            { title: 'Waktu Absen', href: '/admin/pengaturan/waktu-absen' },
        ],
    };
</script>

<script lang="ts">
    import { useForm, router } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent } from '@/components/ui/card';
    import { Label } from '@/components/ui/label';
    import InputError from '@/components/InputError.svelte';
    import { Spinner } from '@/components/ui/spinner';

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

    let form = useForm({
        jam_mulai: jamMulai,
        menit_mulai: menitMulai,
        jam_selesai: jamSelesai,
        menit_selesai: menitSelesai,
        toleransi_terlambat: String(toleransiTerlambat),
    });

    function handleSubmit(e: SubmitEvent) {
        e.preventDefault();
        form.put('/admin/pengaturan/waktu-absen');
    }
</script>

<AppHead title="Pengaturan Waktu Absen" />

<div class="max-w-3xl space-y-6">
    <Heading
        title="Waktu Absen"
        description="Atur jam mulai, jam selesai, dan toleransi terlambat"
    />

    <Card>
        <CardContent class="pt-6">
            <form onsubmit={handleSubmit} class="space-y-6">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label>Jam Mulai</Label>
                        <div class="flex gap-2">
                            <select
                                name="jam_mulai"
                                bind:value={form.jam_mulai}
                                class="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                            >
                                {#each jamOptions as jam (jam)}
                                    <option
                                        value={jam}
                                        class="bg-background text-foreground"
                                        >{jam}</option
                                    >
                                {/each}
                            </select>
                            <span class="self-center">:</span>
                            <select
                                name="menit_mulai"
                                bind:value={form.menit_mulai}
                                class="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                            >
                                {#each menitOptions as menit (menit)}
                                    <option
                                        value={menit}
                                        class="bg-background text-foreground"
                                        >{menit}</option
                                    >
                                {/each}
                            </select>
                        </div>
                        <InputError
                            message={form.errors.jam_mulai ||
                                form.errors.menit_mulai}
                            class="mt-1"
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label>Jam Selesai</Label>
                        <div class="flex gap-2">
                            <select
                                name="jam_selesai"
                                bind:value={form.jam_selesai}
                                class="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                            >
                                {#each jamOptions as jam (jam)}
                                    <option
                                        value={jam}
                                        class="bg-background text-foreground"
                                        >{jam}</option
                                    >
                                {/each}
                            </select>
                            <span class="self-center">:</span>
                            <select
                                name="menit_selesai"
                                bind:value={form.menit_selesai}
                                class="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                            >
                                {#each menitOptions as menit (menit)}
                                    <option
                                        value={menit}
                                        class="bg-background text-foreground"
                                        >{menit}</option
                                    >
                                {/each}
                            </select>
                        </div>
                        <InputError
                            message={form.errors.jam_selesai ||
                                form.errors.menit_selesai}
                            class="mt-1"
                        />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="toleransi_terlambat"
                        >Toleransi Terlambat (menit)</Label
                    >
                    <select
                        id="toleransi_terlambat"
                        name="toleransi_terlambat"
                        bind:value={form.toleransi_terlambat}
                        class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                    >
                        {#each toleransiOptions as t (t)}
                            <option
                                value={String(t)}
                                class="bg-background text-foreground"
                                >{t} menit</option
                            >
                        {/each}
                    </select>
                    <InputError
                        message={form.errors.toleransi_terlambat}
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
                        Kembali
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
