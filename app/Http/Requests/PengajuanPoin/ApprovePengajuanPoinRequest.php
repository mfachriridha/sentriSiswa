<?php

namespace App\Http\Requests\PengajuanPoin;

use Illuminate\Foundation\Http\FormRequest;

class ApprovePengajuanPoinRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isKesiswaan() === true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'jumlah_poin' => ['required', 'integer', 'min:1', 'max:100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'jumlah_poin.required' => 'Jumlah poin wajib diisi.',
            'jumlah_poin.integer' => 'Jumlah poin harus berupa angka.',
            'jumlah_poin.min' => 'Jumlah poin minimal 1.',
            'jumlah_poin.max' => 'Jumlah poin maksimal 100.',
        ];
    }
}
