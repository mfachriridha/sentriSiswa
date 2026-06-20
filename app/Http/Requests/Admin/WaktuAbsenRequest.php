<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WaktuAbsenRequest extends FormRequest
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
        $jamOptions = array_map(fn (int $h): string => sprintf('%02d', $h), range(0, 23));
        $menitOptions = array_map(fn (int $m): string => sprintf('%02d', $m), range(0, 59));
        $toleransiOptions = array_map('strval', [0, 5, 10, 15, 20, 30, 45, 60, 90, 120]);

        return [
            'jam_mulai' => ['required', Rule::in($jamOptions)],
            'menit_mulai' => ['required', Rule::in($menitOptions)],
            'jam_selesai' => ['required', Rule::in($jamOptions)],
            'menit_selesai' => ['required', Rule::in($menitOptions)],
            'toleransi_terlambat' => ['required', Rule::in($toleransiOptions)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            '*.required' => 'Field ini wajib diisi.',
            '*.in' => 'Pilihan tidak valid.',
        ];
    }
}
