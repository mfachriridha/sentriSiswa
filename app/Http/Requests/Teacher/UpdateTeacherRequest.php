<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('teacher');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', "unique:users,email,{$user->id}"],
            'password' => ['nullable', 'string', 'min:8'],
            'nip' => ['nullable', 'string', 'max:30', "unique:teacher_profiles,nip,{$user->teacherProfile?->id}"],
            'phone' => ['nullable', 'string', 'max:20'],
            'teacher_type' => ['required', 'in:homeroom,counselor'],
            'grade' => ['nullable', 'required_if:teacher_type,counselor', 'in:10,11,12'],
        ];
    }
}
