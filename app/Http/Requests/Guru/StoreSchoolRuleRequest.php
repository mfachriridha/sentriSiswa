<?php

namespace App\Http\Requests\Guru;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolRuleRequest extends FormRequest
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
            'judul' => ['required', 'string', 'max:200'],
            'file_pdf' => ['required', 'file', 'mimes:pdf', 'max:10240'],
            'dipublikasikan' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'judul.required' => 'Judul tata tertib wajib diisi.',
            'judul.max' => 'Judul tata tertib maksimal 200 karakter.',
            'file_pdf.required' => 'File PDF tata tertib wajib diunggah.',
            'file_pdf.mimes' => 'File tata tertib harus berupa PDF.',
            'file_pdf.max' => 'Ukuran PDF maksimal 10 MB.',
        ];
    }
}
