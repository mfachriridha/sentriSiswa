<?php

namespace App\Livewire\Admin;

use App\Models\Kelas;
use Livewire\Component;

class KelasCrud extends Component
{
    public bool $showModal = false;

    public bool $editing = false;

    public ?int $editingId = null;

    public string $nama = '';

    public string $tingkat = '10';

    public string $jurusan = 'IPA';

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'tingkat' => ['required', 'in:10,11,12'],
            'jurusan' => ['required', 'in:IPA,IPS,Bahasa'],
        ];
    }

    public function openCreate(): void
    {
        $this->reset(['editing', 'editingId', 'nama', 'tingkat', 'jurusan']);
        $this->showModal = true;
    }

    public function openEdit(Kelas $kelas): void
    {
        $this->editing = true;
        $this->editingId = $kelas->id;
        $this->nama = $kelas->nama;
        $this->tingkat = (string) $kelas->tingkat;
        $this->jurusan = $kelas->jurusan;
        $this->showModal = true;
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->editing) {
            Kelas::where('id', $this->editingId)->update($data);
        } else {
            Kelas::create($data);
        }

        $this->showModal = false;
        $this->reset(['editing', 'editingId', 'nama', 'tingkat', 'jurusan']);
    }

    public function delete(Kelas $kelas): void
    {
        $kelas->delete();
    }

    public function render()
    {
        return view('livewire.admin.kelas-crud', [
            'semuaKelas' => Kelas::orderBy('tingkat')->orderBy('nama')->get(),
        ])->title(__('Manajemen Kelas'));
    }
}
