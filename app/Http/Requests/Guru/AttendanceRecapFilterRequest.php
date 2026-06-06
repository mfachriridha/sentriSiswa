<?php

namespace App\Http\Requests\Guru;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttendanceRecapFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'month' => ['nullable', 'date_format:Y-m'],
            'status' => ['nullable', Rule::in(['hadir', 'terlambat', 'izin', 'sakit', 'alpha'])],
            'student_id' => ['nullable', 'integer', Rule::exists('student_profiles', 'id')],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'start_date.date_format' => 'Tanggal mulai tidak valid.',
            'end_date.date_format' => 'Tanggal selesai tidak valid.',
            'end_date.after_or_equal' => 'Tanggal selesai harus sama dengan atau setelah tanggal mulai.',
            'month.date_format' => 'Bulan tidak valid.',
            'status.in' => 'Status absensi tidak valid.',
            'student_id.exists' => 'Siswa tidak valid.',
        ];
    }
}
