<?php

namespace App\Http\Requests\StudentViolation;

use App\Models\ViolationType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreStudentViolationRequest extends FormRequest
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
            'student_profile_id' => ['required', 'integer', Rule::exists('student_profiles', 'id')],
            'violation_type_id' => ['required', 'integer', Rule::exists('violation_types', 'id')],
            'violation_date' => ['required', 'date', 'before_or_equal:today'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $violationType = ViolationType::find($this->input('violation_type_id'));

                if ($violationType && ! $violationType->is_active) {
                    $validator->errors()->add('violation_type_id', 'Jenis pelanggaran tidak aktif dan tidak dapat dipilih.');
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'student_profile_id.required' => 'Siswa wajib dipilih.',
            'student_profile_id.exists' => 'Siswa tidak valid.',
            'violation_type_id.required' => 'Jenis pelanggaran wajib dipilih.',
            'violation_type_id.exists' => 'Jenis pelanggaran tidak valid.',
            'violation_date.required' => 'Tanggal pelanggaran wajib diisi.',
            'violation_date.date' => 'Tanggal pelanggaran tidak valid.',
            'violation_date.before_or_equal' => 'Tanggal pelanggaran tidak boleh melebihi hari ini.',
        ];
    }
}
