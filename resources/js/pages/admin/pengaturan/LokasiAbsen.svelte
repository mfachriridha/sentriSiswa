<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Pengaturan', href: '/admin/pengaturan/lokasi-absen' },
            { title: 'Lokasi Absen', href: '/admin/pengaturan/lokasi-absen' },
        ],
    };
</script>

<script lang="ts">
    import { router } from '@inertiajs/svelte';
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

    let {
        geofenceData = null,
        toleransiMeter = 0,
    }: {
        geofenceData?: {
            coordinates?: Array<{ lat: number; lng: number }>;
        } | null;
        toleransiMeter?: number;
    } = $props();
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
                    <form
                        method="post"
                        action="/admin/pengaturan/lokasi-absen"
                        enctype="multipart/form-data"
                        onsubmit={(e) => {
                            e.preventDefault();
                            const form = e.target as HTMLFormElement;
                            router.post(
                                '/admin/pengaturan/lokasi-absen',
                                new FormData(form),
                                {
                                    forceFormData: true,
                                },
                            );
                        }}
                        class="space-y-4"
                    >
                        <div class="grid gap-2">
                            <Label for="kml_file">File KML</Label>
                            <Input
                                id="kml_file"
                                name="kml_file"
                                type="file"
                                accept=".kml,.xml"
                                required
                            />
                        </div>
                        <div class="flex justify-end">
                            <Button type="submit">
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
                    <form
                        method="post"
                        action="/admin/pengaturan/lokasi-absen/toleransi"
                        onsubmit={(e) => {
                            e.preventDefault();
                            const form = e.target as HTMLFormElement;
                            const data = new FormData(form);
                            data.append('_method', 'PUT');
                            router.post(
                                '/admin/pengaturan/lokasi-absen/toleransi',
                                data,
                            );
                        }}
                        class="space-y-4"
                    >
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
                                value={toleransiMeter}
                                required
                            />
                        </div>
                        <div class="flex justify-end">
                            <Button type="submit">Simpan Toleransi</Button>
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
