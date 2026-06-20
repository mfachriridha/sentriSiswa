<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SimpanGuruRequest extends FormRequest
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
        $guruId = $this->route('guru')?->id;

        return [
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($guruId)],
            'peran' => ['required', Rule::in(['wali_kelas', 'bk', 'kesiswaan'])],
            'password' => ['nullable', 'string', 'min:8'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'peran.required' => 'Peran wajib dipilih.',
            'peran.in' => 'Peran tidak valid.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
        ];
    }
}
