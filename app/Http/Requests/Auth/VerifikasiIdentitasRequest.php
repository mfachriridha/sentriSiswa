<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifikasiIdentitasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string|Rule>>
     */
    public function rules(): array
    {
        return [
            'peran' => ['required', Rule::in(['guru', 'siswa'])],
            'identitas' => ['required', 'string', 'max:50'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'peran.required' => 'Peran wajib dipilih.',
            'peran.in' => 'Peran tidak valid.',
            'identitas.required' => 'Identitas wajib diisi.',
            'identitas.max' => 'Identitas terlalu panjang.',
        ];
    }
}
