<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBiodataSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'jenis_kelamin' => ['nullable', Rule::in(['L', 'P'])],
            'agama' => ['nullable', 'string', 'max:50'],
            'anak_ke' => ['nullable', 'string', 'max:10'],
            'sekolah_asal' => ['nullable', 'string', 'max:255'],
            'tanggal_masuk' => ['nullable', 'date'],
            'nama_ayah' => ['nullable', 'string', 'max:255'],
            'pekerjaan_ayah' => ['nullable', 'string', 'max:255'],
            'nama_ibu' => ['nullable', 'string', 'max:255'],
            'pekerjaan_ibu' => ['nullable', 'string', 'max:255'],
            'alamat_ortu' => ['nullable', 'string', 'max:500'],
            'telepon_ortu' => ['nullable', 'string', 'max:20'],
            'nama_wali' => ['nullable', 'string', 'max:255'],
            'pekerjaan_wali' => ['nullable', 'string', 'max:255'],
            'alamat_wali' => ['nullable', 'string', 'max:500'],
            'telepon_wali' => ['nullable', 'string', 'max:20'],
        ];
    }
}
