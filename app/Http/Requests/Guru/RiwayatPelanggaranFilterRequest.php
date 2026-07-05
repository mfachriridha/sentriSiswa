<?php

namespace App\Http\Requests\Guru;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RiwayatPelanggaranFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'profil_siswa_id' => ['nullable', 'string', Rule::exists('profil_siswa', 'nisn')],
            'kategori' => ['nullable', Rule::in(['light', 'medium', 'heavy', 'severe'])],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_from'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'profil_siswa_id.exists' => 'Siswa tidak valid.',
            'kategori.in' => 'Kategori tidak valid.',
            'date_from.date_format' => 'Tanggal mulai tidak valid.',
            'date_to.date_format' => 'Tanggal selesai tidak valid.',
            'date_to.after_or_equal' => 'Tanggal selesai harus sama dengan atau setelah tanggal mulai.',
        ];
    }
}
