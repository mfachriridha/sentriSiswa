<?php

namespace App\Http\Requests\PengajuanPoin;

use Illuminate\Foundation\Http\FormRequest;

class RejectPengajuanPoinRequest extends FormRequest
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
            'alasan_penolakan' => ['required', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'alasan_penolakan.required' => 'Alasan penolakan wajib diisi.',
            'alasan_penolakan.max' => 'Alasan penolakan maksimal 1000 karakter.',
        ];
    }
}
