<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Pengaturan', href: '/admin/pengaturan/lokasi-absen' },
            { title: 'Lokasi Absen', href: '/admin/pengaturan/lokasi-absen' },
        ],
    };
</script>

<script lang="ts">
    import { router, useForm } from '@inertiajs/svelte';
    import { MapPin, Upload, Trash2 } from '@lucide/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
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
    import InputError from '@/components/InputError.svelte';
    import { Spinner } from '@/components/ui/spinner';

    let {
        geofenceData = null,
        toleransiMeter = 0,
    }: {
        geofenceData?: {
            coordinates?: Array<{ lat: number; lng: number }>;
        } | null;
        toleransiMeter?: number;
    } = $props();

    let uploadForm = useForm({
        kml_file: null as File | null,
    });

    let toleransiForm = useForm({
        toleransi_meter: String(toleransiMeter),
    });

    function handleFileChange(e: Event) {
        const input = e.target as HTMLInputElement;
        if (input.files && input.files[0]) {
            uploadForm.kml_file = input.files[0];
        }
    }

    function handleUploadSubmit(e: SubmitEvent) {
        e.preventDefault();
        uploadForm.post('/admin/pengaturan/lokasi-absen', {
            forceFormData: true,
        });
    }

    function handleToleransiSubmit(e: SubmitEvent) {
        e.preventDefault();
        toleransiForm.put('/admin/pengaturan/lokasi-absen/toleransi');
    }
</script>

<AppHead title="Pengaturan Lokasi Absen" />

<div class="w-full space-y-6">
    <Heading
        title="Lokasi Absen"
        description="Atur area geofence untuk presensi siswa"
    />

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="space-y-6">
            <Card>
                <CardHeader>
                    <CardTitle>Unggah File KML</CardTitle>
                    <CardDescription
                        >Upload file KML (maks 5 MB) berisi polygon area absen</CardDescription
                    >
                </CardHeader>
                <CardContent>
                    <form onsubmit={handleUploadSubmit} class="space-y-4">
                        <div class="grid gap-2">
                            <Label for="kml_file">File KML</Label>
                            <Input
                                id="kml_file"
                                name="kml_file"
                                type="file"
                                accept=".kml,.xml"
                                onchange={handleFileChange}
                                required
                            />
                            <InputError
                                message={uploadForm.errors.kml_file}
                                class="mt-1"
                            />
                        </div>
                        <div class="flex justify-end">
                            <Button
                                type="submit"
                                disabled={uploadForm.processing ||
                                    !uploadForm.kml_file}
                            >
                                {#if uploadForm.processing}
                                    <Spinner class="mr-2 size-4 animate-spin" />
                                {/if}
                                <Upload class="size-4" />
                                Unggah
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Toleransi Jarak</CardTitle>
                    <CardDescription
                        >Jarak toleransi dari area polygon (0-500 meter)</CardDescription
                    >
                </CardHeader>
                <CardContent>
                    <form onsubmit={handleToleransiSubmit} class="space-y-4">
                        <div class="grid gap-2">
                            <Label for="toleransi_meter"
                                >Toleransi (meter)</Label
                            >
                            <Input
                                id="toleransi_meter"
                                name="toleransi_meter"
                                type="number"
                                min="0"
                                max="500"
                                bind:value={toleransiForm.toleransi_meter}
                                required
                            />
                            <InputError
                                message={toleransiForm.errors.toleransi_meter}
                                class="mt-1"
                            />
                        </div>
                        <div class="flex justify-end">
                            <Button
                                type="submit"
                                disabled={toleransiForm.processing}
                            >
                                {#if toleransiForm.processing}
                                    <Spinner class="mr-2 size-4 animate-spin" />
                                {/if}
                                Simpan Toleransi
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>

            {#if geofenceData?.coordinates}
                <Card>
                    <CardHeader>
                        <CardTitle>Area Geofence Aktif</CardTitle>
                        <CardDescription
                            >{geofenceData.coordinates.length} titik koordinat terdaftar</CardDescription
                        >
                    </CardHeader>
                    <CardContent>
                        <Button
                            variant="destructive"
                            onclick={() => {
                                if (
                                    confirm(
                                        'Yakin ingin menghapus area geofence?',
                                    )
                                ) {
                                    router.delete(
                                        '/admin/pengaturan/lokasi-absen',
                                    );
                                }
                            }}
                        >
                            <Trash2 class="size-4" />
                            Hapus Area Geofence
                        </Button>
                    </CardContent>
                </Card>
            {/if}
        </div>

        <Card>
            <CardHeader>
                <CardTitle>Peta Lokasi</CardTitle>
                <CardDescription>Visualisasi area polygon</CardDescription>
            </CardHeader>
            <CardContent>
                {#if geofenceData?.coordinates && geofenceData.coordinates.length > 0}
                    <div class="space-y-2">
                        <div
                            class="flex items-center gap-2 text-sm text-muted-foreground"
                        >
                            <MapPin class="size-4" />
                            <span
                                >{geofenceData.coordinates.length} titik koordinat</span
                            >
                        </div>
                        <div
                            class="max-h-80 overflow-y-auto rounded-md border border-border bg-muted/30 p-3 text-xs"
                        >
                            {#each geofenceData.coordinates as coord, i (i)}
                                <div
                                    class="flex justify-between py-1 text-muted-foreground"
                                >
                                    <span>#{i + 1}</span>
                                    <span class="font-mono"
                                        >{coord.lat.toFixed(6)}, {coord.lng.toFixed(
                                            6,
                                        )}</span
                                    >
                                </div>
                            {/each}
                        </div>
                    </div>
                {:else}
                    <div
                        class="flex flex-col items-center justify-center rounded-md border border-dashed border-border bg-muted/30 p-8 text-center"
                    >
                        <MapPin class="mb-2 size-8 text-muted-foreground" />
                        <p class="text-sm text-muted-foreground">
                            Belum ada area geofence. Upload file KML untuk
                            memulai.
                        </p>
                    </div>
                {/if}
            </CardContent>
        </Card>
    </div>
</div>
