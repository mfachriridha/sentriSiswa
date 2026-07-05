<?php

namespace App\Http\Requests\Siswa;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('siswa');

        return [
            'nama' => ['required', 'string', 'min:3', 'max:100', "regex:/^[\pL\s.\'-]+$/u"],
            'nisn' => ['required', 'digits:10', "unique:profil_siswa,nisn,{$user->profilSiswa?->nisn},nisn"],
            'nis' => ['required', 'string', 'max:15', "unique:profil_siswa,nis,{$user->profilSiswa?->nis},nis"],
            'kelas_id' => ['nullable', 'string', 'exists:kelas,id'],
            'telepon' => ['nullable', 'string', 'min:10', 'max:15', 'regex:/^[0-9+\-\s()]*$/'],
            'alamat' => ['nullable', 'string'],
        ];
    }
}
