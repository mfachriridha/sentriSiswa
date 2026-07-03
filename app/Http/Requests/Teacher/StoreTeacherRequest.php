<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'min:3', 'max:100', "regex:/^[\pL\s.\'-]+$/u"],
            'email' => ['nullable', 'email', 'max:255', 'unique:pengguna,email'],
            'password' => ['nullable', 'string', 'min:8', 'regex:/[a-z]/i', 'regex:/[0-9]/'],
            'nip' => ['required', 'string', 'max:30', 'unique:profil_guru,nip'],
            'telepon' => ['nullable', 'string', 'min:10', 'max:20', 'regex:/^[0-9+\-\s()]*$/'],
            'peran' => ['required', 'in:wali_kelas,bk,kesiswaan'],
            'tingkat' => ['nullable', 'required_if:peran,bk', 'in:10,11,12'],
        ];
    }
}
