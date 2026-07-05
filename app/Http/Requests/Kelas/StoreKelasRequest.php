<?php

namespace App\Http\Requests\Kelas;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKelasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:20'],
            'tingkat' => ['required', 'in:10,11,12'],
            'wali_kelas_id' => ['nullable', 'string', 'exists:pengguna,id'],
            'siswa_nisn' => ['nullable', 'array'],
            'siswa_nisn.*' => [
                'string',
                Rule::exists('profil_siswa', 'nisn')->whereNull('kelas_id'),
            ],
        ];
    }
}
