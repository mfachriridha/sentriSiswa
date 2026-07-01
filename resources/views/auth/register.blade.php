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
                                      {{ old('peran') === 'teacher' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50/50 text-slate-400 hover:border-slate-300 hover:text-slate-500' }}">
                            <input type="radio" name="peran" value="teacher" {{ old('peran') === 'teacher' ? 'checked' : '' }} class="sr-only">
                            <svg class="h-6 w-6 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                            </svg>
                            <span class="text-xs font-bold">Guru</span>
                        </label>
                        <label class="flex-1 flex flex-col items-center justify-center gap-2.5 rounded-xl border-2 p-4 cursor-pointer transition-all duration-300 relative group
                                      {{ old('peran') !== 'teacher' ? 'border-primary bg-primary/5 text-primary' : 'border-slate-200 bg-slate-50/50 text-slate-400 hover:border-slate-300 hover:text-slate-500' }}">
                            <input type="radio" name="peran" value="student" {{ old('peran') !== 'teacher' ? 'checked' : '' }} class="sr-only">
                            <svg class="h-6 w-6 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <span class="text-xs font-bold">Siswa</span>
                        </label>
                    </div>
                    @error('peran')<p class="mt-1.5 text-xs text-red-500 font-semibold">{{ $message }}</p>@enderror
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

            @if ($adminWhatsAppUrl ?? null)
            <div class="mt-4 border-t border-slate-100 pt-4 text-center">
                <p class="text-xs text-slate-400 mb-2">Identitas tidak ditemukan? Minta admin tambahkan data Anda.</p>
                <a href="{{ $adminWhatsAppUrl }}" target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 rounded-xl border border-green-200 bg-green-50 px-4 py-2 text-xs font-bold text-green-700 hover:bg-green-100 transition-colors">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    Hubungi Admin via WhatsApp
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const roles = document.querySelectorAll('input[name="peran"]');
        const identityLabel = document.getElementById('identity-label');
        const identityInput = document.getElementById('identity');

        function updateLabels() {
            const activeRole = document.querySelector('input[name="peran"]:checked').value;
            const labels = document.querySelectorAll('input[name="peran"]');
            
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
