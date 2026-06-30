<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfilAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255', Rule::unique('pengguna', 'email')->ignore($this->user())],
            'password' => ['nullable', 'string', 'min:8', 'regex:/[a-z]/i', 'regex:/[0-9]/'],
            'whatsapp_number' => ['nullable', 'string', 'min:10', 'max:20', 'regex:/^[0-9+\-\s()]*$/'],
            'photo' => ['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.regex' => 'Kata sandi harus memuat huruf dan angka.',
            'whatsapp_number.min' => 'Nomor WhatsApp minimal 10 digit.',
            'whatsapp_number.max' => 'Nomor WhatsApp maksimal 20 karakter.',
            'whatsapp_number.regex' => 'Format nomor WhatsApp tidak valid.',
            'photo.image' => 'Foto harus berupa gambar.',
            'photo.max' => 'Ukuran foto maksimal 2 MB.',
            'photo.mimes' => 'Format foto hanya JPG, JPEG, atau PNG.',
        ];
    }
}
