<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('teacher');

        return [
            'nama' => ['required', 'string', 'min:3', 'max:100', "regex:/^[\pL\s.\'-]+$/u"],
            'email' => ['nullable', 'email', 'max:255', "unique:pengguna,email,{$user->id}"],
            'password' => ['nullable', 'string', 'min:8', 'regex:/[a-z]/i', 'regex:/[0-9]/'],
            'nip' => ['required', 'string', 'max:30', "unique:profil_guru,nip,{$user->profilGuru?->nip},nip"],
            'telepon' => ['nullable', 'string', 'min:10', 'max:20', 'regex:/^[0-9+\-\s()]*$/'],
            'peran' => ['required', 'in:wali_kelas,bk,kesiswaan'],
            'tingkat' => ['nullable', 'required_if:peran,bk', 'in:10,11,12'],
        ];
    }
}
