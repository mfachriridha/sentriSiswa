@extends('layouts.guest')

@section('title', 'Daftar - Sentri Siswa')

@section('content')
<div class="flex min-h-screen items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="mb-6 text-center">
                <img src="{{ asset('storage/assets/logo/logo-website.png') }}" alt="Sentri Siswa"
                     class="mx-auto h-24 w-auto rounded-xl border-4 border-white shadow-lg">
                <h2 class="mt-6 text-2xl font-bold text-gray-900">Daftar Akun</h2>
                <p class="mt-2 text-sm text-gray-500">Verifikasi identitas Anda untuk membuat akun</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3">
                    <ul class="list-disc pl-4 text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.verify') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Saya adalah</label>
                    <div class="flex gap-4">
                        <label class="flex-1 flex items-center justify-center gap-2 rounded-lg border-2 px-4 py-3 cursor-pointer transition-colors
                                      {{ old('role') === 'teacher' ? 'border-primary bg-primary/5 text-primary' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}">
                            <input type="radio" name="role" value="teacher" {{ old('role') === 'teacher' ? 'checked' : '' }} class="sr-only">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="text-sm font-medium">Guru</span>
                        </label>
                        <label class="flex-1 flex items-center justify-center gap-2 rounded-lg border-2 px-4 py-3 cursor-pointer transition-colors
                                      {{ old('role') === 'student' ? 'border-primary bg-primary/5 text-primary' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}">
                            <input type="radio" name="role" value="student" {{ old('role') === 'student' ? 'checked' : '' }} class="sr-only">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span class="text-sm font-medium">Siswa</span>
                        </label>
                    </div>
                    @error('role')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="identity" class="block text-sm font-medium text-gray-700" id="identity-label">NIP / NISN / NIS</label>
                    <input id="identity" type="text" name="identity" value="{{ old('identity') }}" required
                           class="mt-1 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900 shadow-sm
                                  placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20
                                  transition-colors"
                           placeholder="Masukkan NIP atau NISN/NIS">
                    @error('identity')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white shadow-sm
                               hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary/50
                               transition-colors">
                    Verifikasi
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500">
                Sudah punya akun? <a href="{{ route('login') }}" class="font-medium text-primary hover:underline">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const radios = document.querySelectorAll('input[name="role"]');
    const labels = document.querySelectorAll('label:has(input[name="role"])');
    const identityInput = document.getElementById('identity');
    const identityLabel = document.getElementById('identity-label');
    const identityPlaceholder = {
        teacher: { label: 'NIP', placeholder: 'Masukkan NIP Anda' },
        student: { label: 'NISN / NIS', placeholder: 'Masukkan NISN atau NIS Anda' },
    };

    function updateRoleUI() {
        const selected = document.querySelector('input[name="role"]:checked')?.value;
        labels.forEach((label, i) => {
            if (radios[i].value === selected) {
                label.classList.remove('border-gray-200', 'text-gray-600');
                label.classList.add('border-primary', 'bg-primary/5', 'text-primary');
            } else {
                label.classList.remove('border-primary', 'bg-primary/5', 'text-primary');
                label.classList.add('border-gray-200', 'text-gray-600');
            }
        });
        if (selected && identityPlaceholder[selected]) {
            identityLabel.textContent = identityPlaceholder[selected].label;
            identityInput.placeholder = identityPlaceholder[selected].placeholder;
        }
    }

    radios.forEach(r => r.addEventListener('change', updateRoleUI));
    updateRoleUI();
</script>
@endpush
@endsection