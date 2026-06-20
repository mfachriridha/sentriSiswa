@extends('layouts.app')

@section('title', 'Profil Admin')

@section('content')
<div class="mb-5 flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Profil Admin</h1>
        <p class="mt-1 text-sm text-gray-500">Data akun admin yang dipakai untuk bantuan registrasi.</p>
    </div>
    <a href="{{ route('admin.profil.edit') }}"
       class="inline-flex items-center gap-2 rounded-lg border border-primary/30 px-4 py-2.5 text-sm font-medium text-primary hover:bg-primary/5">
        Edit Profil
    </a>
</div>

<x-alert type="success" :message="session('success')" />

<div class="rounded-xl border border-gray-200 bg-white p-6">
    <div class="mb-6 flex items-center gap-5">
        @if ($admin->photo)
            <img src="{{ asset('storage/'.$admin->photo) }}" alt="{{ $admin->name }}" class="h-20 w-20 rounded-full border-2 border-gray-200 object-cover">
        @else
            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-primary/10 text-2xl font-bold text-primary">
                {{ strtoupper(substr($admin->name, 0, 1)) }}
            </div>
        @endif
        <div>
            <h2 class="text-2xl font-bold text-gray-900">{{ $admin->name }}</h2>
            <p class="mt-1 text-sm text-gray-500">{{ $admin->roleLabel() }}</p>
        </div>
    </div>

    <div class="grid gap-5 md:grid-cols-2">
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Email</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $admin->email }}</p>
        </div>
        <div class="rounded-lg border border-gray-100 bg-gray-50 p-5">
            <p class="text-sm font-medium text-gray-500">Nomor WhatsApp Bantuan</p>
            <p class="mt-1.5 text-sm text-gray-900">{{ $admin->whatsapp_number ?? '-' }}</p>
        </div>
    </div>
</div>
@endsection
