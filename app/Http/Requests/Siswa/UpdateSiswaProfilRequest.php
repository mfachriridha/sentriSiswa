<?php

namespace App\Http\Requests\Siswa;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateSiswaProfilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = Auth::id();

        return [
            'email' => ['nullable', 'email', 'max:255', "unique:pengguna,email,{$userId}"],
            'telepon' => ['nullable', 'string', 'min:10', 'max:15', 'regex:/^[0-9+\-\s()]*$/'],
            'alamat' => ['nullable', 'string'],
            'password' => ['nullable', 'string', 'min:8', 'regex:/[a-z]/i', 'regex:/[0-9]/'],
            'photo' => ['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png'],
            'delete_photo' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'email.max' => 'Email tidak boleh lebih dari 255 karakter.',
            'telepon.min' => 'Nomor telepon minimal 10 digit.',
            'telepon.max' => 'Nomor telepon maksimal 15 digit.',
            'telepon.regex' => 'Format nomor telepon tidak valid.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.regex' => 'Kata sandi harus memuat huruf dan angka.',
            'photo.image' => 'Foto harus berupa file gambar.',
            'photo.max' => 'Ukuran foto maksimal 2MB.',
            'photo.mimes' => 'Format foto hanya diperbolehkan JPG, JPEG, atau PNG.',
        ];
    }
}
