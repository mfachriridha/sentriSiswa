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
        // Kata sandi tidak diatur di formulir ini. Penggantian kata sandi punya
        // alurnya sendiri yang memakai kode OTP, jadi aturan untuk kata sandi
        // tidak dicantumkan di sini agar tidak menyesatkan.
        return [
            'nama' => ['required', 'string', 'min:3', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('pengguna', 'email')->ignore($this->user())],
            'whatsapp_number' => ['nullable', 'string', 'min:10', 'max:20', 'regex:/^[0-9+\-\s()]*$/'],
            'photo' => ['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png'],
            'delete_photo' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama wajib diisi.',
            'nama.min' => 'Nama minimal 3 karakter.',
            'nama.max' => 'Nama maksimal 100 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'whatsapp_number.min' => 'Nomor WhatsApp minimal 10 digit.',
            'whatsapp_number.max' => 'Nomor WhatsApp maksimal 20 karakter.',
            'whatsapp_number.regex' => 'Format nomor WhatsApp tidak valid.',
            'photo.image' => 'Foto harus berupa gambar.',
            'photo.max' => 'Ukuran foto maksimal 2 MB.',
            'photo.mimes' => 'Format foto hanya JPG, JPEG, atau PNG.',
        ];
    }
}
