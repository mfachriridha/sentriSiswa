<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'email' => ['required', 'email', 'max:255', 'unique:pengguna,email'],
            'password' => ['required', 'string', 'min:8', 'regex:/[a-z]/i', 'regex:/[0-9]/', 'confirmed'],
        ];

        if (session('register_role') === 'teacher') {
            $rules['telepon'] = ['required', 'string', 'min:10', 'max:15', 'regex:/^[0-9+\-\s()]*$/'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.regex' => 'Kata sandi harus mengandung huruf dan angka.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'telepon.required' => 'Nomor HP wajib diisi.',
            'telepon.min' => 'Nomor HP minimal 10 digit.',
            'telepon.regex' => 'Format nomor HP tidak valid.',
        ];
    }
}
