<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class VerifyIdentityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'role' => ['required', 'in:teacher,student'],
            'identity' => ['required', 'string', 'regex:/^[0-9]+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'role.required' => 'Pilih peran Anda.',
            'role.in' => 'Peran tidak valid.',
            'identity.required' => 'Masukkan NIP atau NISN/NIS.',
            'identity.string' => 'Format tidak valid.',
            'identity.regex' => 'NIP, NISN, atau NIS hanya boleh berisi angka.',
        ];
    }
}
