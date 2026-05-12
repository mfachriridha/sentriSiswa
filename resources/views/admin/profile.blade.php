@extends('layouts.app')

@section('title', 'Profile - Admin')

@section('page-title', 'Profile')

@section('content')
<div class="max-w-xl">
    <div class="card anim-up">
        <h3 class="font-bold text-slate-900 mb-5">Edit Profile</h3>
        <form action="{{ route('admin.profile.update') }}" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Nama</label>
                <input type="text" name="name" class="input-field" value="{{ auth()->user()->name }}" required>
            </div>
            <div>
                <label class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Email</label>
                <input type="email" name="email" class="input-field" value="{{ auth()->user()->email }}" required>
            </div>
            <hr class="border-slate-200">
            <p class="text-sm font-bold text-muted uppercase tracking-wider">Ganti Password (opsional)</p>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Password Baru</label><input type="password" name="password" class="input-field" placeholder="Minimal 6 karakter"></div>
                <div><label class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-1.5 block">Konfirmasi Password</label><input type="password" name="password_confirmation" class="input-field" placeholder="Ulangi password"></div>
            </div>
            <button type="submit" class="btn-brand"><i class="bi bi-check-circle"></i> Simpan Perubahan</button>
        </form>
    </div>
</div>
@endsection
