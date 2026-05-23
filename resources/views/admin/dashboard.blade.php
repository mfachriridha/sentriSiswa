@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-900">Beranda</h1>
    <p class="mt-2 text-base text-gray-500">Selamat datang, {{ auth()->user()->name }}.</p>
</div>
@endsection
