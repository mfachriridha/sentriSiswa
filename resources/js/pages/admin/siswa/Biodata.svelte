<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Siswa', href: '/admin/siswa' },
            { title: 'Biodata', href: '/admin/siswa' },
        ],
    };
</script>

<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent } from '@/components/ui/card';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';

    let {
        siswa,
    }: {
        siswa: {
            id: number;
            pengguna: { nama: string };
            biodata: Record<string, string | null> | null;
        };
    } = $props();

    const b = $derived(siswa.biodata ?? {});
</script>

<AppHead title="Edit Biodata" />

<div class="max-w-3xl space-y-6">
    <Heading title="Edit Biodata" description={siswa.pengguna?.nama} />

    <Card>
        <CardContent class="pt-6">
            <form
                method="post"
                action={`/admin/siswa/${siswa.id}/biodata`}
                onsubmit={(e) => {
                    e.preventDefault();
                    const form = e.target as HTMLFormElement;
                    const data = new FormData(form);
                    data.append('_method', 'PUT');
                    router.post(`/admin/siswa/${siswa.id}/biodata`, data);
                }}
                class="space-y-6"
            >
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="tempat_lahir">Tempat Lahir</Label>
                        <Input
                            id="tempat_lahir"
                            name="tempat_lahir"
                            value={b.tempat_lahir ?? ''}
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label for="tanggal_lahir">Tanggal Lahir</Label>
                        <Input
                            id="tanggal_lahir"
                            name="tanggal_lahir"
                            type="date"
                            value={b.tanggal_lahir ?? ''}
                        />
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="jenis_kelamin">Jenis Kelamin</Label>
                        <select
                            id="jenis_kelamin"
                            name="jenis_kelamin"
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm"
                        >
                            <option value="">Pilih...</option>
                            <option value="L" selected={b.jenis_kelamin === 'L'}
                                >Laki-laki</option
                            >
                            <option value="P" selected={b.jenis_kelamin === 'P'}
                                >Perempuan</option
                            >
                        </select>
                    </div>
                    <div class="grid gap-2">
                        <Label for="agama">Agama</Label>
                        <Input id="agama" name="agama" value={b.agama ?? ''} />
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="anak_ke">Anak Ke-</Label>
                        <Input
                            id="anak_ke"
                            name="anak_ke"
                            value={b.anak_ke ?? ''}
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label for="sekolah_asal">Sekolah Asal</Label>
                        <Input
                            id="sekolah_asal"
                            name="sekolah_asal"
                            value={b.sekolah_asal ?? ''}
                        />
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="nama_ayah">Nama Ayah</Label>
                        <Input
                            id="nama_ayah"
                            name="nama_ayah"
                            value={b.nama_ayah ?? ''}
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label for="pekerjaan_ayah">Pekerjaan Ayah</Label>
                        <Input
                            id="pekerjaan_ayah"
                            name="pekerjaan_ayah"
                            value={b.pekerjaan_ayah ?? ''}
                        />
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="nama_ibu">Nama Ibu</Label>
                        <Input
                            id="nama_ibu"
                            name="nama_ibu"
                            value={b.nama_ibu ?? ''}
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label for="pekerjaan_ibu">Pekerjaan Ibu</Label>
                        <Input
                            id="pekerjaan_ibu"
                            name="pekerjaan_ibu"
                            value={b.pekerjaan_ibu ?? ''}
                        />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="telepon_ortu">Telepon Orang Tua</Label>
                    <Input
                        id="telepon_ortu"
                        name="telepon_ortu"
                        value={b.telepon_ortu ?? ''}
                    />
                </div>

                <div class="grid gap-2">
                    <Label for="alamat_ortu">Alamat Orang Tua</Label>
                    <Input
                        id="alamat_ortu"
                        name="alamat_ortu"
                        value={b.alamat_ortu ?? ''}
                    />
                </div>

                <div
                    class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"
                >
                    <Button
                        type="button"
                        variant="outline"
                        onclick={() => router.back()}>Batal</Button
                    >
                    <Button type="submit">Simpan Biodata</Button>
                </div>
            </form>
        </CardContent>
    </Card>
</div>
