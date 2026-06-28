@extends('layouts.guest')

@section('title', 'Verifikasi Email Admin - Sentri Siswa')

@section('content')
<div class="flex min-h-screen items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <div class="mb-6 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-primary/10">
                    <svg class="h-7 w-7 text-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13.5a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2.69h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 10a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                </div>
                <h2 class="mt-4 text-xl font-bold text-gray-900">Verifikasi Email Admin</h2>
                <p class="mt-1.5 text-sm text-gray-500">
                    Kode verifikasi 6 digit telah dikirim ke<br>
                    <strong class="text-gray-700">{{ $maskedEmail }}</strong>
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3">
                    <ul class="list-disc pl-4 text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-5 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.setup.verify.store') }}" class="space-y-5" id="otp-form">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3 text-center">Masukkan Kode OTP</label>
                    <div class="flex justify-center gap-2" id="otp-inputs">
                        @for ($i = 0; $i < 6; $i++)
                            <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                                   class="otp-digit h-12 w-12 rounded-xl border-2 border-gray-200 text-center text-lg font-bold text-gray-900 shadow-sm transition-all focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                                   autocomplete="off">
                        @endfor
                    </div>
                    <input type="hidden" name="otp" id="otp-hidden">
                </div>

                <button type="submit"
                        class="w-full rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white shadow-sm transition-all hover:bg-primary-dark active:scale-95">
                    Verifikasi & Simpan
                </button>
            </form>

            <div class="mt-5 text-center" x-data="otpTimer()" x-init="start()">
                <p class="text-sm text-gray-500">
                    <span x-show="timeLeft > 0">
                        Kirim ulang dalam <span class="font-semibold text-primary" x-text="formatTime(timeLeft)"></span>
                    </span>
                    <span x-show="timeLeft === 0" x-cloak>
                        <form method="POST" action="{{ route('admin.setup.resend') }}" class="inline">
                            @csrf
                            <button type="submit" class="font-semibold text-primary hover:underline">
                                Kirim Ulang Kode
                            </button>
                        </form>
                    </span>
                </p>
            </div>

            <div class="mt-4 text-center">
                <a href="{{ route('admin.setup') }}" class="text-xs text-gray-400 hover:text-gray-600 transition-colors">
                    ← Ubah email/kata sandi
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const digits = document.querySelectorAll('.otp-digit');
    const hiddenInput = document.getElementById('otp-hidden');
    const form = document.getElementById('otp-form');

    digits.forEach((input, idx) => {
        input.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
            if (e.target.value && idx < digits.length - 1) digits[idx + 1].focus();
            syncHidden();
        });
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !e.target.value && idx > 0) digits[idx - 1].focus();
        });
        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
            paste.split('').slice(0, 6).forEach((char, i) => { if (digits[i]) digits[i].value = char; });
            syncHidden();
            digits[Math.min(paste.length, 5)].focus();
        });
    });

    function syncHidden() {
        hiddenInput.value = Array.from(digits).map(d => d.value).join('');
    }

    form.addEventListener('submit', (e) => {
        syncHidden();
        if (hiddenInput.value.length !== 6) { e.preventDefault(); digits[0].focus(); }
    });

    function otpTimer() {
        return {
            timeLeft: 600,
            interval: null,
            start() { this.interval = setInterval(() => { if (this.timeLeft > 0) this.timeLeft--; else clearInterval(this.interval); }, 1000); },
            formatTime(s) { return `${Math.floor(s / 60)}:${String(s % 60).padStart(2, '0')}`; }
        };
    }
</script>
@endpush
