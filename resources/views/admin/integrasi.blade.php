@extends('layouts.app')

@section('title', 'Integrasi - Admin')

@section('page-title', 'Integrasi')

@section('content')
@if(session('success'))
<div class="mb-4 p-3 bg-emerald-50 border border-emerald-200 rounded-lg text-xs font-semibold text-emerald-700 flex items-center gap-2">
    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    <button onclick="this.parentElement.remove()" class="ml-auto text-emerald-400 hover:text-emerald-600"><i class="bi bi-x"></i></button>
</div>
@endif

<div class="card anim-up">
    <div class="flex items-center gap-3 mb-5">
        <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
            <i class="bi bi-whatsapp text-emerald-600 text-lg"></i>
        </div>
        <div>
            <h3 class="font-bold text-slate-900">WhatsApp API — wapisender.id</h3>
            <p class="text-xs text-muted">Konfigurasi API key untuk mengirim notifikasi WhatsApp</p>
        </div>
    </div>

    <form action="#" method="POST" onsubmit="event.preventDefault();alert('UI Only: API Key tersimpan!')" class="space-y-4 max-w-xl">
        @csrf
        <div>
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">API Key</label>
            <input type="text" class="input-field font-mono" placeholder="Masukkan API key dari wapisender.id" value="">
            <p class="text-[10px] text-muted mt-1">Dapatkan API key dari <a href="https://wapisender.id" target="_blank" class="text-[#0062a0] hover:underline">wapisender.id</a></p>
        </div>
        <div>
            <label class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Device ID</label>
            <input type="text" class="input-field font-mono" placeholder="Masukkan Device ID" value="">
        </div>
        <button type="submit" class="btn-brand"><i class="bi bi-check-circle"></i> Simpan</button>
    </form>
</div>
@endsection