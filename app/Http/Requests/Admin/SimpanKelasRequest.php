<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SimpanKelasRequest extends FormRequest
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
        $kelasId = $this->route('kelas')?->id;

        return [
            'nama' => ['required', 'string', 'max:50'],
            'tingkat' => ['required', Rule::in(['10', '11', '12'])],
            'wali_kelas_id' => ['nullable', 'exists:users,id'],
            Rule::unique('kelas', 'nama')->where(function ($query) {
                return $query->where('tingkat', $this->tingkat);
            })->ignore($kelasId),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama kelas wajib diisi.',
            'tingkat.required' => 'Tingkat wajib dipilih.',
            'tingkat.in' => 'Tingkat harus 10, 11, atau 12.',
            'wali_kelas_id.exists' => 'Wali kelas tidak valid.',
        ];
    }
}
