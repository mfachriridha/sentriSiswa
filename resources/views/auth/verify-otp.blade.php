@extends('layouts.guest')

@section('title', 'Verifikasi Kode OTP - Sentri Siswa')

@section('content')
<div class="flex items-center justify-center min-h-[80vh]">
    <div class="w-full max-w-md mx-auto">
        <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-100 p-8 sm:p-10 transition-all duration-300">
            <div class="mb-8 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-primary/10 shadow-md border border-primary/20">
                    <svg class="h-8 w-8 text-primary animate-pulse" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                    </svg>
                </div>
                <h2 class="mt-6 text-2xl font-extrabold text-slate-800 tracking-tight">Verifikasi OTP</h2>
                <p class="mt-2 text-xs font-semibold text-slate-500">
                    Kode verifikasi 6 digit telah dikirim ke<br>
                    <strong class="text-slate-800 text-sm tracking-wide">{{ $maskedEmail }}</strong>
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-100 bg-red-50 px-4 py-3.5">
                    <ul class="list-disc pl-4 text-xs font-semibold text-red-600 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-6 rounded-xl border border-green-100 bg-green-50 px-4 py-3.5 text-xs font-semibold text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('otp.verify') }}" class="space-y-6" id="otp-form">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-4 text-center">Masukkan 6 Digit OTP</label>
                    <div class="flex justify-between gap-2" id="otp-inputs">
                        @for ($i = 0; $i < 6; $i++)
                            <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]"
                                   class="otp-digit h-12 w-12 sm:h-14 sm:w-14 rounded-xl border-2 border-slate-200 bg-slate-50/50 text-slate-800 text-center text-xl font-extrabold shadow-inner transition-all duration-300 focus:bg-white focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/20"
                                   autocomplete="off">
                        @endfor
                    </div>
                    <input type="hidden" name="otp" id="otp-hidden">
                </div>

                <button type="submit"
                        class="w-full rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white shadow-lg shadow-primary/20 hover:bg-primary-dark active:scale-[0.98] transition-all duration-300 cursor-pointer">
                    Verifikasi Kode
                </button>
            </form>

            <div class="mt-6 text-center" x-data="otpResendTimer({{ $resendAvailableIn }})" x-init="start()">
                <form method="POST" action="{{ route('otp.kirim-ulang') }}">
                    @csrf
                    <button type="submit" :disabled="timeLeft > 0"
                            class="text-xs font-bold text-primary transition-colors hover:text-primary-dark hover:underline
                                   disabled:cursor-not-allowed disabled:text-slate-400 disabled:no-underline">
                        <span x-show="timeLeft > 0">
                            Kirim ulang kode dalam <span x-text="timeLeft"></span> detik
                        </span>
                        <span x-show="timeLeft === 0">Kirim Ulang Kode</span>
                    </button>
                </form>
            </div>

            <div class="mt-4 text-center">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-xs font-semibold text-slate-400 transition-colors hover:text-slate-600 hover:underline">
                        Batalkan &amp; Keluar
                    </button>
                </form>
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
            const val = e.target.value.replace(/[^0-9]/g, '');
            e.target.value = val;
            if (val && idx < digits.length - 1) {
                digits[idx + 1].focus();
            }
            syncHidden();
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !e.target.value && idx > 0) {
                digits[idx - 1].focus();
            }
        });

        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
            paste.split('').slice(0, 6).forEach((char, i) => {
                if (digits[i]) digits[i].value = char;
            });
            syncHidden();
            digits[Math.min(paste.length, 5)].focus();
        });
    });

    function syncHidden() {
        hiddenInput.value = Array.from(digits).map(d => d.value).join('');
    }

    form.addEventListener('submit', (e) => {
        syncHidden();
        if (hiddenInput.value.length !== 6) {
            e.preventDefault();
            digits[0].focus();
        }
    });

    // Sisa detik datang dari server, jadi tetap akurat walau halaman di-refresh.
    function otpResendTimer(initialSeconds) {
        return {
            timeLeft: initialSeconds,
            interval: null,
            start() {
                if (this.timeLeft <= 0) return;

                this.interval = setInterval(() => {
                    if (this.timeLeft > 0) this.timeLeft--;
                    else clearInterval(this.interval);
                }, 1000);
            },
        };
    }
</script>
@endpush
