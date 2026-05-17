<?php

namespace App\Livewire\Auth;

use App\Models\Guru;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class Register extends Component
{
    public string $role = 'siswa';

    public string $nip = '';

    public string $nis = '';

    public string $nama = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $no_hp = '';

    public bool $validated = false;

    public function setRole(string $role): void
    {
        $this->role = $role;
        $this->reset(['nip', 'nis', 'nama', 'email', 'password', 'password_confirmation', 'no_hp', 'validated']);
    }

    public function cekIdentitas(): void
    {
        if ($this->role === 'guru') {
            $this->validate(['nip' => 'required']);

            $normalizedNip = str_replace(' ', '', $this->nip);
            $guru = Guru::where('nip', $normalizedNip)->whereNull('user_id')->first();

            if (! $guru) {
                $this->addError('nip', 'NIP tidak ditemukan atau sudah terdaftar.');

                return;
            }

            $this->nama = $guru->nama;
            $this->validated = true;
        } else {
            $this->validate(['nis' => 'required']);

            $normalizedNis = str_replace(' ', '', $this->nis);
            $siswa = Siswa::where('nis', $normalizedNis)->whereNull('user_id')->first();

            if (! $siswa) {
                $this->addError('nis', 'NIS tidak ditemukan atau sudah terdaftar.');

                return;
            }

            $this->nama = $siswa->nama;
            $this->validated = true;
        }
    }

    public function register(): void
    {
        $rules = [
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];

        if ($this->role === 'guru') {
            $rules['no_hp'] = ['required', 'string', 'max:20'];
        }

        $this->validate($rules);

        $user = User::create([
            'name' => $this->nama,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->role,
        ]);

        if ($this->role === 'guru') {
            $normalizedNip = str_replace(' ', '', $this->nip);
            Guru::where('nip', $normalizedNip)->update([
                'user_id' => $user->id,
                'no_hp' => $this->no_hp,
            ]);
        } else {
            $normalizedNis = str_replace(' ', '', $this->nis);
            Siswa::where('nis', $normalizedNis)->update(['user_id' => $user->id]);
        }

        Auth::login($user);

        $this->redirect(session()->pull('url.intended', match ($this->role) {
            'guru' => route('guru.dashboard'),
            'siswa' => route('siswa.dashboard'),
            default => route('home'),
        }));
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
