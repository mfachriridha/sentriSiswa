<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    public function showForm()
    {
        return view('auth.register');
    }

    public function validateNip(Request $request)
    {
        $nip = preg_replace('/[^\d]/', '', (string) $request->input('nip'));

        if ($nip === '') {
            return response()->json([
                'valid' => false,
                'message' => 'NIP tidak boleh kosong.',
            ]);
        }

        $user = User::where('nip', $nip)->where('is_active', false)->first();

        if (! $user) {
            return response()->json([
                'valid' => false,
                'message' => 'NIP tidak ditemukan atau akun sudah diaktivasi.',
            ]);
        }

        return response()->json([
            'valid' => true,
            'nip' => $user->nip,
            'name' => $user->name,
        ]);
    }

    public function validateNis(Request $request)
    {
        $value = preg_replace('/[^\d]/', '', (string) $request->input('nis_or_nisn'));

        if ($value === '') {
            return response()->json([
                'valid' => false,
                'message' => 'NIS/NISN tidak boleh kosong.',
            ]);
        }

        $student = Student::where(function ($q) use ($value) {
            $q->where('nis', $value)->orWhere('nisn', $value);
        })
            ->whereNull('user_id')
            ->first();

        if (! $student) {
            return response()->json([
                'valid' => false,
                'message' => 'NIS/NISN tidak ditemukan atau akun sudah diaktivasi.',
            ]);
        }

        return response()->json([
            'valid' => true,
            'nis' => $student->nis,
            'nisn' => $student->nisn,
            'name' => $student->name,
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'role' => ['required', 'in:guru,siswa'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $password = Hash::make($data['password']);

        if ($data['role'] === 'guru') {
            $nip = preg_replace('/[^\d]/', '', (string) $request->input('nip'));

            if ($nip === '') {
                throw ValidationException::withMessages([
                    'nip' => 'NIP tidak valid.',
                ]);
            }

            $user = User::where('nip', $nip)->where('is_active', false)->first();

            if (! $user) {
                throw ValidationException::withMessages([
                    'nip' => 'NIP tidak valid atau akun sudah diaktivasi.',
                ]);
            }

            $user->update([
                'email' => $data['email'],
                'password' => $password,
                'is_active' => true,
            ]);

            return view('auth.register', ['registered' => true]);
        }

        $value = preg_replace('/[^\d]/', '', (string) $request->input('nis_or_nisn'));

        if ($value === '') {
            throw ValidationException::withMessages([
                'nis_or_nisn' => 'NIS/NISN tidak valid.',
            ]);
        }

        $student = Student::where(function ($q) use ($value) {
            $q->where('nis', $value)->orWhere('nisn', $value);
        })
            ->whereNull('user_id')
            ->first();

        if (! $student) {
            throw ValidationException::withMessages([
                'nis_or_nisn' => 'NIS/NISN tidak valid atau akun sudah diaktivasi.',
            ]);
        }

        $user = User::create([
            'name' => $student->name,
            'email' => $data['email'],
            'password' => $password,
            'role' => 'siswa',
            'is_active' => true,
        ]);

        $student->update(['user_id' => $user->id]);

        return view('auth.register', ['registered' => true]);
    }
}
