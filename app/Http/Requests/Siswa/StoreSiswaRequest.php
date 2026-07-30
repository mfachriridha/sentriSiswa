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
            'nisn' => ['required', 'digits_between:10,12', 'unique:profil_siswa,nisn'],
            'nis' => ['required', 'regex:/^[0-9]+$/', 'max:15', 'unique:profil_siswa,nis'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'kelas_id' => ['nullable', 'string', 'exists:kelas,id'],
            'telepon' => ['nullable', 'string', 'min:10', 'max:15', 'regex:/^[0-9+\-\s()]*$/'],
            'alamat' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin tidak valid.',
        ];
    }
}
