<?php

namespace App\Http\Requests\Class;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'identifier' => ['required', 'string', 'max:255'],
            'grade' => ['required', 'in:10,11,12'],
            'homeroom_teacher_id' => ['nullable', 'string', 'exists:users,id'],
        ];
    }
}
