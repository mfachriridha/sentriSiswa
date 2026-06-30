<?php

namespace App\Http\Requests\Guru;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProfilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $userId = Auth::id();

        return [
            'nama' => ['required', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255', "unique:pengguna,email,{$userId}"],
            'telepon' => ['nullable', 'string', 'min:10', 'max:20', 'regex:/^[0-9+\-\s()]*$/'],
            'password' => ['nullable', 'string', 'min:8', 'regex:/[a-z]/i', 'regex:/[0-9]/'],
            'photo' => ['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png'],
            'delete_photo' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama tidak boleh kosong.',
            'nama.max' => 'Nama tidak boleh lebih dari 100 karakter.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'email.max' => 'Email tidak boleh lebih dari 255 karakter.',
            'telepon.min' => 'Nomor telepon minimal 10 digit.',
            'telepon.max' => 'Nomor telepon maksimal 20 digit.',
            'telepon.regex' => 'Format nomor telepon tidak valid.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.regex' => 'Kata sandi harus memuat huruf dan angka.',
            'photo.image' => 'Foto harus berupa file gambar.',
            'photo.max' => 'Ukuran foto maksimal 2MB.',
            'photo.mimes' => 'Format foto hanya diperbolehkan JPG, JPEG, atau PNG.',
        ];
    }
}
