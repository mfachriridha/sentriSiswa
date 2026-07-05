<?php

namespace App\Http\Requests\Kelas;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKelasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $kelas = $this->route('kelas');

        return [
            'nama' => ['required', 'string', 'max:20'],
            'tingkat' => ['required', 'in:10,11,12'],
            'wali_kelas_id' => ['nullable', 'string', 'exists:pengguna,id'],
            'siswa_nisn' => ['nullable', 'array'],
            'siswa_nisn.*' => [
                'string',
                Rule::exists('profil_siswa', 'nisn')->where(
                    fn ($query) => $query->whereNull('kelas_id')->orWhere('kelas_id', $kelas->id)
                ),
            ],
        ];
    }
}
