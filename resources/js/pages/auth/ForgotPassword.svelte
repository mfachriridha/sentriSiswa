<script module lang="ts">
    export const layout = {
        title: 'Lupa Kata Sandi',
        description: 'Masukkan email untuk menerima tautan reset kata sandi',
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
    }: {
        status?: string;
    } = $props();

    let form = useForm({
        email: '',
    });

    function handleSubmit(e: SubmitEvent) {
        e.preventDefault();
        form.post('/forgot-password');
    }
</script>

<AppHead title="Lupa Kata Sandi" />

{#if status}
    <div
        class="mb-4 rounded-md border border-green-200 bg-green-50 p-3 text-center text-sm font-medium text-green-700"
    >
        {status}
    </div>
{/if}

<Card class="border-border shadow-sm">
    <CardHeader class="space-y-1 text-center">
        <CardTitle class="text-2xl font-bold tracking-tight"
            >Lupa Kata Sandi</CardTitle
        >
        <CardDescription
            >Masukkan email Anda untuk menerima tautan reset kata sandi</CardDescription
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
                    autocomplete="off"
                    placeholder="email@contoh.com"
                />
                <InputError message={form.errors.email} class="mt-1" />
            </div>

            <Button type="submit" class="w-full" disabled={form.processing}>
                {#if form.processing}
                    <Spinner class="mr-2 size-4" />
                {/if}
                Kirim Tautan Reset
            </Button>

            <p class="text-center text-sm text-muted-foreground">
                Atau, kembali ke
                <TextLink href="/login">Masuk</TextLink>
            </p>
        </form>
    </CardContent>
</Card>
