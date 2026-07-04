<?php

namespace App\Http\Requests\Kelas;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKelasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:20'],
            'tingkat' => ['required', 'in:10,11,12'],
            'wali_kelas_id' => ['nullable', 'string', 'exists:pengguna,id'],
        ];
    }
}
