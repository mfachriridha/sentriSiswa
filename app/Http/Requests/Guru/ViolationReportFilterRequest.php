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
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'class_id' => ['nullable', 'integer', Rule::exists('classes', 'id')],
            'grade' => ['nullable', Rule::in(['10', '11', '12'])],
            'category' => ['nullable', Rule::in(['light', 'medium', 'heavy', 'severe'])],
            'status' => ['nullable', Rule::in(['pending', 'approved', 'rejected'])],
        ];
    }
}
