<script module lang="ts">
    export const layout = {
        title: 'Verifikasi Email',
        description:
            'Silakan verifikasi email Anda dengan menekan tautan yang baru kami kirim.',
    };
</script>

<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import { Button } from '@/components/ui/button';
    import {
        Card,
        CardContent,
        CardDescription,
        CardHeader,
        CardTitle,
    } from '@/components/ui/card';

    let {
        status = '',
    }: {
        status?: string;
    } = $props();

    const handleResend = (e: SubmitEvent) => {
        e.preventDefault();
        router.post('/email/verification-notification');
    };

    const handleLogout = () => {
        router.post('/logout');
    };
</script>

<AppHead title="Verifikasi Email" />

{#if status === 'verification-link-sent'}
    <div
        class="mb-4 rounded-md border border-green-200 bg-green-50 p-3 text-center text-sm font-medium text-green-700"
    >
        Tautan verifikasi baru telah dikirim ke email Anda.
    </div>
{/if}

<Card>
    <CardHeader class="space-y-1 text-center">
        <CardTitle class="text-2xl">Verifikasi Email</CardTitle>
        <CardDescription
            >Periksa inbox email Anda untuk tautan verifikasi</CardDescription
        >
    </CardHeader>
    <CardContent class="space-y-4">
        <form method="post" onsubmit={handleResend}>
            <Button type="submit" variant="secondary" class="w-full"
                >Kirim Ulang Email Verifikasi</Button
            >
        </form>

        <button
            type="button"
            onclick={handleLogout}
            class="block w-full text-center text-sm text-primary underline underline-offset-4"
        >
            Keluar
        </button>
    </CardContent>
</Card>
