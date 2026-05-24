<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('student');

        return [
            'name' => ['required', 'string', 'min:3', 'max:255', "regex:/^[\pL\s.\'-]+$/u"],
            'email' => ['nullable', 'email', 'max:255', "unique:users,email,{$user->id}"],
            'password' => ['nullable', 'string', 'min:8', 'regex:/[a-z]/i', 'regex:/[0-9]/'],
            'nisn' => ['required', 'digits:10', "unique:student_profiles,nisn,{$user->studentProfile?->id}"],
            'nis' => ['required', 'string', 'max:20'],
            'class_id' => ['nullable', 'string', 'exists:classes,id'],
            'phone' => ['nullable', 'string', 'min:10', 'max:20', 'regex:/^[0-9+\-\s()]*$/'],
            'address' => ['nullable', 'string'],
        ];
    }
}
