<?php

namespace App\Livewire\Admin;

use App\Models\Absensi;
use App\Models\PengajuanPoin;
use App\Models\Siswa;
use Livewire\Component;

class Dashboard extends Component
{
    public int $totalSiswa = 0;

    public int $hadirHariIni = 0;

    public int $tanpaKeterangan = 0;

    public int $pengajuanPending = 0;

    public function mount(): void
    {
        $today = now()->toDateString();

        $this->totalSiswa = Siswa::count();
        $this->hadirHariIni = Absensi::whereDate('tanggal', $today)
            ->where('status', 'hadir')
            ->count();

        $sudahAbsen = Absensi::whereDate('tanggal', $today)
            ->distinct('user_id')
            ->count('user_id');

        $this->tanpaKeterangan = max(0, $this->totalSiswa - $sudahAbsen);
        $this->pengajuanPending = PengajuanPoin::where('status', 'pending')->count();
    }

    public function render()
    {
        return view('livewire.admin.dashboard')
            ->title(__('Dashboard Admin'));
    }
}
