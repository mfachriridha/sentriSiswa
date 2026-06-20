<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class KirimOtpRequest extends FormRequest
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
        return [
            'tujuan' => ['required', 'in:ganti_email,ganti_password'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tujuan.required' => 'Tujuan verifikasi wajib dipilih.',
            'tujuan.in' => 'Tujuan tidak valid.',
        ];
    }
}
