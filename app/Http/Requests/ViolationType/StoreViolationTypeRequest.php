<?php

namespace App\Http\Requests\ViolationType;

use App\Models\ViolationType;
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
            'name' => ['required', 'string', 'max:255', 'unique:violation_types,name'],
            'category' => ['required', Rule::in(array_keys(ViolationType::categoryLabels()))],
            'point_deduction' => ['required', 'integer', 'min:5', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_active' => ['required', 'boolean'],
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
            'name.required' => 'Nama pelanggaran wajib diisi.',
            'name.unique' => 'Nama pelanggaran sudah digunakan.',
            'category.required' => 'Kategori wajib dipilih.',
            'category.in' => 'Kategori tidak valid.',
            'point_deduction.required' => 'Poin pelanggaran wajib diisi.',
            'point_deduction.integer' => 'Poin pelanggaran harus berupa angka.',
            'point_deduction.min' => 'Poin pelanggaran minimal 5.',
            'point_deduction.max' => 'Poin pelanggaran maksimal 100.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    private function validatePointRange(Validator $validator): void
    {
        $category = (string) $this->input('category');
        $point = (int) $this->input('point_deduction');
        $ranges = ViolationType::categoryRanges();

        if (! isset($ranges[$category]) || ! $this->filled('point_deduction')) {
            return;
        }

        [$minimumPoint, $maximumPoint] = $ranges[$category];

        if ($point < $minimumPoint || $point > $maximumPoint) {
            $validator->errors()->add('point_deduction', "Poin untuk kategori ini harus berada di antara {$minimumPoint} sampai {$maximumPoint}.");
        }
    }
}
