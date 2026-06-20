<script module lang="ts">
    export const layout = {
        breadcrumbs: [
            { title: 'Siswa', href: '/admin/siswa' },
            { title: 'Biodata', href: '/admin/siswa' },
        ],
    };
</script>

<script lang="ts">
    import { useForm, router } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent } from '@/components/ui/card';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import InputError from '@/components/InputError.svelte';
    import { Spinner } from '@/components/ui/spinner';

    let {
        siswa,
    }: {
        siswa: {
            id: number;
            pengguna: { nama: string };
            biodata: Record<string, string | null> | null;
        };
    } = $props();

    let form = useForm({
        tempat_lahir: siswa.biodata?.tempat_lahir ?? '',
        tanggal_lahir: siswa.biodata?.tanggal_lahir ?? '',
        jenis_kelamin: siswa.biodata?.jenis_kelamin ?? '',
        agama: siswa.biodata?.agama ?? '',
        anak_ke: siswa.biodata?.anak_ke ?? '',
        sekolah_asal: siswa.biodata?.sekolah_asal ?? '',
        nama_ayah: siswa.biodata?.nama_ayah ?? '',
        pekerjaan_ayah: siswa.biodata?.pekerjaan_ayah ?? '',
        nama_ibu: siswa.biodata?.nama_ibu ?? '',
        pekerjaan_ibu: siswa.biodata?.pekerjaan_ibu ?? '',
        telepon_ortu: siswa.biodata?.telepon_ortu ?? '',
        alamat_ortu: siswa.biodata?.alamat_ortu ?? '',
    });

    function handleSubmit(e: SubmitEvent) {
        e.preventDefault();
        form.put(`/admin/siswa/${siswa.id}/biodata`);
    }
</script>

<AppHead title="Edit Biodata" />

<div class="max-w-3xl space-y-6">
    <Heading title="Edit Biodata" description={siswa.pengguna?.nama} />

    <Card>
        <CardContent class="pt-6">
            <form onsubmit={handleSubmit} class="space-y-6">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="tempat_lahir">Tempat Lahir</Label>
                        <Input
                            id="tempat_lahir"
                            name="tempat_lahir"
                            bind:value={form.tempat_lahir}
                        />
                        <InputError
                            message={form.errors.tempat_lahir}
                            class="mt-1"
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label for="tanggal_lahir">Tanggal Lahir</Label>
                        <Input
                            id="tanggal_lahir"
                            name="tanggal_lahir"
                            type="date"
                            bind:value={form.tanggal_lahir}
                        />
                        <InputError
                            message={form.errors.tanggal_lahir}
                            class="mt-1"
                        />
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="jenis_kelamin">Jenis Kelamin</Label>
                        <select
                            id="jenis_kelamin"
                            name="jenis_kelamin"
                            bind:value={form.jenis_kelamin}
                            class="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-ring"
                        >
                            <option
                                value=""
                                class="bg-background text-foreground"
                                >Pilih...</option
                            >
                            <option
                                value="L"
                                class="bg-background text-foreground"
                                >Laki-laki</option
                            >
                            <option
                                value="P"
                                class="bg-background text-foreground"
                                >Perempuan</option
                            >
                        </select>
                        <InputError
                            message={form.errors.jenis_kelamin}
                            class="mt-1"
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label for="agama">Agama</Label>
                        <Input
                            id="agama"
                            name="agama"
                            bind:value={form.agama}
                        />
                        <InputError message={form.errors.agama} class="mt-1" />
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="anak_ke">Anak Ke-</Label>
                        <Input
                            id="anak_ke"
                            name="anak_ke"
                            bind:value={form.anak_ke}
                        />
                        <InputError
                            message={form.errors.anak_ke}
                            class="mt-1"
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label for="sekolah_asal">Sekolah Asal</Label>
                        <Input
                            id="sekolah_asal"
                            name="sekolah_asal"
                            bind:value={form.sekolah_asal}
                        />
                        <InputError
                            message={form.errors.sekolah_asal}
                            class="mt-1"
                        />
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="nama_ayah">Nama Ayah</Label>
                        <Input
                            id="nama_ayah"
                            name="nama_ayah"
                            bind:value={form.nama_ayah}
                        />
                        <InputError
                            message={form.errors.nama_ayah}
                            class="mt-1"
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label for="pekerjaan_ayah">Pekerjaan Ayah</Label>
                        <Input
                            id="pekerjaan_ayah"
                            name="pekerjaan_ayah"
                            bind:value={form.pekerjaan_ayah}
                        />
                        <InputError
                            message={form.errors.pekerjaan_ayah}
                            class="mt-1"
                        />
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="nama_ibu">Nama Ibu</Label>
                        <Input
                            id="nama_ibu"
                            name="nama_ibu"
                            bind:value={form.nama_ibu}
                        />
                        <InputError
                            message={form.errors.nama_ibu}
                            class="mt-1"
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label for="pekerjaan_ibu">Pekerjaan Ibu</Label>
                        <Input
                            id="pekerjaan_ibu"
                            name="pekerjaan_ibu"
                            bind:value={form.pekerjaan_ibu}
                        />
                        <InputError
                            message={form.errors.pekerjaan_ibu}
                            class="mt-1"
                        />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="telepon_ortu">Telepon Orang Tua</Label>
                    <Input
                        id="telepon_ortu"
                        name="telepon_ortu"
                        bind:value={form.telepon_ortu}
                    />
                    <InputError
                        message={form.errors.telepon_ortu}
                        class="mt-1"
                    />
                </div>

                <div class="grid gap-2">
                    <Label for="alamat_ortu">Alamat Orang Tua</Label>
                    <Input
                        id="alamat_ortu"
                        name="alamat_ortu"
                        bind:value={form.alamat_ortu}
                    />
                    <InputError
                        message={form.errors.alamat_ortu}
                        class="mt-1"
                    />
                </div>

                <div
                    class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"
                >
                    <Button
                        type="button"
                        variant="outline"
                        onclick={() => router.back()}
                        disabled={form.processing}
                    >
                        Batal
                    </Button>
                    <Button type="submit" disabled={form.processing}>
                        {#if form.processing}
                            <Spinner class="mr-2 size-4 animate-spin" />
                        {/if}
                        Simpan Biodata
                    </Button>
                </div>
            </form>
        </CardContent>
    </Card>
</div>
