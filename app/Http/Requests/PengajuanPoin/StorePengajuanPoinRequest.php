<?php

namespace App\Http\Requests\PengajuanPoin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePengajuanPoinRequest extends FormRequest
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
            'profil_siswa_id' => ['required', 'string', Rule::exists('profil_siswa', 'nisn')],
            'kategori_pengajuan_poin_id' => ['required', 'integer', Rule::exists('kategori_pengajuan_poin', 'id')],
            'alasan' => ['required', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'profil_siswa_id.required' => 'Siswa wajib dipilih.',
            'profil_siswa_id.exists' => 'Siswa tidak valid.',
            'kategori_pengajuan_poin_id.required' => 'Kategori prestasi wajib dipilih.',
            'kategori_pengajuan_poin_id.exists' => 'Kategori prestasi tidak valid.',
            'alasan.required' => 'Keterangan detail wajib diisi.',
            'alasan.max' => 'Keterangan detail maksimal 1000 karakter.',
        ];
    }
}
