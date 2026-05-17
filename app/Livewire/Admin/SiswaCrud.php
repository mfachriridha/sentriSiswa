<?php

namespace App\Livewire\Admin;

use App\Models\BiodataSiswa;
use App\Models\Kelas;
use App\Models\OrangTuaSiswa;
use App\Models\Siswa;
use Livewire\Attributes\Computed;
use Livewire\Component;

class SiswaCrud extends Component
{
    public bool $showModal = false;

    public bool $showDetailModal = false;

    public bool $editing = false;

    public ?int $editingId = null;

    public ?int $detailSiswaId = null;

    public string $detailTab = 'dasar';

    public string $nama = '';

    public string $nis = '';

    public string $nisn = '';

    public string $jenis_kelamin = 'L';

    public ?int $kelas_id = null;

    public string $no_hp = '';

    // Biodata fields
    public string $tempat_lahir = '';

    public ?string $tanggal_lahir = null;

    public string $agama = '';

    public string $status_keluarga = '';

    public string $anak_ke = '';

    public string $alamat = '';

    public string $no_telp_rumah = '';

    public string $sekolah_asal = '';

    public string $diterima_kelas = '';

    public ?string $diterima_tanggal = null;

    // Orang Tua fields
    public string $nama_ayah = '';

    public string $nama_ibu = '';

    public string $alamat_ortu = '';

    public string $no_telp_ortu = '';

    public string $pekerjaan_ayah = '';

    public string $pekerjaan_ibu = '';

    public string $nama_wali = '';

    public string $alamat_wali = '';

    public string $no_telp_wali = '';

    public string $pekerjaan_wali = '';

    #[Computed]
    public function daftarKelas()
    {
        return Kelas::orderBy('tingkat')->orderBy('nama')->get();
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:255'],
            'nis' => ['required', 'string', 'max:50'],
            'nisn' => ['nullable', 'string', 'max:50'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'kelas_id' => ['required', 'exists:kelas,id'],
            'no_hp' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(Siswa $siswa): void
    {
        $this->editing = true;
        $this->editingId = $siswa->id;
        $this->nama = $siswa->nama;
        $this->nis = $siswa->nis;
        $this->nisn = $siswa->nisn ?? '';
        $this->jenis_kelamin = $siswa->jenis_kelamin;
        $this->kelas_id = $siswa->kelas_id;
        $this->no_hp = $siswa->no_hp ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $data = $this->validate();

        if ($this->editing) {
            Siswa::where('id', $this->editingId)->update($data);
        } else {
            Siswa::create($data);
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(Siswa $siswa): void
    {
        $siswa->delete();
    }

    public function openDetail(Siswa $siswa): void
    {
        $this->detailSiswaId = $siswa->id;
        $this->detailTab = 'dasar';
        $this->loadBiodata();
        $this->loadOrangTua();
        $this->showDetailModal = true;
    }

    public function setDetailTab(string $tab): void
    {
        $this->detailTab = $tab;
        if ($tab === 'biodata') {
            $this->loadBiodata();
        } elseif ($tab === 'orangtua') {
            $this->loadOrangTua();
        }
    }

    public function loadBiodata(): void
    {
        $biodata = BiodataSiswa::where('siswa_id', $this->detailSiswaId)->first();
        if ($biodata) {
            $this->tempat_lahir = $biodata->tempat_lahir ?? '';
            $this->tanggal_lahir = $biodata->tanggal_lahir ?? null;
            $this->agama = $biodata->agama ?? '';
            $this->status_keluarga = $biodata->status_keluarga ?? '';
            $this->anak_ke = $biodata->anak_ke ?? '';
            $this->alamat = $biodata->alamat ?? '';
            $this->no_telp_rumah = $biodata->no_telp_rumah ?? '';
            $this->sekolah_asal = $biodata->sekolah_asal ?? '';
            $this->diterima_kelas = $biodata->diterima_kelas ?? '';
            $this->diterima_tanggal = $biodata->diterima_tanggal ?? null;
        } else {
            $this->resetBiodataFields();
        }
    }

    public function saveBiodata(): void
    {
        $this->validate([
            'tempat_lahir' => ['nullable', 'string', 'max:255'],
            'tanggal_lahir' => ['nullable', 'date'],
            'agama' => ['nullable', 'string', 'max:50'],
            'status_keluarga' => ['nullable', 'string', 'max:50'],
            'anak_ke' => ['nullable', 'string', 'max:10'],
            'alamat' => ['nullable', 'string'],
            'no_telp_rumah' => ['nullable', 'string', 'max:20'],
            'sekolah_asal' => ['nullable', 'string', 'max:255'],
            'diterima_kelas' => ['nullable', 'string', 'max:50'],
            'diterima_tanggal' => ['nullable', 'date'],
        ]);

        BiodataSiswa::updateOrCreate(
            ['siswa_id' => $this->detailSiswaId],
            $this->only([
                'tempat_lahir', 'tanggal_lahir', 'agama', 'status_keluarga',
                'anak_ke', 'alamat', 'no_telp_rumah', 'sekolah_asal',
                'diterima_kelas', 'diterima_tanggal',
            ])
        );

        $this->dispatch('saved');
    }

    public function loadOrangTua(): void
    {
        $ortu = OrangTuaSiswa::where('siswa_id', $this->detailSiswaId)->first();
        if ($ortu) {
            $this->nama_ayah = $ortu->nama_ayah ?? '';
            $this->nama_ibu = $ortu->nama_ibu ?? '';
            $this->alamat_ortu = $ortu->alamat_ortu ?? '';
            $this->no_telp_ortu = $ortu->no_telp_ortu ?? '';
            $this->pekerjaan_ayah = $ortu->pekerjaan_ayah ?? '';
            $this->pekerjaan_ibu = $ortu->pekerjaan_ibu ?? '';
            $this->nama_wali = $ortu->nama_wali ?? '';
            $this->alamat_wali = $ortu->alamat_wali ?? '';
            $this->no_telp_wali = $ortu->no_telp_wali ?? '';
            $this->pekerjaan_wali = $ortu->pekerjaan_wali ?? '';
        } else {
            $this->resetOrangTuaFields();
        }
    }

    public function saveOrangTua(): void
    {
        $this->validate([
            'nama_ayah' => ['nullable', 'string', 'max:255'],
            'nama_ibu' => ['nullable', 'string', 'max:255'],
            'alamat_ortu' => ['nullable', 'string'],
            'no_telp_ortu' => ['nullable', 'string', 'max:20'],
            'pekerjaan_ayah' => ['nullable', 'string', 'max:255'],
            'pekerjaan_ibu' => ['nullable', 'string', 'max:255'],
            'nama_wali' => ['nullable', 'string', 'max:255'],
            'alamat_wali' => ['nullable', 'string'],
            'no_telp_wali' => ['nullable', 'string', 'max:20'],
            'pekerjaan_wali' => ['nullable', 'string', 'max:255'],
        ]);

        OrangTuaSiswa::updateOrCreate(
            ['siswa_id' => $this->detailSiswaId],
            $this->only([
                'nama_ayah', 'nama_ibu', 'alamat_ortu', 'no_telp_ortu',
                'pekerjaan_ayah', 'pekerjaan_ibu', 'nama_wali',
                'alamat_wali', 'no_telp_wali', 'pekerjaan_wali',
            ])
        );

        $this->dispatch('saved');
    }

    private function resetForm(): void
    {
        $this->reset(array_merge(
            ['editing', 'editingId', 'nama', 'nis', 'nisn', 'jenis_kelamin', 'kelas_id', 'no_hp'],
            $this->biodataFieldNames(),
            $this->orangTuaFieldNames(),
        ));
    }

    private function resetBiodataFields(): void
    {
        $this->reset($this->biodataFieldNames());
    }

    private function resetOrangTuaFields(): void
    {
        $this->reset($this->orangTuaFieldNames());
    }

    private function biodataFieldNames(): array
    {
        return [
            'tempat_lahir', 'tanggal_lahir', 'agama', 'status_keluarga',
            'anak_ke', 'alamat', 'no_telp_rumah', 'sekolah_asal',
            'diterima_kelas', 'diterima_tanggal',
        ];
    }

    private function orangTuaFieldNames(): array
    {
        return [
            'nama_ayah', 'nama_ibu', 'alamat_ortu', 'no_telp_ortu',
            'pekerjaan_ayah', 'pekerjaan_ibu', 'nama_wali',
            'alamat_wali', 'no_telp_wali', 'pekerjaan_wali',
        ];
    }

    public function render()
    {
        $siswas = Siswa::with('kelas', 'user')
            ->orderBy('nama')
            ->get()
            ->map(function ($siswa) {
                $siswa->status_aktif = $siswa->user_id ? 'Aktif' : 'Belum Aktif';

                return $siswa;
            });

        return view('livewire.admin.siswa-crud', [
            'semuaSiswa' => $siswas,
        ])->title(__('Manajemen Siswa'));
    }
}
