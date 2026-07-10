<?php

namespace App\Http\Requests\Guru;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ViolationReportFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isBk() === true || $this->user()?->isKesiswaan() === true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'mulai' => ['nullable', 'date_format:Y-m-d'],
            'selesai' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:mulai'],
            'kelas_id' => ['nullable', 'integer', Rule::exists('kelas', 'id')],
            'tingkat' => ['nullable', Rule::in(['10', '11', '12'])],
            'kategori' => ['nullable', Rule::in(['ringan', 'sedang', 'berat', 'sangat_berat'])],
        ];
    }
}
