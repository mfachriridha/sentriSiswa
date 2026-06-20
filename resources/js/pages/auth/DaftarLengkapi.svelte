<script module lang="ts">
    export const layout = {
        title: 'Lengkapi Pendaftaran',
        description: 'Buat email dan kata sandi untuk akun Anda',
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
    import TextLink from '@/components/TextLink.svelte';
    import InputError from '@/components/InputError.svelte';

    let {
        nama = '',
        peran = '',
        identitas = '',
    }: {
        nama?: string;
        peran?: string;
        identitas?: string;
    } = $props();

    let form = useForm({
        email: '',
        password: '',
        password_confirmation: '',
    });

    function handleSubmit(e: SubmitEvent) {
        e.preventDefault();
        form.post('/daftar');
    }
</script>

<AppHead title="Lengkapi Pendaftaran" />

{#if nama}
    <div
        class="mb-4 rounded-xl border border-indigo-100 bg-indigo-50/50 p-4 text-center text-sm"
    >
        <p
            class="text-xs font-medium uppercase tracking-wider text-indigo-600/80"
        >
            Identitas Terverifikasi
        </p>
        <p class="mt-1 text-lg font-bold text-foreground">{nama}</p>
        <p class="mt-1 text-xs text-muted-foreground">
            {peran === 'guru' ? 'Guru' : 'Siswa'} — {identitas}
        </p>
    </div>
{/if}

<Card class="border-border shadow-sm">
    <CardHeader class="space-y-1 text-center">
        <CardTitle class="text-2xl font-bold tracking-tight"
            >Lengkapi Pendaftaran</CardTitle
        >
        <CardDescription
            >Buat email dan kata sandi untuk akun Anda</CardDescription
        >
    </CardHeader>
    <CardContent>
        <form onsubmit={handleSubmit} class="space-y-4">
            <div class="space-y-2">
                <Label for="email">Email</Label>
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

            <div class="space-y-2">
                <Label for="password">Kata Sandi</Label>
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
                Daftar
            </Button>

            <p class="text-center text-sm text-muted-foreground">
                Sudah punya akun?
                <TextLink href="/login">Masuk</TextLink>
            </p>
        </form>
    </CardContent>
</Card>
