@extends('layouts.guest')

@section('title', 'Nomor WhatsApp - Sentri Siswa')

@section('content')
<div class="flex items-center justify-center min-h-[80vh]">
    <div class="w-full max-w-md mx-auto">
        <div class="bg-slate-800/40 backdrop-blur-xl rounded-2xl shadow-2xl border border-slate-700/60 p-8 sm:p-10 transition-all duration-300">
            <div class="mb-8 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-green-500/10 shadow-lg border border-green-500/20">
                    <svg class="h-8 w-8 text-green-500" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                </div>
                <h2 class="mt-6 text-2xl font-extrabold text-white tracking-tight">Nomor WhatsApp</h2>
                <p class="mt-2 text-xs font-medium text-slate-400">
                    Satu langkah terakhir untuk akun Guru <strong>{{ $teacher->name }}</strong>
                </p>
            </div>

            <div class="mb-6 rounded-xl border border-blue-900/50 bg-blue-950/40 backdrop-blur p-4">
                <p class="text-xs font-semibold text-blue-400">
                    ℹ️ Nomor WhatsApp akan digunakan untuk mengirimkan rekap absensi, pengajuan pelanggaran, dan notifikasi penting dari sekolah.
                </p>
            </div>

            @if ($errors->any())
                <div class="mb-6 rounded-xl border border-red-900/50 bg-red-950/40 backdrop-blur px-4 py-3.5">
                    <ul class="list-disc pl-4 text-xs font-medium text-red-400 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('google.whatsapp.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="phone" class="block text-xs font-bold text-slate-300 uppercase tracking-wider">Nomor WhatsApp</label>
                    <div class="mt-2 flex shadow-inner rounded-xl overflow-hidden border border-slate-700 bg-slate-900/50 focus-within:border-primary focus-within:ring-4 focus-within:ring-primary/20 transition-all duration-300">
                        <span class="inline-flex items-center border-r border-slate-800 bg-slate-800 px-4 text-sm font-extrabold text-slate-400">
                            🇮🇩 +62
                        </span>
                        <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required autofocus
                               class="block w-full border-0 bg-transparent px-4 py-3 text-sm text-white placeholder:text-slate-500 focus:outline-none focus:ring-0"
                               placeholder="8xxxxxxxxxx">
                    </div>
                    <p class="mt-2 text-[10px] text-slate-500 font-medium">Contoh: 8123456789</p>
                </div>

                <button type="submit"
                        class="w-full rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white shadow-lg shadow-primary/20 hover:bg-primary-dark active:scale-[0.98] transition-all duration-300 cursor-pointer">
                    Simpan & Lanjutkan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
