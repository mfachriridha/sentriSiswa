<?php

namespace App\Http\Requests\PelanggaranSiswa;

use App\Models\JenisPelanggaran;
use App\Models\PelanggaranSiswa;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdatePelanggaranSiswaRequest extends FormRequest
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
            'profil_siswa_id' => ['required', 'string', Rule::exists('profil_siswa', 'nisn')],
            'jenis_pelanggaran_id' => ['required', 'integer', Rule::exists('jenis_pelanggaran', 'id')],
            'tanggal_pelanggaran' => ['required', 'date', 'before_or_equal:today'],
            'catatan' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $studentViolation = $this->route('pelanggaranSiswa');
                $violationType = JenisPelanggaran::find($this->input('jenis_pelanggaran_id'));

                if ($violationType && ! $violationType->aktif && ! $this->isCurrentViolationType($studentViolation, $violationType)) {
                    $validator->errors()->add('jenis_pelanggaran_id', 'Jenis pelanggaran tidak aktif dan tidak dapat dipilih.');
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
            'profil_siswa_id.required' => 'Siswa wajib dipilih.',
            'profil_siswa_id.exists' => 'Siswa tidak valid.',
            'jenis_pelanggaran_id.required' => 'Jenis pelanggaran wajib dipilih.',
            'jenis_pelanggaran_id.exists' => 'Jenis pelanggaran tidak valid.',
            'tanggal_pelanggaran.required' => 'Tanggal pelanggaran wajib diisi.',
            'tanggal_pelanggaran.date' => 'Tanggal pelanggaran tidak valid.',
            'tanggal_pelanggaran.before_or_equal' => 'Tanggal pelanggaran tidak boleh melebihi hari ini.',
        ];
    }

    private function isCurrentViolationType(mixed $studentViolation, JenisPelanggaran $violationType): bool
    {
        return $studentViolation instanceof PelanggaranSiswa && (int) $studentViolation->jenis_pelanggaran_id === $violationType->id;
    }
}
