<script module lang="ts">
    export const layout = {
        title: 'Ganti Kata Sandi',
        description: 'Masukkan kata sandi baru untuk akun Anda',
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
        password: '',
        password_confirmation: '',
    });

    function handleSubmit(e: SubmitEvent) {
        e.preventDefault();
        form.put('/otp/ganti-password');
    }
</script>

<AppHead title="Ganti Kata Sandi" />

<Card class="border-border shadow-sm">
    <CardHeader class="space-y-1 text-center">
        <CardTitle class="text-2xl font-bold tracking-tight"
            >Ganti Kata Sandi</CardTitle
        >
        <CardDescription
            >Masukkan kata sandi baru untuk akun Anda</CardDescription
        >
    </CardHeader>
    <CardContent>
        <form onsubmit={handleSubmit} class="space-y-4">
            <div class="space-y-2">
                <Label for="password">Kata Sandi Baru</Label>
                <Input
                    id="password"
                    type="password"
                    bind:value={form.password}
                    required
                    autocomplete="new-password"
                    placeholder="Minimal 8 karakter"
                />
                <InputError message={form.errors.password} class="mt-1" />
            </div>

            <div class="space-y-2">
                <Label for="password_confirmation">Konfirmasi Kata Sandi</Label>
                <Input
                    id="password_confirmation"
                    type="password"
                    bind:value={form.password_confirmation}
                    required
                    autocomplete="new-password"
                    placeholder="Ulangi kata sandi"
                />
                <InputError
                    message={form.errors.password_confirmation}
                    class="mt-1"
                />
            </div>

            <Button type="submit" class="w-full" disabled={form.processing}>
                {#if form.processing}
                    <Spinner class="mr-2 size-4" />
                {/if}
                Simpan Kata Sandi Baru
            </Button>
        </form>
    </CardContent>
</Card>
