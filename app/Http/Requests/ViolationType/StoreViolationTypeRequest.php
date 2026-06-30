<?php

namespace App\Http\Requests\ViolationType;

use App\Models\JenisPelanggaran;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreViolationTypeRequest extends FormRequest
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
            'nama' => ['required', 'string', 'max:255', 'unique:jenis_pelanggaran,nama'],
            'kategori' => ['required', Rule::in(array_keys(JenisPelanggaran::categoryLabels()))],
            'pengurangan_poin' => ['required', 'integer', 'min:5', 'max:100'],
            'keterangan' => ['nullable', 'string'],
            'aktif' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $this->validatePointRange($validator);
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama pelanggaran wajib diisi.',
            'nama.unique' => 'Nama pelanggaran sudah digunakan.',
            'kategori.required' => 'Kategori wajib dipilih.',
            'kategori.in' => 'Kategori tidak valid.',
            'pengurangan_poin.required' => 'Poin pelanggaran wajib diisi.',
            'pengurangan_poin.integer' => 'Poin pelanggaran harus berupa angka.',
            'pengurangan_poin.min' => 'Poin pelanggaran minimal 5.',
            'pengurangan_poin.max' => 'Poin pelanggaran maksimal 100.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'aktif' => $this->boolean('aktif'),
        ]);
    }

    private function validatePointRange(Validator $validator): void
    {
        $category = (string) $this->input('kategori');
        $point = (int) $this->input('pengurangan_poin');
        $ranges = JenisPelanggaran::categoryRanges();

        if (! isset($ranges[$category]) || ! $this->filled('pengurangan_poin')) {
            return;
        }

        [$minimumPoint, $maximumPoint] = $ranges[$category];

        if ($point < $minimumPoint || $point > $maximumPoint) {
            $validator->errors()->add('pengurangan_poin', "Poin untuk kategori ini harus berada di antara {$minimumPoint} sampai {$maximumPoint}.");
        }
    }
}
