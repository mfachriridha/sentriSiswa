<?php

namespace App\Http\Requests\Siswa;

use Illuminate\Foundation\Http\FormRequest;

class StoreSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'min:3', 'max:100', "regex:/^[\pL\s.\'-]+$/u"],
            'nisn' => ['required', 'digits:10', 'unique:profil_siswa,nisn'],
            'nis' => ['required', 'string', 'max:20', 'unique:profil_siswa,nis'],
            'kelas_id' => ['nullable', 'string', 'exists:kelas,id'],
            'telepon' => ['nullable', 'string', 'min:10', 'max:20', 'regex:/^[0-9+\-\s()]*$/'],
            'alamat' => ['nullable', 'string'],
        ];
    }
}
