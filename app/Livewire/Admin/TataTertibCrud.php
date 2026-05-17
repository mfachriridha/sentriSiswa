<?php

namespace App\Livewire\Admin;

use App\Models\TataTertib;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class TataTertibCrud extends Component
{
    use WithFileUploads;

    public bool $showModal = false;

    public string $judul = '';

    public $file_pdf;

    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:255'],
            'file_pdf' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ];
    }

    public function openUpload(): void
    {
        $this->reset(['judul', 'file_pdf']);
        $this->showModal = true;
    }

    public function upload(): void
    {
        $this->validate();

        $path = $this->file_pdf->store('tata-tertib', 'public');

        TataTertib::create([
            'judul' => $this->judul,
            'file_pdf' => $path,
        ]);

        $this->showModal = false;
        $this->reset(['judul', 'file_pdf']);
    }

    public function delete(TataTertib $tataTertib): void
    {
        if ($tataTertib->file_pdf) {
            Storage::disk('public')->delete($tataTertib->file_pdf);
        }
        $tataTertib->delete();
    }

    public function render()
    {
        return view('livewire.admin.tata-tertib-crud', [
            'daftarTataTertib' => TataTertib::latest()->get(),
        ])->title(__('Tata Tertib'));
    }
}
