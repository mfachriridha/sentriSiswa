<?php

namespace App\Http\Requests\Guru;

use Illuminate\Foundation\Http\FormRequest;

class StoreGuruRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'min:3', 'max:100', "regex:/^[\pL\s.\'-]+$/u"],
            'nip' => ['required', 'string', 'max:30', 'unique:profil_guru,nip'],
            'telepon' => ['nullable', 'string', 'min:10', 'max:15', 'regex:/^[0-9+\-\s()]*$/'],
            'peran' => ['required', 'in:wali_kelas,bk,kesiswaan'],
            'tingkat' => ['nullable', 'required_if:peran,bk', 'in:10,11,12'],
        ];
    }
}
