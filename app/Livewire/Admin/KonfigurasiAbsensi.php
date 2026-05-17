<?php

namespace App\Livewire\Admin;

use App\Models\KonfigurasiAbsensi as KonfigurasiModel;
use Livewire\Component;

class KonfigurasiAbsensi extends Component
{
    public string $jam_mulai_absen = '06:00';

    public string $batas_terlambat = '07:30';

    public string $jam_akhir_absen = '18:00';

    public function mount(): void
    {
        $this->jam_mulai_absen = KonfigurasiModel::where('kunci', 'jam_mulai_absen')->value('nilai') ?? '06:00';
        $this->batas_terlambat = KonfigurasiModel::where('kunci', 'batas_terlambat')->value('nilai') ?? '07:30';
        $this->jam_akhir_absen = KonfigurasiModel::where('kunci', 'jam_akhir_absen')->value('nilai') ?? '18:00';
    }

    public function save(): void
    {
        $this->validate([
            'jam_mulai_absen' => ['required', 'date_format:H:i'],
            'batas_terlambat' => ['required', 'date_format:H:i'],
            'jam_akhir_absen' => ['required', 'date_format:H:i'],
        ]);

        KonfigurasiModel::updateOrCreate(
            ['kunci' => 'jam_mulai_absen'],
            ['nilai' => $this->jam_mulai_absen]
        );
        KonfigurasiModel::updateOrCreate(
            ['kunci' => 'batas_terlambat'],
            ['nilai' => $this->batas_terlambat]
        );
        KonfigurasiModel::updateOrCreate(
            ['kunci' => 'jam_akhir_absen'],
            ['nilai' => $this->jam_akhir_absen]
        );

        $this->dispatch('saved');
    }

    public function render()
    {
        return view('livewire.admin.konfigurasi-absensi')
            ->title(__('Konfigurasi Absensi'));
    }
}
