<script module lang="ts">
    export const layout = {
        title: 'Ganti Email',
        description: 'Masukkan email baru untuk akun Anda',
    };
</script>

<script lang="ts">
    import { useForm } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import { Button } from '@/components/ui/button';
    import {
        Card,
        CardContent,
        CardDescription,
        CardHeader,
        CardTitle,
    } from '@/components/ui/card';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { Spinner } from '@/components/ui/spinner';
    import InputError from '@/components/InputError.svelte';

    let form = useForm({
        email: '',
    });

    function handleSubmit(e: SubmitEvent) {
        e.preventDefault();
        form.put('/otp/ganti-email');
    }
</script>

<AppHead title="Ganti Email" />

<Card class="border-border shadow-sm">
    <CardHeader class="space-y-1 text-center">
        <CardTitle class="text-2xl font-bold tracking-tight">Ganti Email</CardTitle>
        <CardDescription>Masukkan email baru untuk akun Anda</CardDescription>
    </CardHeader>
    <CardContent>
        <form onsubmit={handleSubmit} class="space-y-4">
            <div class="space-y-2">
                <Label for="email">Email Baru</Label>
                <Input
                    id="email"
                    type="email"
                    bind:value={form.email}
                    required
                    autocomplete="email"
                    placeholder="email@contoh.com"
                />
                <InputError message={form.errors.email} class="mt-1" />
            </div>

            <Button type="submit" class="w-full" disabled={form.processing}>
                {#if form.processing}
                    <Spinner class="mr-2 size-4" />
                {/if}
                Simpan Email Baru
            </Button>
        </form>
    </CardContent>
</Card>
