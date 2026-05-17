<?php

namespace App\Livewire\Admin;

use App\Models\PengajuanPoin;
use App\Models\Siswa;
use Livewire\Component;

class PengajuanPoinReview extends Component
{
    public string $filter = 'pending';

    public string $catatan_admin = '';

    public ?int $tolakId = null;

    public bool $showTolakModal = false;

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
    }

    public function setujui(int $id): void
    {
        $pengajuan = PengajuanPoin::findOrFail($id);

        if ($pengajuan->status !== 'pending') {
            return;
        }

        $pengajuan->update([
            'status' => 'disetujui',
            'diproses_oleh' => auth()->id(),
        ]);

        $siswa = Siswa::where('user_id', $pengajuan->user_id)->first();
        if ($siswa) {
            $siswa->increment('poin', $pengajuan->jumlah_poin);
        }
    }

    public function openTolak(int $id): void
    {
        $this->tolakId = $id;
        $this->catatan_admin = '';
        $this->showTolakModal = true;
    }

    public function tolak(): void
    {
        $this->validate([
            'catatan_admin' => ['required', 'string', 'max:500'],
        ]);

        $pengajuan = PengajuanPoin::findOrFail($this->tolakId);
        $pengajuan->update([
            'status' => 'ditolak',
            'diproses_oleh' => auth()->id(),
            'catatan_admin' => $this->catatan_admin,
        ]);

        $this->showTolakModal = false;
        $this->reset(['tolakId', 'catatan_admin']);
    }

    public function render()
    {
        $query = PengajuanPoin::with(['user.siswa.kelas', 'guru', 'diprosesOleh'])
            ->latest();

        if ($this->filter !== 'semua') {
            $query->where('status', $this->filter);
        }

        $pengajuan = $query->get();

        return view('livewire.admin.pengajuan-poin-review', [
            'daftarPengajuan' => $pengajuan,
        ])->title(__('Pengajuan Poin'));
    }
}
