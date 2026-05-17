<?php

namespace App\Livewire\Admin;

use App\Models\PoinPelanggaran;
use Livewire\Component;

class PoinPelanggaranCrud extends Component
{
    public bool $showModal = false;

    public bool $editing = false;

    public ?int $editingId = null;

    public string $kategori = 'Ringan';

    public string $jenis_pelanggaran = '';

    public int $poin = 0;

    public array $expandedCategories = ['Ringan', 'Sedang', 'Berat', 'Amat Berat'];

    public function toggleCategory(string $kategori): void
    {
        if (in_array($kategori, $this->expandedCategories)) {
            $this->expandedCategories = array_values(array_diff($this->expandedCategories, [$kategori]));
        } else {
            $this->expandedCategories[] = $kategori;
        }
    }

    public function rules(): array
    {
        return [
            'kategori' => ['required', 'in:Ringan,Sedang,Berat,Amat Berat'],
            'jenis_pelanggaran' => ['required', 'string', 'max:255'],
            'poin' => ['required', 'integer', 'min:1'],
        ];
    }

    public function openCreate(): void
    {
        $this->reset(['editing', 'editingId', 'kategori', 'jenis_pelanggaran', 'poin']);
        $this->showModal = true;
    }

    public function openEdit(PoinPelanggaran $poin): void
    {
        $this->editing = true;
        $this->editingId = $poin->id;
        $this->kategori = $poin->kategori;
        $this->jenis_pelanggaran = $poin->jenis_pelanggaran;
        $this->poin = $poin->poin;
        $this->showModal = true;
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->editing) {
            PoinPelanggaran::where('id', $this->editingId)->update($data);
        } else {
            PoinPelanggaran::create($data);
        }

        $this->showModal = false;
        $this->reset(['editing', 'editingId', 'kategori', 'jenis_pelanggaran', 'poin']);
    }

    public function delete(PoinPelanggaran $poin): void
    {
        $poin->delete();
    }

    public function render()
    {
        $semua = PoinPelanggaran::orderBy('kategori')->orderBy('jenis_pelanggaran')->get();
        $grouped = $semua->groupBy('kategori');

        return view('livewire.admin.poin-pelanggaran-crud', [
            'grouped' => $grouped,
            'categories' => ['Ringan', 'Sedang', 'Berat', 'Amat Berat'],
        ])->title(__('Poin Pelanggaran'));
    }
}
