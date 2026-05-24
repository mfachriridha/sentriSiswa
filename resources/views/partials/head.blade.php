<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="preload" as="image" href="{{ asset('storage/assets/logo/logo-sidebar.png') }}">
    <title>@yield('title', 'Sentri Siswa')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
