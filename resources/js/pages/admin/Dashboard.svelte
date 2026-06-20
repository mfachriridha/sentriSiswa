<script module lang="ts">
    export const layout = {
        breadcrumbs: [{ title: 'Dashboard Admin', href: '/admin/dashboard' }],
    };
</script>

<script lang="ts">
    import { GraduationCap, School, Users } from '@lucide/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import Heading from '@/components/Heading.svelte';
    import AdminStatCard from '@/components/admin/AdminStatCard.svelte';
    import DashboardBarChart from '@/components/admin/DashboardBarChart.svelte';
    import ShortcutCard from '@/components/admin/ShortcutCard.svelte';

    type StatData = {
        totalSiswa: number;
        totalGuru: number;
        totalKelas: number;
        totalTerdaftar: number;
    };

    type ChartItem = {
        label: string;
        value: number;
        variant?:
            | 'primary'
            | 'success'
            | 'warning'
            | 'error'
            | 'info'
            | 'neutral';
    };

    type ChartsData = {
        studentsByGrade: ChartItem[];
        registration: ChartItem[];
        roles: ChartItem[];
    };

    let {
        statistik = {
            totalSiswa: 0,
            totalGuru: 0,
            totalKelas: 0,
            totalTerdaftar: 0,
        },
        charts = { studentsByGrade: [], registration: [], roles: [] },
    }: {
        statistik?: StatData;
        charts?: ChartsData;
    } = $props();
</script>

<AppHead title="Dashboard Admin" />

<div class="w-full space-y-8">
    <Heading title="Dashboard Admin" description="Ringkasan data sekolah" />

    <!-- Statistik Cards -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <AdminStatCard
            title="Total Siswa"
            value={statistik.totalSiswa}
            description="Siswa terdaftar aktif"
            tone="sky"
        >
            {#snippet icon()}
                <GraduationCap class="size-5" />
            {/snippet}
        </AdminStatCard>

        <AdminStatCard
            title="Total Guru"
            value={statistik.totalGuru}
            description="Wali Kelas, BK, & Kesiswaan"
            tone="emerald"
        >
            {#snippet icon()}
                <Users class="size-5" />
            {/snippet}
        </AdminStatCard>

        <AdminStatCard
            title="Total Kelas"
            value={statistik.totalKelas}
            description="Jumlah kelas aktif saat ini"
            tone="amber"
        >
            {#snippet icon()}
                <School class="size-5" />
            {/snippet}
        </AdminStatCard>
    </div>

    <!-- Charts Section -->
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <DashboardBarChart
            title="Siswa per Tingkat"
            items={charts.studentsByGrade.map((i) => ({
                ...i,
                variant: 'primary',
            }))}
        />
        <DashboardBarChart
            title="Status Registrasi Siswa"
            items={charts.registration.map((i) => ({
                ...i,
                variant: i.label === 'Terdaftar' ? 'success' : 'warning',
            }))}
        />
        <DashboardBarChart
            title="Komposisi Akun"
            items={charts.roles.map((i) => ({
                ...i,
                variant:
                    i.label === 'Admin'
                        ? 'error'
                        : i.label === 'Siswa'
                          ? 'info'
                          : 'primary',
            }))}
        />
    </div>

    <!-- Akses Cepat Shortcuts -->
    <div class="space-y-4 pt-2">
        <h3 class="text-base font-semibold text-foreground">Akses Cepat</h3>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <ShortcutCard
                href="/admin/guru"
                title="Data Guru"
                description="Kelola akun dan profil guru sekolah."
            >
                {#snippet icon()}
                    <Users class="size-5" />
                {/snippet}
            </ShortcutCard>

            <ShortcutCard
                href="/admin/siswa"
                title="Data Siswa"
                description="Kelola akun, kelas, dan biodata siswa."
            >
                {#snippet icon()}
                    <GraduationCap class="size-5" />
                {/snippet}
            </ShortcutCard>

            <ShortcutCard
                href="/admin/kelas"
                title="Data Kelas"
                description="Atur kelas dan wali kelas yang bertugas."
            >
                {#snippet icon()}
                    <School class="size-5" />
                {/snippet}
            </ShortcutCard>
        </div>
    </div>
</div>
