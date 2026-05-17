<?php

namespace App\Livewire\Admin;

use App\Models\BkTingkat;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\WaliKelas;
use Livewire\Attributes\Computed;
use Livewire\Component;

class GuruCrud extends Component
{
    public bool $showModal = false;

    public bool $showDetailModal = false;

    public bool $editing = false;

    public ?int $editingId = null;

    public ?int $detailGuruId = null;

    public string $detailTab = 'data';

    public string $nama = '';

    public string $nip = '';

    // Wali Kelas
    public ?int $wk_kelas_id = null;

    public string $wk_tahun_ajaran = '';

    // BK
    public array $bk_tingkat = [];

    #[Computed]
    public function daftarKelas()
    {
        return Kelas::orderBy('tingkat')->orderBy('nama')->get();
    }

    #[Computed]
    public function daftarKelasTersedia()
    {
        return Kelas::whereDoesntHave('waliKelas')
            ->orWhereHas('waliKelas', function ($q) {
                if ($this->detailGuruId) {
                    $q->where('guru_id', $this->detailGuruId);
                }
            })
            ->orderBy('tingkat')
            ->orderBy('nama')
            ->get();
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'nip' => ['required', 'string', 'max:50'],
        ];
    }

    public function openCreate(): void
    {
        $this->reset(['editing', 'editingId', 'nama', 'nip']);
        $this->showModal = true;
    }

    public function openEdit(Guru $guru): void
    {
        $this->editing = true;
        $this->editingId = $guru->id;
        $this->nama = $guru->nama;
        $this->nip = $guru->nip;
        $this->showModal = true;
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->editing) {
            Guru::where('id', $this->editingId)->update($data);
        } else {
            Guru::create($data);
        }

        $this->showModal = false;
        $this->reset(['editing', 'editingId', 'nama', 'nip']);
    }

    public function delete(Guru $guru): void
    {
        $guru->waliKelas()->delete();
        $guru->bkTingkat()->delete();
        $guru->delete();
    }

    public function openDetail(Guru $guru): void
    {
        $this->detailGuruId = $guru->id;
        $this->detailTab = 'data';

        $wk = WaliKelas::where('guru_id', $guru->id)->first();
        $this->wk_kelas_id = $wk?->kelas_id;
        $this->wk_tahun_ajaran = $wk?->tahun_ajaran ?? now()->format('Y');

        $this->bk_tingkat = BkTingkat::where('guru_id', $guru->id)
            ->pluck('tingkat')
            ->toArray();

        $this->showDetailModal = true;
    }

    public function setDetailTab(string $tab): void
    {
        $this->detailTab = $tab;
    }

    public function saveWaliKelas(): void
    {
        $this->validate([
            'wk_kelas_id' => ['required', 'exists:kelas,id'],
            'wk_tahun_ajaran' => ['required', 'string', 'max:9'],
        ]);

        WaliKelas::updateOrCreate(
            ['guru_id' => $this->detailGuruId],
            [
                'kelas_id' => $this->wk_kelas_id,
                'tahun_ajaran' => $this->wk_tahun_ajaran,
            ]
        );

        $this->dispatch('saved');
    }

    public function saveBk(): void
    {
        $guruId = $this->detailGuruId;

        BkTingkat::where('guru_id', $guruId)->delete();

        foreach ($this->bk_tingkat as $tingkat) {
            BkTingkat::create([
                'guru_id' => $guruId,
                'tingkat' => $tingkat,
            ]);
        }

        $this->dispatch('saved');
    }

    public function render()
    {
        $gurus = Guru::with('user', 'waliKelas.kelas', 'bkTingkat')
            ->orderBy('nama')
            ->get()
            ->map(function ($guru) {
                $guru->status_aktif = $guru->user_id ? 'Aktif' : 'Belum Aktif';
                $guru->wali_kelas_nama = $guru->waliKelas?->kelas?->nama ?? '-';
                $guru->bk_tingkat_list = $guru->bkTingkat->pluck('tingkat')->sort()->join(', ') ?: '-';

                return $guru;
            });

        return view('livewire.admin.guru-crud', [
            'semuaGuru' => $gurus,
        ])->title(__('Manajemen Guru'));
    }
}
