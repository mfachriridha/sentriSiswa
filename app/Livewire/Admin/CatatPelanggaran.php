<?php

namespace App\Livewire\Admin;

use App\Models\PelanggaranSiswa;
use App\Models\PoinPelanggaran;
use App\Models\Siswa;
use Livewire\Component;

class CatatPelanggaran extends Component
{
    public ?int $siswa_id = null;

    public ?int $poin_pelanggaran_id = null;

    public string $keterangan = '';

    public string $searchSiswa = '';

    public array $hasilPencarian = [];

    public bool $searching = false;

    public function updatedSearchSiswa(): void
    {
        if (strlen($this->searchSiswa) < 2) {
            $this->hasilPencarian = [];

            return;
        }

        $this->searching = true;
        $this->hasilPencarian = Siswa::with('kelas')
            ->where('nama', 'like', '%'.$this->searchSiswa.'%')
            ->orWhere('nis', 'like', '%'.$this->searchSiswa.'%')
            ->orderBy('nama')
            ->limit(10)
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'nama' => $s->nama,
                'nis' => $s->nis,
                'kelas' => $s->kelas?->nama ?? '-',
            ])
            ->toArray();
        $this->searching = false;
    }

    public function selectSiswa(int $id): void
    {
        $siswa = Siswa::with('kelas')->find($id);
        if ($siswa) {
            $this->siswa_id = $siswa->id;
            $this->searchSiswa = $siswa->nama.' - '.$siswa->nis;
        }
        $this->hasilPencarian = [];
    }

    public function simpan(): void
    {
        $this->validate([
            'siswa_id' => ['required', 'exists:siswa,id'],
            'poin_pelanggaran_id' => ['required', 'exists:poin_pelanggaran,id'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $poinPelanggaran = PoinPelanggaran::find($this->poin_pelanggaran_id);
        $siswa = Siswa::find($this->siswa_id);

        PelanggaranSiswa::create([
            'user_id' => $siswa->user_id,
            'poin_pelanggaran_id' => $this->poin_pelanggaran_id,
            'keterangan' => $this->keterangan,
            'dicatat_oleh' => auth()->id(),
        ]);

        $siswa->decrement('poin', $poinPelanggaran->poin);

        $this->reset(['siswa_id', 'poin_pelanggaran_id', 'keterangan', 'searchSiswa', 'hasilPencarian']);
        $this->dispatch('saved');
    }

    public function render()
    {
        $poinPelanggaran = PoinPelanggaran::orderBy('kategori')->orderBy('jenis_pelanggaran')->get();
        $recent = PelanggaranSiswa::with(['user.siswa.kelas', 'poinPelanggaran'])
            ->latest()
            ->limit(50)
            ->get();

        return view('livewire.admin.catat-pelanggaran', [
            'daftarPoin' => $poinPelanggaran,
            'recent' => $recent,
        ])->title(__('Catat Pelanggaran'));
    }
}
