<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LokasiAbsenRequest extends FormRequest
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
            'kml_file' => ['required', 'file', 'mimetypes:text/xml,application/xml,application/vnd.google-earth.kml+xml', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'kml_file.required' => 'File KML wajib diunggah.',
            'kml_file.file' => 'File tidak valid.',
            'kml_file.mimetypes' => 'File harus berformat KML.',
            'kml_file.max' => 'File maksimal 5 MB.',
        ];
    }
}
