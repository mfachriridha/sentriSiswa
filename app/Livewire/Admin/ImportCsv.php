<?php

namespace App\Livewire\Admin;

use App\Services\CsvImportService;
use Livewire\Component;
use Livewire\WithFileUploads;

class ImportCsv extends Component
{
    use WithFileUploads;

    public $file;

    public string $tipe = 'guru';

    public bool $processing = false;

    public ?array $result = null;

    public function downloadTemplate(string $tipe)
    {
        if ($tipe === 'guru') {
            $content = "Nama;nip\nGuru Satu;1234567890123456\nGuru Dua;6543210987654321";

            return response()->streamDownload(function () use ($content) {
                echo $content;
            }, 'template-guru.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        $content = "Nama;Jenis Kelamin;Kelas;NIS;NISN\nSiswa Satu;Laki-laki;X IPA 1;1234567890;1234567890\nSiswa Dua;Perempuan;X IPS 1;0987654321;0987654321";

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, 'template-siswa.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function processImport(): void
    {
        $this->validate([
            'file' => ['required', 'mimes:csv,txt', 'max:10240'],
        ]);

        $this->processing = true;

        $path = $this->file->store('temp-imports');

        $service = new CsvImportService;
        $fullPath = storage_path('app/'.$path);

        if ($this->tipe === 'guru') {
            $this->result = $service->importGuru($fullPath);
        } else {
            $this->result = $service->importSiswa($fullPath);
        }

        @unlink($fullPath);
        $this->processing = false;
        $this->reset('file');
    }

    public function render()
    {
        return view('livewire.admin.import-csv')
            ->title(__('Import Data'));
    }
}
