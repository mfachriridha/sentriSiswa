@extends('layouts.guest')

@section('title', 'Nomor WhatsApp - Sentri Siswa')

@section('content')
<div class="flex min-h-screen items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <div class="mb-6 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-green-50">
                    <svg class="h-7 w-7 text-green-600" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                </div>
                <h2 class="mt-4 text-xl font-bold text-gray-900">Tambah Nomor WhatsApp</h2>
                <p class="mt-1.5 text-sm text-gray-500 max-w-xs mx-auto">
                    Selamat, <strong>{{ $teacher->name }}</strong>! Satu langkah lagi sebelum masuk ke dashboard.
                </p>
            </div>

            <div class="mb-5 rounded-xl bg-blue-50 border border-blue-100 px-4 py-3">
                <p class="text-sm text-blue-700">
                    Nomor WhatsApp digunakan untuk notifikasi absensi dan informasi penting dari sekolah.
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

            <form method="POST" action="{{ route('google.whatsapp.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Nomor WhatsApp</label>
                    <div class="mt-1 flex">
                        <span class="inline-flex items-center rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 px-3 text-sm text-gray-500">
                            🇮🇩 +62
                        </span>
                        <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required autofocus
                               class="block w-full rounded-r-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900 shadow-sm
                                      placeholder:text-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-colors"
                               placeholder="08xx-xxxx-xxxx">
                    </div>
                    <p class="mt-1.5 text-xs text-gray-400">Format: 08xx, 628xx, atau +628xx</p>
                    @error('phone')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <button type="submit"
                        class="w-full rounded-xl bg-primary px-4 py-3 text-sm font-bold text-white shadow-sm transition-all hover:bg-primary-dark active:scale-95">
                    Simpan & Masuk ke Dashboard
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
