<script module lang="ts">
    export const layout = {
        title: 'Masuk ke akun Anda',
        description: 'Masukkan email dan kata sandi untuk masuk',
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
        status = '',
        canResetPassword,
    }: {
        status?: string;
        canResetPassword: boolean;
    } = $props();

    let form = useForm({
        email: '',
        password: '',
        remember: false,
    });

    function handleSubmit(e: SubmitEvent) {
        e.preventDefault();
        form.post('/login', {
            onFinish: () => form.reset('password'),
        });
    }
</script>

<AppHead title="Masuk" />

{#if status}
    <div
        class="mb-4 rounded-md border border-green-200 bg-green-50 p-3 text-center text-sm font-medium text-green-700"
    >
        {status}
    </div>
{/if}

<Card class="border-border shadow-sm">
    <CardHeader class="space-y-1 text-center">
        <CardTitle class="text-2xl font-bold tracking-tight">Masuk</CardTitle>
        <CardDescription
            >Masukkan email dan kata sandi untuk masuk ke SentriSiswa</CardDescription
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
                <div class="flex items-center justify-between">
                    <Label for="password">Kata Sandi</Label>
                    {#if canResetPassword}
                        <TextLink href="/forgot-password" class="text-sm"
                            >Lupa kata sandi?</TextLink
                        >
                    {/if}
                </div>
                <Input
                    id="password"
                    type="password"
                    bind:value={form.password}
                    required
                    autocomplete="current-password"
                    placeholder="Kata sandi"
                />
                <InputError message={form.errors.password} class="mt-1" />
            </div>

            <Button type="submit" class="w-full" disabled={form.processing}>
                {#if form.processing}
                    <Spinner class="mr-2 size-4" />
                {/if}
                Masuk
            </Button>

            <p class="text-center text-sm text-muted-foreground">
                Belum punya akun?
                <TextLink href="/daftar">Daftar</TextLink>
            </p>
        </form>
    </CardContent>
</Card>
