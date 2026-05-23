<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'nisn' => ['nullable', 'string', 'max:10', 'unique:student_profiles,nisn'],
            'nis' => ['nullable', 'string', 'max:20'],
            'class_id' => ['nullable', 'string', 'exists:classes,id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
        ];
    }
}
