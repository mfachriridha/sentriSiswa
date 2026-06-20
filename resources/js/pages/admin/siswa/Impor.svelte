<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Siswa', href: '/admin/siswa' },
            { title: 'Impor', href: '/admin/siswa/impor' },
        ],
    };
</script>

<script lang="ts">
    import { router, Link } from '@inertiajs/svelte';
    import { Download, Upload, Users } from '@lucide/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent } from '@/components/ui/card';
    import { Label } from '@/components/ui/label';
    import InputError from '@/components/InputError.svelte';

    let {
        pratinjau = null,
        totalBaris = 0,
        pathFile = '',
        errors = {},
    }: {
        pratinjau?: {
            data: Array<Record<string, string>>;
            current_page: number;
            last_page: number;
        } | null;
        totalBaris?: number;
        pathFile?: string;
        errors?: Record<string, string>;
    } = $props();

    let fileInput: HTMLInputElement;
    let selectedFile: File | null = $state(null);

    function handleFileChange(e: Event) {
        const input = e.target as HTMLInputElement;
        selectedFile = input.files?.[0] ?? null;
    }

    function handleUpload() {
        if (!selectedFile) return;

        const formData = new FormData();
        formData.append('file', selectedFile);
        router.post('/admin/siswa/impor/unggah', formData);
    }

    function handleImport() {
        if (!pathFile) return;

        const formData = new FormData();
        formData.append('file_path', pathFile);
        router.post('/admin/siswa/impor', formData);
    }

    function downloadTemplate() {
        window.location.href = '/admin/siswa/impor/template';
    }
</script>

<AppHead title="Impor Siswa" />

<div class="w-full space-y-6">
    <Heading
        title="Impor Data Siswa"
        description="Import data siswa dari file Excel"
    >
        {#snippet actions()}
            <Button variant="outline" size="sm" onclick={downloadTemplate}>
                <Download class="size-4" />
                Unduh Template
            </Button>
        {/snippet}
    </Heading>

    {#if !pratinjau}
        <Card>
            <CardContent class="pt-6">
                <div class="space-y-4">
                    <div class="space-y-2">
                        <Label for="file">Pilih File Excel</Label>
                        <input
                            bind:this={fileInput}
                            id="file"
                            type="file"
                            accept=".xlsx,.xls"
                            onchange={handleFileChange}
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm"
                        />
                        <InputError message={errors.file} />
                    </div>

                    <div class="flex gap-3">
                        <Button onclick={handleUpload} disabled={!selectedFile}>
                            <Upload class="size-4" />
                            Unggah File
                        </Button>
                        <Button variant="outline" onclick={downloadTemplate}>
                            <Download class="size-4" />
                            Unduh Template
                        </Button>
                    </div>
                </div>
            </CardContent>
        </Card>
    {:else}
        <Card>
            <CardContent class="pt-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-muted-foreground">
                            Total {totalBaris} baris data ditemukan
                        </p>
                        <Button onclick={handleImport}>
                            <Users class="size-4" />
                            Mulai Import
                        </Button>
                    </div>

                    <div class="overflow-x-auto rounded-lg border">
                        <table class="w-full text-sm">
                            <thead class="bg-muted/50">
                                <tr>
                                    {#if pratinjau.data.length > 0}
                                        {#each Object.keys(pratinjau.data[0]) as key}
                                            <th
                                                class="px-4 py-2 text-left font-medium"
                                                >{key}</th
                                            >
                                        {/each}
                                    {/if}
                                </tr>
                            </thead>
                            <tbody>
                                {#each pratinjau.data as row}
                                    <tr class="border-t">
                                        {#each Object.values(row) as value}
                                            <td class="px-4 py-2"
                                                >{value ?? '-'}</td
                                            >
                                        {/each}
                                    </tr>
                                {/each}
                            </tbody>
                        </table>
                    </div>

                    {#if pratinjau.last_page > 1}
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-muted-foreground">
                                Halaman {pratinjau.current_page} dari {pratinjau.last_page}
                            </p>
                            <div class="flex gap-2">
                                {#if pratinjau.current_page > 1}
                                    <Button variant="outline" size="sm" asChild>
                                        {#snippet children(props)}
                                            <Link
                                                href="?page={pratinjau.current_page -
                                                    1}"
                                                class={props.class}
                                                >Sebelumnya</Link
                                            >
                                        {/snippet}
                                    </Button>
                                {/if}
                                {#if pratinjau.current_page < pratinjau.last_page}
                                    <Button variant="outline" size="sm" asChild>
                                        {#snippet children(props)}
                                            <Link
                                                href="?page={pratinjau.current_page +
                                                    1}"
                                                class={props.class}
                                                >Selanjutnya</Link
                                            >
                                        {/snippet}
                                    </Button>
                                {/if}
                            </div>
                        </div>
                    {/if}
                </div>
            </CardContent>
        </Card>
    {/if}
</div>
