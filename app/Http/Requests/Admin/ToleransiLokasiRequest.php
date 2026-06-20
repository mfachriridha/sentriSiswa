<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ToleransiLokasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'toleransi_meter' => ['required', 'integer', 'min:0', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'toleransi_meter.required' => 'Toleransi wajib diisi.',
            'toleransi_meter.integer' => 'Toleransi harus berupa angka.',
            'toleransi_meter.min' => 'Toleransi minimal 0 meter.',
            'toleransi_meter.max' => 'Toleransi maksimal 500 meter.',
        ];
    }
}
