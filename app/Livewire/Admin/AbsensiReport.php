<?php

namespace App\Livewire\Admin;

use App\Models\Absensi;
use App\Models\Kelas;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AbsensiReport extends Component
{
    use WithPagination;

    #[Url]
    public ?int $kelas_id = null;

    #[Url]
    public ?string $tanggal = null;

    #[Url]
    public string $status = '';

    public function updatedKelasId(): void
    {
        $this->resetPage();
    }

    public function updatedTanggal(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['kelas_id', 'tanggal', 'status']);
        $this->resetPage();
    }

    public function render()
    {
        $query = Absensi::with(['user.siswa.kelas', 'kelas'])
            ->latest('tanggal')
            ->latest('jam_absen');

        if ($this->kelas_id) {
            $query->where('kelas_id', $this->kelas_id);
        }

        if ($this->tanggal) {
            $query->whereDate('tanggal', $this->tanggal);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        $absensi = $query->paginate(20);

        return view('livewire.admin.absensi-report', [
            'absensi' => $absensi,
            'semuaKelas' => Kelas::orderBy('tingkat')->orderBy('nama')->get(),
        ])->title(__('Laporan Absensi'));
    }
}
