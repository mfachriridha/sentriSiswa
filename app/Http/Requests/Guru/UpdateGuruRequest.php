<?php

namespace App\Http\Requests\Guru;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGuruRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('guru');

        return [
            'nama' => ['required', 'string', 'min:3', 'max:100', "regex:/^[\pL\s.,\'-]+$/u"],
            'nip' => ['required', 'string', 'max:30', "unique:profil_guru,nip,{$user->profilGuru?->nip},nip"],
            'peran' => ['required', 'in:wali_kelas,bk,kesiswaan'],
            'tingkat' => ['nullable', 'required_if:peran,bk', 'in:10,11,12'],
            'kelas_id' => [
                'nullable',
                Rule::exists('kelas', 'id')->where(
                    fn ($query) => $query->whereNull('wali_kelas_id')->orWhere('wali_kelas_id', $user->id)
                ),
            ],
        ];
    }
}
