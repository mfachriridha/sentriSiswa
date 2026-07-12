<?php

namespace App\Http\Requests\Guru;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AttendanceRecapFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'mulai' => ['nullable', 'date_format:Y-m-d'],
            'selesai' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:mulai'],
            'month' => ['nullable', 'date_format:Y-m'],
            'status' => ['nullable', Rule::in(['hadir', 'izin', 'sakit', 'alpha'])],
            'profil_siswa_id' => ['nullable', 'string', Rule::exists('profil_siswa', 'nisn')],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'mulai.date_format' => 'Tanggal mulai tidak valid.',
            'selesai.date_format' => 'Tanggal selesai tidak valid.',
            'selesai.after_or_equal' => 'Tanggal selesai harus sama dengan atau setelah tanggal mulai.',
            'month.date_format' => 'Bulan tidak valid.',
            'status.in' => 'Status absensi tidak valid.',
            'profil_siswa_id.exists' => 'Siswa tidak valid.',
        ];
    }
}
