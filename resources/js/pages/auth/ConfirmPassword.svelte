<script module lang="ts">
    export const layout = {
        title: 'Konfirmasi Kata Sandi',
        description:
            'Ini adalah area aman. Silakan konfirmasi kata sandi Anda.',
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
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';

    function handleSubmit(e: SubmitEvent) {
        e.preventDefault();
        const form = e.target as HTMLFormElement;
        router.post('/user/confirm-password', new FormData(form));
    }
</script>

<AppHead title="Konfirmasi Kata Sandi" />

<Card>
    <CardHeader class="space-y-1 text-center">
        <CardTitle class="text-2xl">Konfirmasi Kata Sandi</CardTitle>
        <CardDescription
            >Konfirmasi kata sandi untuk melanjutkan</CardDescription
        >
    </CardHeader>
    <CardContent>
        <form
            method="post"
            action="/user/confirm-password"
            onsubmit={handleSubmit}
            class="space-y-4"
        >
            <div class="space-y-2">
                <Label for="password">Kata Sandi</Label>
                <Input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                />
            </div>

            <Button type="submit" class="w-full">Konfirmasi</Button>
        </form>
    </CardContent>
</Card>
