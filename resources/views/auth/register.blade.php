@extends('layouts.guest')

@section('title', 'Daftar - Sentri Siswa')

@section('content')
<div class="flex items-center justify-center min-h-[80vh]">
    <div class="w-full max-w-md mx-auto">
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 p-8 sm:p-10 transition-all duration-300">
            <div class="mb-8 text-center">
                <img src="{{ asset('storage/assets/logo/logo-website.png') }}" alt="Sentri Siswa"
                     class="mx-auto h-20 w-auto rounded-2xl border-4 border-slate-100 shadow-md">
                <h2 class="mt-6 text-2xl font-extrabold tracking-tight text-slate-800">Daftar Akun</h2>
                <p class="mt-2 text-xs font-semibold text-slate-500">Verifikasi identitas Anda terlebih dahulu</p>
            </div>

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-100 bg-red-50 px-4 py-3.5 flex flex-col items-start gap-3">
                    <ul class="list-disc pl-4 text-xs font-semibold text-red-600 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    @if (session('admin_whatsapp_url'))
                        <a href="{{ session('admin_whatsapp_url') }}" target="_blank" rel="noopener"
                           class="inline-flex items-center justify-center rounded-xl bg-green-600 px-4 py-2 text-xs font-bold text-white hover:bg-green-700 transition-all cursor-pointer">
                            Hubungi Admin
                        </a>
                    @endif
                </div>
            @endif

            <form method="POST" action="{{ route('register.verify') }}" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Saya adalah</label>
                    <div class="flex gap-4">
                        <label class="flex-1 flex flex-col items-center justify-center gap-2.5 rounded-xl border-2 p-4 cursor-pointer transition-all duration-300 relative group
                                      {{ old('role') === 'teacher' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50/50 text-slate-400 hover:border-slate-300 hover:text-slate-500' }}">
                            <input type="radio" name="role" value="teacher" {{ old('role') === 'teacher' ? 'checked' : '' }} class="sr-only">
                            <svg class="h-6 w-6 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                            </svg>
                            <span class="text-xs font-bold">Guru</span>
                        </label>
                        <label class="flex-1 flex flex-col items-center justify-center gap-2.5 rounded-xl border-2 p-4 cursor-pointer transition-all duration-300 relative group
                                      {{ old('role') !== 'teacher' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50/50 text-slate-400 hover:border-slate-300 hover:text-slate-500' }}">
                            <input type="radio" name="role" value="student" {{ old('role') !== 'teacher' ? 'checked' : '' }} class="sr-only">
                            <svg class="h-6 w-6 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <span class="text-xs font-bold">Siswa</span>
                        </label>
                    </div>
                    @error('role')<p class="mt-1.5 text-xs text-red-500 font-semibold">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="identity" class="block text-xs font-bold text-slate-500 uppercase tracking-wider" id="identity-label">Nomor Identitas</label>
                    <input id="identity" type="text" name="identity" value="{{ old('identity') }}" required
                           inputmode="numeric" pattern="[0-9]*" autocomplete="off"
                           class="mt-2 block w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:bg-white focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/10 transition-all duration-300"
                           placeholder="Masukkan NIP atau NISN/NIS">
                    @error('identity')<p class="mt-1.5 text-xs text-red-500 font-semibold">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="w-full rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white shadow-lg shadow-primary/20 hover:bg-primary-dark active:scale-[0.98] transition-all duration-300 cursor-pointer">
                    Verifikasi Identitas
                </button>
            </form>

            <p class="mt-8 text-center text-xs font-semibold text-slate-400">
                Sudah punya akun? <a href="{{ route('login') }}" class="text-primary hover:text-primary-dark hover:underline transition-colors font-bold">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const roles = document.querySelectorAll('input[name="role"]');
        const identityLabel = document.getElementById('identity-label');
        const identityInput = document.getElementById('identity');

        function updateLabels() {
            const activeRole = document.querySelector('input[name="role"]:checked').value;
            const labels = document.querySelectorAll('input[name="role"]');
            
            labels.forEach(input => {
                const parent = input.closest('label');
                if (input.checked) {
                    parent.className = 'flex-1 flex flex-col items-center justify-center gap-2.5 rounded-xl border-2 p-4 cursor-pointer transition-all duration-300 relative group border-primary bg-primary/5 text-primary';
                } else {
                    parent.className = 'flex-1 flex flex-col items-center justify-center gap-2.5 rounded-xl border-2 p-4 cursor-pointer transition-all duration-300 relative group border-slate-200 bg-slate-50/50 text-slate-400 hover:border-slate-300 hover:text-slate-500';
                }
            });

            if (activeRole === 'teacher') {
                identityLabel.textContent = 'NIP (Nomor Induk Pegawai)';
                identityInput.placeholder = 'Masukkan 18 digit NIP Anda';
            } else {
                identityLabel.textContent = 'NISN / NIS';
                identityInput.placeholder = 'Masukkan NISN atau NIS Anda';
            }
        }

        roles.forEach(role => {
            role.addEventListener('change', updateLabels);
        });

        // Strip non-digits from input
        identityInput.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });

        updateLabels();
    });
</script>
@endpush
@endsection
