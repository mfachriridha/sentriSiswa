<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SimpanSiswaRequest extends FormRequest
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
        $siswaId = $this->route('siswa')?->id;

        return [
            'nama' => ['required', 'string', 'max:255'],
            'nisn' => ['nullable', 'string', 'max:10', Rule::unique('siswa', 'nisn')->ignore($siswaId)],
            'nis' => ['nullable', 'string', 'max:20'],
            'kelas_id' => ['nullable', 'exists:kelas,id'],
            'telepon' => ['nullable', 'string', 'max:20'],
            'alamat' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama wajib diisi.',
            'nisn.unique' => 'NISN sudah terdaftar.',
            'kelas_id.exists' => 'Kelas tidak valid.',
        ];
    }
}
