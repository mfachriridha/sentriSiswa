<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Guru', href: '/admin/guru' },
            { title: 'Detail', href: '' },
        ],
    };
</script>

<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { ArrowLeft, Mail, Shield, User } from '@lucide/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent } from '@/components/ui/card';

    let {
        guru,
    }: {
        guru: {
            id: number;
            nama: string;
            email: string | null;
            peran: string;
            status: string;
            created_at: string;
        };
    } = $props();

    const labelPeran: Record<string, string> = {
        wali_kelas: 'Wali Kelas',
        bk: 'BK',
        kesiswaan: 'Kesiswaan',
    };

    const labelStatus: Record<string, string> = {
        terdaftar: 'Terdaftar',
        belum_terdaftar: 'Belum Terdaftar',
    };
</script>

<AppHead title="Detail Guru" />

<div class="max-w-3xl space-y-6">
    <Heading title="Detail Guru">
        {#snippet actions()}
            <Button variant="outline" size="sm" asChild>
                {#snippet children(props)}
                    <Link href="/admin/guru" class={props.class}>
                        <ArrowLeft class="size-4" />
                        Kembali
                    </Link>
                {/snippet}
            </Button>
        {/snippet}
    </Heading>

    <Card>
        <CardContent class="space-y-6 pt-6">
            <div class="flex items-center gap-4">
                <div
                    class="flex size-16 items-center justify-center rounded-full bg-primary/10"
                >
                    <User class="size-8 text-primary" />
                </div>
                <div>
                    <h3 class="text-xl font-semibold">{guru.nama}</h3>
                    <p class="text-sm text-muted-foreground">
                        {labelPeran[guru.peran] ?? guru.peran}
                    </p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div class="space-y-1">
                    <p
                        class="flex items-center gap-2 text-sm font-medium text-muted-foreground"
                    >
                        <Mail class="size-4" />
                        Email
                    </p>
                    <p class="text-sm">{guru.email ?? '-'}</p>
                </div>

                <div class="space-y-1">
                    <p
                        class="flex items-center gap-2 text-sm font-medium text-muted-foreground"
                    >
                        <Shield class="size-4" />
                        Status
                    </p>
                    <p class="text-sm">
                        {labelStatus[guru.status] ?? guru.status}
                    </p>
                </div>

                <div class="space-y-1">
                    <p class="text-sm font-medium text-muted-foreground">
                        Tanggal Dibuat
                    </p>
                    <p class="text-sm">
                        {new Date(guru.created_at).toLocaleDateString('id-ID', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                        })}
                    </p>
                </div>
            </div>

            <div class="flex gap-3 border-t pt-4">
                <Button asChild>
                    {#snippet children(props)}
                        <Link
                            href="/admin/guru/{guru.id}/edit"
                            class={props.class}
                        >
                            Edit Guru
                        </Link>
                    {/snippet}
                </Button>
            </div>
        </CardContent>
    </Card>
</div>
