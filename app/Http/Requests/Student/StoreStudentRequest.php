<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
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
            'nisn' => ['required', 'digits:10', 'unique:profil_siswa,nisn'],
            'nis' => ['required', 'string', 'max:20', 'unique:profil_siswa,nis'],
            'kelas_id' => ['nullable', 'string', 'exists:kelas,id'],
            'telepon' => ['nullable', 'string', 'min:10', 'max:20', 'regex:/^[0-9+\-\s()]*$/'],
            'alamat' => ['nullable', 'string'],
        ];
    }
}
