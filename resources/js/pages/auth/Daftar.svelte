<script module lang="ts">
    export const layout = {
        title: 'Daftar Akun',
        description: 'Verifikasi identitas Anda (NISN/NIP) untuk mendaftar',
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

    let form = useForm({
        peran: 'siswa',
        identitas: '',
    });

    function handleSubmit(e: SubmitEvent) {
        e.preventDefault();
        form.post('/daftar/verifikasi');
    }
</script>

<AppHead title="Daftar" />

<Card class="border-border shadow-sm">
    <CardHeader class="space-y-1 text-center">
        <CardTitle class="text-2xl font-bold tracking-tight"
            >Daftar Akun</CardTitle
        >
        <CardDescription
            >Verifikasi identitas Anda (NISN/NIP/NIS) untuk mendaftar</CardDescription
        >
    </CardHeader>
    <CardContent>
        <form onsubmit={handleSubmit} class="space-y-5">
            <div class="space-y-2">
                <Label>Saya adalah</Label>
                <div class="flex gap-6 pt-1">
                    <label
                        class="flex cursor-pointer items-center gap-2 text-sm text-foreground"
                    >
                        <input
                            type="radio"
                            name="peran"
                            value="siswa"
                            bind:group={form.peran}
                            required
                            class="size-4 text-primary focus:ring-primary"
                        />
                        <span>Siswa</span>
                    </label>
                    <label
                        class="flex cursor-pointer items-center gap-2 text-sm text-foreground"
                    >
                        <input
                            type="radio"
                            name="peran"
                            value="guru"
                            bind:group={form.peran}
                            required
                            class="size-4 text-primary focus:ring-primary"
                        />
                        <span>Guru (Staf Sekolah)</span>
                    </label>
                </div>
                <InputError message={form.errors.peran} class="mt-1" />
            </div>

            <div class="space-y-2">
                <Label for="identitas">NISN / NIS / NIP</Label>
                <Input
                    id="identitas"
                    type="text"
                    bind:value={form.identitas}
                    required
                    placeholder="Masukkan NISN, NIS, atau NIP"
                />
                <InputError message={form.errors.identitas} class="mt-1" />
            </div>

            <Button type="submit" class="w-full" disabled={form.processing}>
                {#if form.processing}
                    <Spinner class="mr-2 size-4" />
                {/if}
                Verifikasi
            </Button>

            <p class="text-center text-sm text-muted-foreground">
                Sudah punya akun?
                <TextLink href="/login">Masuk</TextLink>
            </p>
        </form>
    </CardContent>
</Card>
