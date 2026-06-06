<?php

namespace App\Http\Requests\Guru;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isStudentAffairs() === true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'rule_pdf' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul tata tertib wajib diisi.',
            'rule_pdf.required' => 'File PDF tata tertib wajib diunggah.',
            'rule_pdf.mimes' => 'File tata tertib harus berupa PDF.',
            'rule_pdf.max' => 'Ukuran PDF maksimal 10 MB.',
        ];
    }
}
